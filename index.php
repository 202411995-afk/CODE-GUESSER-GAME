<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Menu</title>

    <style>
        body {
            margin: 0;
            font-family: 'Courier New', Courier, monospace;
            background-color: #2e3440;
            color: #e5e9f0;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            background-color: #3b4252;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
            z-index: 100;
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
        .main-content {
            flex-grow: 1;
            display: flex;
            overflow: hidden;
        }

        .game-link-wrapper {
            flex: 1; 
            text-decoration: none;
            color: inherit;
            position: relative;
            border-right: 4px solid #4c566a;
        }

        .game-button {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .game-button .overlay {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-image: url('idle.png'); 
            transition: transform 0.4s ease; 
        }

        .game-link-wrapper:hover .game-button .overlay {
            background-image: url('running.gif'); 
            transform: scale(1.05);
        }

        .section-text {
            z-index: 10;
            background-color: rgba(59, 66, 82, 0.9); 
            padding: 20px 40px;
            border: 2px solid #88c0d0;
            border-radius: 10px;
            font-size: 2.5em;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            text-align: center;
        }

        .info-panel {
            flex: 1;
            background-color: #2e3440;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 60px;
            box-sizing: border-box;
        }

        .info-panel h2 {
            color: #88c0d0; 
            font-size: 2em;
            margin-bottom: 20px;
            border-bottom: 2px solid #4c566a;
            padding-bottom: 10px;
            width: 100%;
        }

        .info-panel p {
            font-size: 1.2em;
            line-height: 1.6;
            color: #d8dee9;
            background-color: #3b4252;
            padding: 20px;
            border-radius: 8px;
            border-left: 5px solid #a3be8c; 
            box-shadow: 2px 2px 0 0 #4c566a;
        }

        @media (max-width: 768px) {
            .main-content {
                flex-direction: column;
            }
            .game-link-wrapper {
                height: 50vh;
                border-right: none;
                border-bottom: 4px solid #4c566a;
            }
            .info-panel {
                height: 50vh;
                padding: 30px;
            }
            .section-text {
                font-size: 1.5em;
            }
        }
        
    </style>
</head>

<body>

    <div class="navbar">
        <div class="navbar-title">CODE GUESSER GAME</div>

        <div class="navbar-actions">
            <a href="menu.php" class="navbar-button">Leaderboard</a>
            <a href="logout.php" class="navbar-button">Logout</a>
        </div>
    </div>

    <div class="main-content">
        
        <a href="test1.php" class="game-link-wrapper">
            <div class="game-button">
                <div class="overlay"></div>
                <span class="section-text">START GAME</span>
            </div>
        </a>

        <div class="info-panel">
            <h2>HOW TO PLAY</h2>
            <p>
                Welcome, Operator. Your mission is to restore the system kernel. 
                <br><br>
                Analyze the code snippets provided by the security mainframe. 
                Type the correct PHP syntax to repair the corrupted sectors.
                <br><br>
                <strong>Warning:</strong> Incorrect syntax will drain your system health. 
                Complete all levels to log your score on the Leaderboard.
            </p>
            <h2> OBJECTIVE</h2>
            <p>
                You must find complete the syntax to finish the game.
                <br><br>
                Each of which are harder than the last one.
                <br><br>
                You must try answer all of the questions before you health starts to slowly corrode
            </p>

        </div>

    </div>

</body>
</html>