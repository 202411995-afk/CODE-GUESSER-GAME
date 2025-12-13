<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "users";

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
$conn->query($sql);
$conn->select_db($dbname);

$tableSql = "CREATE TABLE IF NOT EXISTS game_scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    game_mode ENUM('single', 'multi') NOT NULL,
    correct_answers INT NOT NULL,
    time_taken INT NOT NULL,
    played_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($tableSql);

function getLeaderboard($conn, $mode) {
    $sql = "SELECT username, correct_answers, time_taken 
            FROM game_scores 
            WHERE game_mode = '$mode' 
            ORDER BY correct_answers DESC, time_taken ASC 
            LIMIT 10";     
    return $conn->query($sql);
}

$singlePlayerResult = getLeaderboard($conn, 'single');
$multiPlayerResult  = getLeaderboard($conn, 'multi');

function formatTime($seconds) {
    $m = floor($seconds / 60);
    $s = $seconds % 60;
    return sprintf("%02d:%02d", $m, $s);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leaderboards</title>
    <style>
        @font-face {
            font-family: 'Pixelify';
            src: local('Monospace'), local('Courier New');
        }

        body {
            font-family: 'Pixelify', monospace;
            background-color: #282c34;
            color: #d8dee9;
            margin: 0;
            padding: 20px;
            padding-top: 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
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
            box-sizing: border-box;
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
            font-family: 'Pixelify', monospace;
            transition: background-color 0.3s, color 0.3s;
        }

        .navbar-button:hover {
            background-color: #4c566a;
        }

        /*    LEADERBOARD   */
        h1 {
            color: #eceff4;
            text-shadow: 2px 2px #2e3440;
            margin-bottom: 30px;
            letter-spacing: 2px;
        }

        .container {
            display: flex;
            justify-content: center;
            gap: 30px;
            width: 90%;
            max-width: 1000px;
            flex-wrap: wrap;
        }

        .board-section {
            background-color: #3b4252;
            border: 4px solid #4c566a; 
            box-shadow: 4px 4px 0 0 #2e3440;
            padding: 20px;
            width: 45%;
            min-width: 350px;
            flex-grow: 1;
        }

        h2 {
            text-align: center;
            border-bottom: 2px solid #4c566a;
            padding-bottom: 15px;
            margin-top: 0;
            letter-spacing: 1px;
        }

        .single-header { color: #88c0d0;}
        .multi-header { color: #a3be8c; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            color: #d8dee9;
        }

        th {
            background-color: #434c5e;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #4c566a;
            color: #eceff4;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #4c566a;
        }

        tr:nth-child(1) td { color: #ebcb8b; /* Gold/Yellow */ font-weight: bold; }
        tr:nth-child(2) td { color: #e5e9f0; /* Silver/White */ font-weight: bold; }
        tr:nth-child(3) td { color: #d08770; /* Bronze/Orange */ font-weight: bold; }

        tr:hover {
            background-color: #434c5e;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="navbar-title">CODE GUESSER </div>
        <div class="navbar-actions">
            <a href="index.php" class="navbar-button">Main Menu</a> 
            <a href="logout.php" class="navbar-button">Logout</a>
        </div>
    </div>

    <h1>𓄴LEADERBOARD𓈃</h1>

    <div class="container">
        
        <div class="board-section">
            <h2 class="single-header">SINGLE PLAYER</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%">Rank</th>
                        <th>Player</th>
                        <th style="width: 20%">Score</th>
                        <th style="width: 20%">Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rank = 1;
                    if ($singlePlayerResult->num_rows > 0):
                        while($row = $singlePlayerResult->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $rank++; ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo $row['correct_answers']; ?></td>
                            <td><?php echo formatTime($row['time_taken']); ?></td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="4" style="text-align:center; color: #777;">No games played yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>



    </div>

</body>
</html>

<?php $conn->close(); ?>