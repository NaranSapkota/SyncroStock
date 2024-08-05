<?php
include('dbConnection.php');
$conn = connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);

    $sql = "SELECT COUNT(*) as count FROM user WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die(mysqli_error($conn));
    }

    $row = mysqli_fetch_assoc($result);
    $count = $row['count'];

    if ($count > 0) {
        echo 'exists';
    } else {
        echo 'not exists';
    }
}

mysqli_close($conn);
?>
