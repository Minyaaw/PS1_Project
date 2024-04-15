<?php
session_start();

// Include the database connection file
require_once 'db.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Escape user input to prevent SQL injection
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    // Hash the password before comparing with the database
    $hashedPassword = hash('sha256', $password); // You should use a secure hashing algorithm like bcrypt

    // Query the database to check if the username and password match
    $sql = "SELECT * FROM Student WHERE studentUsername = '$username' AND studentPassword = '$hashedPassword';";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Username and password match
        // Set session variables
        $_SESSION["username"] = $username;

        // Redirect to the home page or any other page
        header("Location: welcome.php");
        exit();
    } else {
        // Username and password do not match
        // Handle invalid login
        echo "Invalid username or password.";
    }
}

// Close connection (not necessary as connection will be closed automatically at the end of the script execution)
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-container">
    <h2>Login</h2>
    <form id="login-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="text" id="username" name="username" placeholder="Username" required>
        <input type="password" id="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <p id="error-message" class="error"></p>
</div>
</body>
</html>
