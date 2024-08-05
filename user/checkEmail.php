<?php
include('dbConnection.php');
$conn = connect();

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $check_email_query = "SELECT * FROM user WHERE email = '$email'";
    $check_email_result = mysqli_query($conn, $check_email_query);

    if (mysqli_num_rows($check_email_result) > 0) {
        echo 'exists';
    } else {
        echo 'not_exists';
    }

    mysqli_close($conn);
}
?>

