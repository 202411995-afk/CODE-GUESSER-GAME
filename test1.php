<?php
session_start();
if(!isset($_SESSION['user_id'])){ header('Location: login.php'); exit; }

function save_game_result($final_score) {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "users";

    if (!isset($_SESSION['username'])) return;

    $conn = new mysqli($host, $user, $pass, $dbname);
    if ($conn->connect_error) {
        die("DB Error: " . $conn->connect_error);
    }

    $username = $_SESSION['username'];
    $mode = 'single';

    $start_time = $_SESSION['start_time'] ?? time();
    $time_taken = time() - $start_time;

    $check = $conn->prepare(
        "SELECT correct_answers, time_taken
         FROM game_scores
         WHERE username = ? AND game_mode = ?"
    );
    $check->bind_param("ss", $username, $mode);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (
            $final_score > $row['correct_answers'] ||
            ($final_score == $row['correct_answers'] && $time_taken < $row['time_taken'])
        ) {
            $update = $conn->prepare(
                "UPDATE game_scores
                 SET correct_answers = ?, time_taken = ?
                 WHERE username = ? AND game_mode = ?"
            );
            $update->bind_param("iiss", $final_score, $time_taken, $username, $mode);
            $update->execute();
            $update->close();
        }
    } else {
        $insert = $conn->prepare(
            "INSERT INTO game_scores
            (username, game_mode, correct_answers, time_taken)
            VALUES (?, ?, ?, ?)"
        );
        $insert->bind_param("ssii", $username, $mode, $final_score, $time_taken);
        $insert->execute();
        $insert->close();
    }

    $check->close();
    $conn->close();
}


function getData($url){
    $res = httpGet($url);
    $arr = json_decode($res,true);
    return is_array($arr) ? $arr : [];
}

$MAX_HEALTH = 100;

$TASK_LEVELS = [
    1 => [
        'title' => 'Variable Increment',
        'prompt' => 'Increment the player\'s X position by the player speed variable.',
        'hint' => 'Use the shorthand operator (e.g., `x += y;`) or full form.',
        'correct_answer' => ['player_x += player_speed;', 'player_x = player_x + player_speed;', 'debug_x_X_x_Admin_Only'],
    ],
    2 => [
        'title' => 'Conditional Check',
        'prompt' => 'Check if the player\'s X position is greater than the wall\'s X position.',
        'hint' => 'Start with `if` and use the `>` operator.',
        'correct_answer' => ['if (player_x > wall_x) {', 'if(player_x > wall_x){', 'debug_x_X_x_Admin_Only'],
    ],
    3 => [
        'title' => 'String Concatenation',
        'prompt' => 'Define a variable $message with the current X position and the string " units."',
        'hint' => 'Use the PHP concatenation operator (`.`).',
        'correct_answer' => ['$message = player_x . " units";', '$message = $player_x . " units";', 'debug_x_X_x_Admin_Only'],
    ],
    4 => [
        'title' => 'Output Score',
        'prompt' => 'Display the variable $score to the screen.',
        'hint' => 'Use the `echo` command.',
        'correct_answer' => ['echo $score;', 'print $score;', 'debug_x_X_x_Admin_Only'],
    ],
    5 => [
        'title' => 'Set Variable',
        'prompt' => 'Set the variable $score to 0.',
        'hint' => 'Use the equals sign (=).',
        'correct_answer' => ['$score = 0;', 'debug_x_X_x_Admin_Only'],
    ],
    6 => [
        'title' => 'Decrease Health',
        'prompt' => 'Decrease the variable $hp by the variable $damage.',
        'hint' => 'Use the shorthand subtraction operator `-=`',
        'correct_answer' => ['$hp -= $damage;', '$hp = $hp - $damage;', 'debug_x_X_x_Admin_Only'],
    ],
    7 => [
        'title' => 'Logical AND',
        'prompt' => 'Write an if statement checking if $alive is true AND $ammo is greater than 0.',
        'hint' => 'Use the `&&` operator.',
        'correct_answer' => ['if ($alive && $ammo > 0) {', 'if($alive && $ammo > 0){', 'debug_x_X_x_Admin_Only'],
    ],
    8 => [
        'title' => 'Function Definition',
        'prompt' => 'Define a new function named "jump".',
        'hint' => 'Start with the keyword `function`.',
        'correct_answer' => ['function jump() {', 'function jump(){', 'debug_x_X_x_Admin_Only'],
    ],
    9 => [
        'title' => 'Random Number',
        'prompt' => 'Generate a random number between 1 and 10.',
        'hint' => 'Use the `rand()` function.',
        'correct_answer' => ['rand(1, 10);', 'rand(1,10);', 'debug_x_X_x_Admin_Only'],
    ],
    10 => [
        'title' => 'Count Array',
        'prompt' => 'Count the number of elements in the array $enemies.',
        'hint' => 'Use the `count()` function.',
        'correct_answer' => ['count($enemies);', '$count = count($enemies);', 'debug_x_X_x_Admin_Only'],
    ]
];

