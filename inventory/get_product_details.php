<?php
include '../inc/functions.php';
$conn = connect();

$productId = $_POST['id'];


$sql = "SELECT a.Product_ID, P.Product_Name, w.Warehouse_Name, a.Quantity
        FROM item_availabilities a 
        JOIN Warehouses w ON w.Warehouse_ID = a.Warehouse_ID
        JOIN Products P ON P.Product_ID = a.Product_ID
        WHERE a.Product_ID = '$productId'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
   
    echo "<strong>Product Details:</strong><br>";
    echo "<table border='1' style='width: 100%;'><tr><th>ID</th><th>Warehouse</th><th>Available</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
	echo "<td>" . $row['Product_ID'] . "</td>";
        echo "<td>" . $row['Warehouse_Name'] . "</td>";
        echo "<td>" . $row['Quantity'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No Details Found for these Products.";
}

$conn->close();
?>
