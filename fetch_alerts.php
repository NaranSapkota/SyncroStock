<?php
include 'connection.php';

function checkStockLevels($conn) {
    $productAlerts = [];

    // Updated table and column names
    $sql = "SELECT Product_ID, Product_Name, Minimum_Level FROM Products";
    $result = $conn->query($sql);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    while ($row = $result->fetch_assoc()) {
        if ($row['Minimum_Level'] == 1) {
            $productAlerts[] = "Product '{$row['Product_Name']}' is low on stock.";
        }
    }

    return $productAlerts;
}

function checkOrderAlerts($conn) {
    $orderAlerts = [];

    // Updated table and column names
    $sql = "SELECT Order_ID, Order_Date, Order_Status FROM orders WHERE Order_Status IN ('Available', 'Delivered', 'Cancelled')";
    $result = $conn->query($sql);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    $currentDate = new DateTime();
    while ($row = $result->fetch_assoc()) {
        $orderDate = new DateTime($row['Order_Date']);
        $interval = $currentDate->diff($orderDate)->days;

        if ($interval > 5 && $row['Order_Status'] === 'Available') {
            $orderAlerts[] = "Order '{$row['Order_ID']}' is {$row['Order_Status']} and has been open for more than 5 days.";
        }
    }

    return $orderAlerts;
}

$productAlerts = checkStockLevels($conn);
$orderAlerts = checkOrderAlerts($conn);

$response = [
    'productAlerts' => $productAlerts,
    'orderAlerts' => $orderAlerts
];

echo json_encode($response);
?>
