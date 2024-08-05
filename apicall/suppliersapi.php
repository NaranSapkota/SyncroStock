<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection
// $servername = "localhost";
// $username = "root";
// $password = "";
// $db_name = "uitilneuxt_syncro";

// $conn = new mysqli($servername, $username, $password, $db_name);
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }
include 'connection.php';

// Query to fetch suppliers
$sql = "SELECT * FROM Suppliers";
$result = $conn->query($sql);

// Check if results exist and fetch data
$suppliers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $suppliers[] = [
            'Supplier_ID' => $row['Supplier_ID'],
            'Company_Name' => $row['Company_Name'],
            'Contact_Name' => $row['Contact_Name'],
            'Contact_Title' => $row['Contact_Title'],
            'Address' => $row['Address'],
            'City' => $row['City'],
            'Province' => $row['Province'],
            'Postal_Code' => $row['Postal_Code'],
            'Country' => $row['Country'],
            'Phone' => $row['Phone'],
            'Email' => $row['Email']
        ];
    }
}

// Close connection
$conn->close();

// Return the suppliers array for inclusion in the HTML section
return $suppliers;
?>
