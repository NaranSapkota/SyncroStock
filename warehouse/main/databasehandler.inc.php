<?php

$dsn = "mysql:host=localhost;dbname=uitilneuxt_Syncro";
$dbusername = "uitilneuxt_root";
$dbpassword = "SyncroStock";


try {
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {

    echo "Connection failed: " . $e->getMessage();
    
}
