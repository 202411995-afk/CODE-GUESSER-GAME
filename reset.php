<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "test";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 

$sql = "TRUNCATE TABLE game_scores";

if ($conn->query($sql) === TRUE) {
    echo "<h1>✅ Success! Leaderboard has been cleared.</h1>";
    echo "<p>You can now go back and play. No more duplicates will appear.</p>";
    echo "<a href='test1.php'>Go to Game</a>";
} else {
    echo "Error clearing table: " . $conn->error;
}

$conn->close();
?>