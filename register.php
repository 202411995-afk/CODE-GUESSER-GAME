<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $conn = new mysqli('127.0.0.1', 'root', '', 'users');
    $conn->set_charset('utf8mb4');

    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $contact  = trim($_POST['contact']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare(
        "INSERT INTO users (username, email, password, contact)
         VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param("ssss", $username, $email, $password, $contact);
    $stmt->execute();

    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>REGISTER</title>
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

    .register-container {
        background-color: #3b4252;
        border: 4px solid #4c566a;
        box-shadow: 4px 4px 0 0 #2e3440, -4px -4px 0 0 #2e3440;
        width: 520px;
        padding: 40px 35px;
        box-shadow: 0px 4px 25px rgba(0,0,0,0.35);
        animation: fadeIn 0.6s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #005bb5;
        font-size: 26px;
        letter-spacing: 1px;
    }

    .register-container input {
        width: 97%;
        display:flex;
        padding: 8px;
        border-radius: 5px;
        margin: 12px 0;
        border: 1px solid #ccc;
        font-size: 16px;
        transition: 0.3s;
    }

    .register-container input:focus {
        border-color: #007bff;
        transform: scale(1.03);
        outline: none;
    }

    button {
        width: 100%;
        background: #007bff;
        color: white;
        padding: 14px;
        border: none;
        font-size: 17px;
        border-radius: 10px;
        cursor: pointer;
        margin-top: 10px;
        transition: 0.3s;
        font-weight: bold;
    }

    button:hover {
        background: #0056b3;
        transform: scale(1.05);
    }

    .login-link {
        color:white;
        text-align: center;
        margin-top: 18px;
        font-size: 15px;
    }

    .login-link a {
        color: #007bff;
        font-weight: bold;
        text-decoration: none;
    }

    .login-link a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<div class="register-container">
    <h2>Create Your Account</h2>

    <form method="post">
        <input name="username" required placeholder="Username">
        <input name="email" required placeholder="Email Address">
        <input name="password" type="password" required placeholder="Password">
        <input name="contact" placeholder="Contact Number">
        <button>Register</button>
    </form>

    <div class="login-link">
        <p>Already have an account? <a href="login.php">Log in here</a></p>
    </div>
</div>

</body>
</html>
