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
   
    return $conn;
}