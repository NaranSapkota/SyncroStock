<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('dbConnection.php');
$conn = connect();

if (isset($_POST['updateUser'])) {
    $user_id = $_SESSION['user_id'];  // Fetch user_id from the session

    // Fetch the user details
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $contactNumber = $_POST['contactNumber'];
    $activationDate = $_POST['activationDate'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $userName = $_POST['userName'];
    $role_id = $_POST['role_id'];
    $status = isset($_POST['status']) ? 'ON' : 'OFF';

    // Update the user
    $sql2 = "UPDATE user SET 
        FirstName = '$firstName',
        Lastname = '$lastName',
        Cellphone = '$contactNumber',
        Useractivacion = '$activationDate',
        Address = '$address',
        email = '$email',
        username = '$userName',
        Status = '$status'
        WHERE user_id = '$user_id'"; 

    $result = mysqli_query($conn, $sql2);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    // Update the user's role in the user_roles table
    $sql3 = "UPDATE user_roles SET role_id = '$role_id' WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $sql3);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    } else {
        header('Location: profile.php?update_msg=You have updated your profile successfully!');
        exit();
    }

    // Close connection
    mysqli_close($conn);
}
?>
