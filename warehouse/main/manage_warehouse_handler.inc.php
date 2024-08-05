<?php
// manage_warehouse_handler.inc.php
require_once "databasehandler.inc.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Example: Check if fields are empty
    if (empty($_POST['warehousename']) || empty($_POST['address']) || empty($_POST['phonenumber'])) {
        // Handle error: Redirect back with error message
        header("Location: ../managewarehouse/editwarehouse.php?error=emptyfields");
        exit();
    } else {
        // Proceed with processing form data
        $warehouseId = $_POST['warehouse_id'];
        $warehouseName = $_POST['warehousename'];
        $address = $_POST['address'];
        $phoneNumber = $_POST['phonenumber'];
        $status = $_POST['status'];
        $managerId = $_POST['warehousemanager'];

        // Validate and sanitize inputs if necessary

        // Example: Update database or perform other operations
        // Ensure you have a valid database connection and appropriate SQL queries
        // For simplicity, assume updating database here
        try {
            // Your database update logic here
            // Example: Update warehouses table
            $query = "UPDATE Warehouses SET Warehouse_Name = :name, Address = :address, wh_phone = :phone, Status = :status, wh_user_manager_id = :manager WHERE Warehouse_ID = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':name', $warehouseName);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':phone', $phoneNumber);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':manager', $managerId);
            $stmt->bindParam(':id', $warehouseId);
            $stmt->execute();

            // Redirect with success message
            header("Location: ../managewarehouse/editwarehouse.php?success=warehouseupdated");
            exit();
        } catch (PDOException $e) {
            // Handle database error
            // You might want to log the error or display a user-friendly message
            header("Location: ../managewarehouse/editwarehouse.php?error=dberror");
            exit();
        }
    }
} else {
    // Redirect if accessed directly without POST request
    header("Location: ../managewarehouse/editwarehouse.php");
    exit();
}
?>
