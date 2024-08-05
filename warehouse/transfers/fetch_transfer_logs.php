<?php
include '../main/databasehandler.inc.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 15;
$offset = ($page - 1) * $records_per_page;

try {
    $query = "
        SELECT 
            t.Transaction_ID,
            t.Type,
            t.Transactions_Number,
            t.Date,
            t.Quantity,
            t.Price,
            ow.Warehouse_Name AS origin_warehouse, 
            dw.Warehouse_Name AS destination_warehouse
        FROM transactions t
        JOIN Warehouses ow ON t.Origin_ID = ow.Warehouse_ID
        JOIN Warehouses dw ON t.Destination_ID = dw.Warehouse_ID
        WHERE 1=1
    ";

    if (!empty($search)) {
        $query .= " AND (t.Transactions_Number LIKE :search OR ow.Warehouse_Name LIKE :search OR dw.Warehouse_Name LIKE :search)";
    }
    if (!empty($filter)) {
        $query .= " AND (t.Type = :filter OR ow.Warehouse_Name = :filter OR dw.Warehouse_Name = :filter)";
    }

    $query .= " ORDER BY t.Date DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($query);

    if (!empty($search)) {
        $stmt->bindValue(':search', '%' . $search . '%');
    }
    if (!empty($filter)) {
        $stmt->bindValue(':filter', $filter);
    }
    $stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $transfers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // For pagination
    $count_query = "
        SELECT COUNT(*) 
        FROM transactions t
        JOIN Warehouses ow ON t.Origin_ID = ow.Warehouse_ID
        JOIN Warehouses dw ON t.Destination_ID = dw.Warehouse_ID
        WHERE 1=1
    ";

    if (!empty($search)) {
        $count_query .= " AND (t.Transactions_Number LIKE :search OR ow.Warehouse_Name LIKE :search OR dw.Warehouse_Name LIKE :search)";
    }
    if (!empty($filter)) {
        $count_query .= " AND (t.Type = :filter OR ow.Warehouse_Name = :filter OR dw.Warehouse_Name = :filter)";
    }

    $count_stmt = $pdo->prepare($count_query);

    if (!empty($search)) {
        $count_stmt->bindValue(':search', '%' . $search . '%');
    }
    if (!empty($filter)) {
        $count_stmt->bindValue(':filter', $filter);
    }

    $count_stmt->execute();
    $total_records = $count_stmt->fetchColumn();
    $total_pages = ceil($total_records / $records_per_page);

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
