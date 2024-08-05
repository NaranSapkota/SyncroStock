<?php
//if (isset($_POST['submit'])) 
session_start();
include("dbConnection.php");
$conn = connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the submitted form data
    $userID = $_SESSION['UserID'];
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Check if new password and confirm password match
    if ($newPassword !== $confirmPassword) {
        die('New password and confirm password do not match.');
    }

    // Retrieve the current hashed password from the database
    $sql = "SELECT password FROM user WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();

    // Debugging: Print user ID and hashed password
    error_log("UserID: $userID");
    error_log("Hashed Password: $hashedPassword");

    if ($stmt->num_rows === 1) {
        // Verify the current password
        if (password_verify($currentPassword, $hashedPassword)) {
            // Hash the new password
            $newHashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Update the password in the database
            $updateSql = "UPDATE user SET password = ? WHERE user_id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("si", $newHashedPassword, $userID);
            if ($updateStmt->execute()) {
                echo "Password successfully changed.";
            } else {
                echo "Error updating password: " . $conn->error;
            }
            $updateStmt->close();
        } else {
            echo "Current password is incorrect.";
            error_log("Current password is incorrect.");
        }
    } else {
        echo "User not found.";
        error_log("User not found.");
    }

    $stmt->close();
    $conn->close();
}
?>
