
<?php

// Function to connect to the database
function connect() {
    $servername = "localhost";
    $username = "uitilneuxt_root";
    $password = "SyncroStock";
    $dbname = "uitilneuxt_Syncro";

    // Establish connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection errors
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}


?>


