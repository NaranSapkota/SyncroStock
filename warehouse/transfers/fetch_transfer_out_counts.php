<?php
require_once "../main/databasehandler.inc.php";

$warehouseId = isset($_POST['warehouse_id']) ? intval($_POST['warehouse_id']) : 0;

if ($warehouseId > 0) {
    try {
        $query = "SELECT COUNT(*) AS transfer_out_count 
                  FROM transactions 
                  WHERE Origin_ID = :warehouse_id;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':warehouse_id', $warehouseId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode(['transfer_out_count' => $result['transfer_out_count']]);
        } else {
            echo json_encode(['transfer_out_count' => 0]);
        }
    } catch (PDOException $e) {
        echo json_encode(['transfer_out_count' => 0]);
    }
} else {
    echo json_encode(['transfer_out_count' => 0]);
}
?>
