<?php
session_start();

// Include the database connection file
require_once 'db.php';

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in
    if (isset($_SESSION["username"])) {
        // Get the task post ID from the request
        $taskPostId = 1;

        // Get the current like status from the database
        $username = $_SESSION["username"];
        $sql = "SELECT * FROM Student WHERE studentUsername = '$username';";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $studentId = $row["studentId"];

            // Check if the student has already liked the post
            $checkSql = "SELECT * FROM StudentTaskLike WHERE studentId = '$studentId' AND taskPostId = '$taskPostId';";
            $checkResult = $conn->query($checkSql);

            if ($checkResult->num_rows > 0) {
                // Remove the like if already liked
                $deleteSql = "DELETE FROM StudentTaskLike WHERE studentId = '$studentId' AND taskPostId = '$taskPostId';";
                $deleteResult = $conn->query($deleteSql);

                if ($deleteResult === TRUE) {
                    // Like removed successfully
                    echo json_encode(array("success" => true, "message" => "Like removed."));
                    exit();
                } else {
                    // Failed to remove like
                    echo json_encode(array("success" => false, "message" => "Failed to remove like."));
                    exit();
                }
            } else {
                // Insert the like if not already liked
                $insertSql = "INSERT IGNORE INTO StudentTaskLike (studentId, taskPostId) VALUES ('$studentId', '$taskPostId');";
                $insertResult = $conn->query($insertSql);

                if ($insertResult === TRUE || $conn->affected_rows > 0) {
                    // Like inserted successfully or already exists
                    echo json_encode(array("success" => true, "message" => "Like added."));
                    exit();
                } else {
                    // Failed to insert like
                    echo json_encode(array("success" => false, "message" => "Failed to add like."));
                    exit();
                }
            }
        } else {
            // Student not found
            echo json_encode(array("success" => false, "message" => "Student not found."));
            exit();
        }
    } else {
        // User is not logged in
        echo json_encode(array("success" => false, "message" => "User is not logged in."));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Like Page</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="script.js" defer></script>
</head>
<body>
<div class="like-container">
    <h2>Like Page</h2>
    <form id="login-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <button id="like-button">Like</button>
    </form>
    <p id="message"></p>
</div>
</body>
</html>