function initialize_game() {
    global $MAX_HEALTH, $TASK_LEVELS;
    $_SESSION['health'] = $MAX_HEALTH;
    $_SESSION['level'] = 1;
    
    $_SESSION['start_time'] = time(); 

    $task_ids = array_keys($TASK_LEVELS);
    shuffle($task_ids);

    $_SESSION['task_mapping'] = [];
    $display_level = 1;
    foreach ($task_ids as $id) {
        $_SESSION['task_mapping'][$display_level] = $id;
        $display_level++;
    }

    $_SESSION['game_state'] = 'playing';
    $_SESSION['message'] = 'Guess the Code - Level 1';
    $_SESSION['speed'] = rand(5, 15);
}

if (isset($_POST['restart']) || !isset($_SESSION['health']) || !isset($_SESSION['task_mapping'])) {
    initialize_game();
}

if (isset($_POST['timeout'])) {
    if($_SESSION['game_state'] == 'playing') { 
        $final_score = max(0, $_SESSION['level'] - 1);
        save_game_result($final_score); 
    }

    $_SESSION['health'] = 0;
    $_SESSION['game_state'] = 'lose';
    $_SESSION['message'] = "TIME'S UP! Process terminated.";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$health = $_SESSION['health'];
$level = $_SESSION['level'];
$game_state = $_SESSION['game_state'];
$message = $_SESSION['message'];
$speed = $_SESSION['speed'];
$task_mapping = $_SESSION['task_mapping'];

$real_task_id = $task_mapping[$level] ?? null;
$current_task = $TASK_LEVELS[$real_task_id] ?? null;

if ($game_state === 'playing' && isset($_POST['guess']) && $current_task) {
    $user_guess = trim($_POST['guess']);
    $clean_guess = strtolower(str_replace([' ', ';', '$', '"', "'"], '', $user_guess)); 

    $is_correct = false;
    foreach ($current_task['correct_answer'] as $answer) {
        $clean_answer = strtolower(str_replace([' ', ';', '$', '"', "'"], '', $answer));
        if ($clean_guess === $clean_answer) {
            $is_correct = true;
            break;
        }
    }

    if ($is_correct) {
        $level++;
        $_SESSION['level'] = $level;
        $_SESSION['health'] = $MAX_HEALTH;
        $_SESSION['show_door'] = true;

        if ($level > count($TASK_LEVELS)) {
            $_SESSION['game_state'] = 'win';
            $_SESSION['message'] = "SYSTEM RESTORED (VICTORY)";
            
            save_game_result(count($TASK_LEVELS)); 
            
        } else {
            $_SESSION['message'] = "CORRECT! Opening Security Door...";
        }
    } else {
        $_SESSION['health'] -= 15;
        $message = "SYNTAX ERROR! Health deducted.";
        $_SESSION['show_fail'] = true;

        if ($_SESSION['health'] <= 0) {
            $_SESSION['health'] = 0;
            $_SESSION['game_state'] = 'lose';
            $_SESSION['message'] = "FATAL ERROR (GAME OVER)";
            
            save_game_result(max(0, $level - 1));

        } else {
            $_SESSION['message'] = $message;
        }
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$image_src = 'running.gif';

if ($game_state === 'lose') {
    $image_src = 'failed.gif';
} elseif ($game_state === 'win') {
    $image_src = 'idle.gif';
} elseif (isset($_SESSION['show_door']) && $_SESSION['show_door'] === true) {
    $image_src = 'opening door.gif';
    unset($_SESSION['show_door']);
} elseif (isset($_SESSION['show_fail']) && $_SESSION['show_fail'] === true) {
    $image_src = 'failed.gif';
    unset($_SESSION['show_fail']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Code Guesser (PHP Version)</title>
<style>
        @font-face {
            font-family: 'Pixelify';
            src: local('Monospace'), local('Courier New');
        }

        body {
            font-family: 'Pixelify', monospace;
            background-color: #282c34;
            background-size: cover;
            color: #d8dee9;
            display: flex;
            flex-direction: column; 
            align-items: center;
            margin: 0;
            padding: 20px;
            padding-top: 70px; 
        }

        .container {
            background-color: #3b4252;
            border: 4px solid #4c566a;
            box-shadow: 4px 4px 0 0 #2e3440, -4px -4px 0 0 #2e3440;
            padding: 24px;
            width: 90%;
            max-width: 800px;
            text-align: center;
        }

        .visual-area {
            width: 100%;
            height: 200px; 
            background-color: #2e3440;
            border: 2px solid #4c566a;
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .visual-area img {
            max-height: 100%;
            max-width: 100%;
            image-rendering: pixelated; 
        }

        .health-container {
            margin: 20px 0;
            text-align: left;
            background-color: #2e3440; 
            padding: 15px;
            border: 2px solid #4c566a;
        }

        .health-bar-bg {
            background-color: #4c566a;
            border: 2px solid #2e3440;
            height: 50px;
            overflow: hidden;
            box-shadow: inset 1px 1px #2e3440;
            margin-top: 10px;
        }

        #health-bar-fill {
            height: 100%;
            width: <?php echo $health; ?>%;
            background-color: <?php echo ($health < 30) ? '#ebcb8b' : '#bf616a'; ?>;
            transition: width 0.2s linear;
        }

        input[type="text"] {
            font-family: 'Pixelify', monospace;
            background-color: #eceff4;
            color: #2e3440;
            border: 2px solid #4c566a;
            padding: 8px;
            font-size: 1.1em;
            width: 80%;
            max-width: 400px;
            margin-top: 10px;
        }

        .btn {
            font-family: 'Pixelify', monospace;
            background-color: #5e81ac;
            color: #eceff4;
            border: 3px solid #3b4252;
            padding: 10px 20px;
            margin: 5px;
            font-size: 1em;
            cursor: pointer;
            box-shadow: 2px 2px 0 0 #2e3440;
            transition: background-color 0.1s;
        }

        .btn:hover { background-color: #81a1c1; }

        .message {
            background-color: #4c566a;
            padding: 10px;
            margin-top: 15px;
            margin-bottom: 20px;
            border: 2px solid #2e3440;
            font-size: 1.5em;
            color: #88c0d0;
        }

        .status-info {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
            font-size: 0.9em;
            color: #88c0d0;
        }

        .navbar {
            background-color: #3b4252;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            z-index: 1000;
        }

        .navbar-title {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #e5e9f0;
        }

        .navbar-actions {
            display: flex;
        }

        .navbar-button {
            background-color: transparent;
            color: #e5e9f0;
            border: 1px solid #e5e9f0;
            padding: 8px 15px;
            margin-left: 10px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s;
        }

        .navbar-button:hover {
            background-color: #4c566a;
            border-color: #4c566a;
        }
</style>
</head>
<body>

<div class="navbar">
    <div class="navbar-title">CODE GUESSER GAME</div>
        <br><br><br>
    <div class="navbar-actions">
        <a href="index.php" class="navbar-button">Main Menu</a>
        <a href="logout.php" class="navbar-button">Logout</a>
    </div>
</div>
<br><br><br>
<div class="container">
    <div class="visual-area">
        <img id="gameImage" src="<?php echo $image_src; ?>" alt="Game State">
    </div>

    <div class="message">
        <?php echo $message; ?>
    </div>

    <?php if ($game_state === 'playing' && $current_task): ?>
        <div id="gameplay-ui">
            <h2><?php echo $current_task['prompt']; ?></h2>
            <p style="color: #a3be8c;">Hint: <?php echo $current_task['hint']; ?></p>

            <form method="POST" id="gameForm">
                <input type="text" name="guess" placeholder="Type your code here..." autocomplete="off" autofocus>
                <button type="submit" class="btn">SUBMIT CODE</button>
            </form>
        </div>
    <?php elseif ($game_state === 'win'): ?>
        <div id="endgame-ui"><h1 style="color: #a3be8c;">MISSION COMPLETE</h1></div>
    <?php elseif ($game_state === 'lose'): ?>
        <div id="endgame-ui"><h1 style="color: #bf616a;">GAME OVER</h1></div>
    <?php endif; ?>

    <div class="health-container">
        <div class="status-info">
            <span>Health: <span id="health-text"><?php echo ceil($health); ?></span>%</span>
            <span>Level <?php echo $level; ?></span>
        </div>
        <div class="health-bar-bg">
            <div id="health-bar-fill"></div>
        </div>
    </div>

    <div class="status-info" style="margin-top: 15px;">
        <form method="POST" style="display:inline;">
            <input type="hidden" name="restart" value="true">
            <button type="submit" class="btn">RESTART GAME</button>
        </form>
    </div>
</div>

<?php if ($game_state === 'playing'): ?>
<script>
(function() {
    var img = document.getElementById('gameImage');
    if (img) {
        if (img.src.indexOf('opening') > -1 && img.src.indexOf('door') > -1) {
            setTimeout(function() { img.src = 'running.gif'; }, 2500);
        } else if (img.src.indexOf('failed') > -1) {
            setTimeout(function() { img.src = 'running.gif'; }, 1500);
        }
    }

    var currentHealth = <?php echo $health; ?>;
    var healthBar = document.getElementById('health-bar-fill');
    var healthText = document.getElementById('health-text');
    var decayRate = 0.05;
    var intervalTime = 100;

    var timer = setInterval(function() {
        if (currentHealth <= 0) {
            clearInterval(timer);
            triggerTimeout();
            return;
        }
        currentHealth -= decayRate;
        healthBar.style.width = currentHealth + '%';
        healthText.innerText = Math.ceil(currentHealth);
        if (currentHealth < 40) healthBar.style.backgroundColor = '#ebcb8b';
    }, intervalTime);

    function triggerTimeout() {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo $_SERVER['PHP_SELF']; ?>';
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'timeout';
        input.value = 'true';
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }

    var gameForm = document.getElementById('gameForm');
    if (gameForm) {
        gameForm.addEventListener('submit', function() { clearInterval(timer); });
    }
})();
</script>
<?php endif; ?>

</body>
</html>