<?php


// Database connection parameters
// $servername = "localhost";
// $username = "root";
// $password = "";
// $db_name = "uitilneuxt_syncro";

// // Create connection
// $conn = new mysqli($servername, $username, $password, $db_name);
include 'connection.php';
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare data from POST (sanitize or validate inputs as needed)
$company_name = $conn->real_escape_string($_POST['company_name']);
$contact_name = $conn->real_escape_string($_POST['contact_name']);
$contact_title = $conn->real_escape_string($_POST['contact_title']);
$address = $conn->real_escape_string($_POST['address']);
$city = $conn->real_escape_string($_POST['city']);
$province = $conn->real_escape_string($_POST['province']);
$postal_code = $conn->real_escape_string($_POST['postal_code']);
$country = $conn->real_escape_string($_POST['country']);
$phone = $conn->real_escape_string($_POST['phone']);
$email = $conn->real_escape_string($_POST['email']);

// SQL query to insert data into database
$sql = "INSERT INTO suppliers (Company_Name, Contact_Name, Contact_Title, Address, City, Province, Postal_Code, Country, Phone, Email) 
        VALUES ('$company_name', '$contact_name', '$contact_title', '$address', '$city', '$province', '$postal_code', '$country', '$phone', '$email')";

if ($conn->query($sql) === TRUE) {
  
    header("Location: ../suppliers.php"); // Example for going up one directory

    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$conn->close();
?>
