<?php
// fetch_manage_warehouses.php
require_once "../main/databasehandler.inc.php";

try {
    $query = "SELECT w.Warehouse_ID, w.Warehouse_Name, w.Address, w.City, w.Province, w.Postal_Code, w.Country, w.Phone, w.Fax, w.Status, u.FirstName, u.Lastname 
              FROM Warehouses AS w 
              LEFT JOIN user AS u ON w.wh_user_manager_id = u.user_id;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $warehouses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($warehouses);
} catch (PDOException $e) {
    // Handle database error - for simplicity, return an empty array in case of error
    echo json_encode([]);
}
?>
