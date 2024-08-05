<?php
require('../pdf/fpdf.php');

// Database connection
include("dbConnection.php");
$conn = connect();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the user table
$sql = "SELECT user.*, roles.role_name 
        FROM user 
        JOIN user_roles ON user.user_id = user_roles.user_id
        JOIN roles ON user_roles.role_id = roles.role_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);

    // Column headers
    $pdf->Cell(40, 10, 'User ID', 1);
    $pdf->Cell(40, 10, 'Username', 1);
    $pdf->Cell(60, 10, 'Email', 1);
    $pdf->Cell(40, 10, 'Role Name', 1);
    $pdf->Ln();

    // Data
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(40, 10, $row['user_id'], 1);
        $pdf->Cell(40, 10, $row['username'], 1);
        $pdf->Cell(60, 10, $row['email'], 1);
        $pdf->Cell(40, 10, $row['role_name'], 1);
        $pdf->Ln();
    }

    $pdf->Output('D', 'user_data.pdf'); // Download the file
} else {
    echo "No records found.";
}
$conn->close();
?>
