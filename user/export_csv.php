<?php
// Start output buffering
ob_start();

include("dbConnection.php");
$conn = connect();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the user table
$sql = "SELECT user.user_id, user.FirstName, user.Lastname, user.Status, user.Useractivacion, user.Userdeactivacion, user.email, user.Cellphone, user.Address, user.username, roles.role_name, Warehouses.Warehouse_Name
        FROM user 
        JOIN user_roles ON user.user_id = user_roles.user_id
        JOIN roles ON user_roles.role_id = roles.role_id
        JOIN Warehouse_by_user ON user.user_id = Warehouse_by_user.user_id
        JOIN Warehouses ON Warehouse_by_user.Warehouse_ID = Warehouses.Warehouse_ID";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Clear the output buffer
    ob_clean();

    // Set headers to force download of CSV file
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=user_data.csv');

    // Open the output stream
    $output = fopen('php://output', 'w');

    // Output column headers
    fputcsv($output, array('User ID', 'First Name', 'Last Name', 'Status', 'Activation Date', 'Deactivation Date', 'Email', 'Contact Number', 'Address', 'Username', 'Role Name','Warehouse Name'));

    // Output rows
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    // Close the output stream
    fclose($output);
    
    // Flush the output buffer and end the script
    ob_flush();
    exit;
} else {
    echo "No records found.";
}

$conn->close();
?>
