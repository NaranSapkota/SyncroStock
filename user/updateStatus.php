<?php
include('dbConnection.php');
$conn = connect();

if(isset($_POST['user_id']) && isset($_POST['status'])) {
    $user_id = $_POST['user_id'];
    $status = ($_POST['status'] == 'ON') ? 'ON' : 'OFF'; // Sanitize status

    $update_sql = "UPDATE user SET Status = '$status' WHERE user_id = '$user_id'";
    
    if(mysqli_query($conn, $update_sql)) {
        echo "Status updated successfully.";
    } else {
        echo "Error updating status: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
?>
