<?php
// edit_warehouse_handler.inc.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs
    $warehouseId = $_POST["warehouse_id"];
    $warehousename = $_POST["warehousename"];
    $address = $_POST["address"];
    $city = $_POST["city"];
    $province = $_POST["province"];
    $postalcode = $_POST["postalcode"];
    $country = $_POST["country"];
    $phone = $_POST["phone"];
    $fax = $_POST["fax"];
    $status = $_POST["status"];
    $warehousemanager = $_POST["warehousemanager"]; // user_id of the selected manager

    if (empty($warehousename) || empty($address) || empty($city) || empty($province) || empty($postalcode) || empty($country) || empty($phone) || empty($warehousemanager) || empty($status)) {
        header("Location: ../managewarehouse/editwarehouse.php?id=$warehouseId&error=emptyfields");
        exit();
    }

    // Update warehouse in database
    require_once "databasehandler.inc.php";

    try {
        $query = "UPDATE Warehouses 
                  SET Warehouse_Name = :warehousename, Address = :address, City = :city, Province = :province, Postal_Code = :postalcode, Country = :country, Phone = :phone, Fax = :fax, Status = :status, wh_user_manager_id = :warehousemanager
                  WHERE Warehouse_ID = :warehouseId";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':warehousename', $warehousename);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':province', $province);
        $stmt->bindParam(':postalcode', $postalcode);
        $stmt->bindParam(':country', $country);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':fax', $fax);
        $stmt->bindParam(':warehousemanager', $warehousemanager);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':warehouseId', $warehouseId);
        $stmt->execute();

        header("Location: ../managewarehouse/managewarehouse.php?success=warehouseupdated");
        exit();
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
} else {
    header("Location: ../managewarehouse/managewarehouse.php");
    exit();
}
?>
