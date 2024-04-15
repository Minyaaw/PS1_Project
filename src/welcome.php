<?php
session_start();

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// If the user is logged in, retrieve their username
$username = $_SESSION["username"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="welcome-container">
    <h2>Welcome, <?php echo $username; ?>!</h2>
    <p>You have successfully logged in.</p>
    <a href="logout.php">Logout</a> <!-- Update the link to logout.php -->
</div>
</body>
</html>
