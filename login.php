<?php
ob_start();
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $conn = new mysqli('127.0.0.1', 'root', '', 'users');
    $conn->set_charset('utf8mb4');

    if ($conn->connect_error) {
        die('Database connection failed');
    }

    $login    = trim($_POST['login']); // username OR email
    $password = $_POST['password'];

    $stmt = $conn->prepare(
        "SELECT user_id, username, password
         FROM users
         WHERE email = ? OR username = ?
         LIMIT 1"
    );

    if (!$stmt) {
        die('Prepare failed');
    }

    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {

            $_SESSION['user_id'] = $row['user_id'];
            
            // --- FIX IS HERE: Changed 'name' to 'username' ---
            $_SESSION['username'] = $row['username']; 

            $stmt->close();
            $conn->close();

            header("Location: index.php");
            exit;
        }
    }

    $err = 'Invalid credentials';

    $stmt->close();
    $conn->close();
}

ob_end_flush();
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CODE GUESSER - Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        background: url('bg.png') no-repeat center center/cover;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(3px);
    }

    .login-container {
        background-color: #3b4252;
        border: 4px solid #4c566a;
        box-shadow: 4px 4px 0 0 #2e3440, -4px -4px 0 0 #2e3440;
        width: 400px;
        padding: 100px;
        animation: fadeIn 0.6s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    h1 {
        text-align: center;
        margin-bottom: 15px;
        color: #0066cc;
    }

    .login-container input {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 15px;
        transition: 0.3s;
    }

    .login-container input:focus {
        border-color: #007bff;
        transform: scale(1.03);
        outline: none;
    }

    button {
        width: 100%;
        background: #007bff;
        color: white;
        padding: 12px;
        border: none;
        font-size: 16px;
        border-radius: 8px;
        cursor: pointer;
        margin-top: 10px;
        transition: 0.3s;
    }

    button:hover {
        background: #0056b3;
        transform: scale(1.05);
    }

    .register-link {
        color: white;
        text-align: center;
        margin-top: 15px;
    }

    .register-link a {
        color: white;
        text-decoration: none;
        font-weight: bold;
    }

    .register-link a:hover {
        text-decoration: underline;
    }

    .error {
        color: red;
        text-align: center;
        margin-bottom: 10px;
        font-weight: bold;
    }
</style>
</head>

<body>

<div class="login-container">
    <h1>READY PLAYER 1</h1>

    <?php if (!empty($err)) echo '<div class="error">'.$err.'</div>'; ?>

    <form method="post" autocomplete="off">
        <input name="login" placeholder="Username or Email" required>
        <input name="password" type="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <div class="register-link">
        <p>New here? <a href="register.php">Create an account</a></p>
    </div>
</div>

</body>
</html>
