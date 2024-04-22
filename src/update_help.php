<?php
require_once 'db.php'; // Connect to your database

// Check if the username is passed via POST
if (isset($_POST['username']) & isset($_POST['HelpId'])) {
    $username = $_POST['username'];
    $HelpId = $_POST['HelpId'];

    // Step 1: Get the studentId from the username
    $query = "SELECT studentId FROM Student WHERE studentUsername = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username); // Bind the username to the parameter
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // We found the student
        $row = $result->fetch_assoc();
        $studentId = $row['studentId'];

        // Step 2: Update the helpPostStudentResp with the retrieved studentId
        $updateQuery = "UPDATE HelpBoard SET helpPostStudentResp = ? WHERE helpPostId = ?"; // You need the HelpPostId from your context
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ii", $studentId, $HelpId); // Bind the studentId and helpPostId
        $updateStmt->execute();

        if ($updateStmt->affected_rows > 0) {
            echo json_encode(["status" => "success", "message" => "Updated successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Update failed"]);
        }

        $updateStmt->close();
    } else {
        // Student not found
        echo json_encode(["status" => "error", "message" => "Student not found"]);
    }

    $stmt->close();
} else {
    // No username provided
    echo json_encode(["status" => "error", "message" => "No username provided"]);
}

$conn->close();
?>
