<?php
require_once "main/databasehandler.inc.php";
try {
    $query = "SELECT username, FirstName, LastName FROM user;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($users);
} catch (PDOException $e) {
    echo json_encode([]);
}
?>