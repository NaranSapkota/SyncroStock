<?php
// fetch_warehouses.php
require_once "main/databasehandler.inc.php";

try {
    $query = "SELECT Warehouse_Name FROM Warehouses;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $warehouses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($warehouses);
} catch (PDOException $e) {
    echo json_encode([]);
}
?>
