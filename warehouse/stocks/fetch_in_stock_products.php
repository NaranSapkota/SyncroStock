<?php
require_once "../main/databasehandler.inc.php";

$warehouseId = isset($_POST['warehouse_id']) ? intval($_POST['warehouse_id']) : 0;

if ($warehouseId > 0) {
    try {
        $query = "SELECT COUNT(*) AS in_stock_count 
                  FROM item_availabilities 
                  WHERE Warehouse_ID = :warehouse_id AND Quantity > 0;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':warehouse_id', $warehouseId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode(['in_stock_count' => $result['in_stock_count']]);
        } else {
            echo json_encode(['in_stock_count' => 0]);
        }
    } catch (PDOException $e) {
        echo json_encode(['in_stock_count' => 0]);
    }
} else {
    echo json_encode(['in_stock_count' => 0]);
}
?>
