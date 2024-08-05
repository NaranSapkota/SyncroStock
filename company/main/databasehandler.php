<?php
// databasehandler.php

$host = 'localhost';
$db = 'uitilneuxt_Syncro';
$user = 'uitilneuxt_root';  // Replace with your database username
$pass = 'SyncroStock';      // Replace with your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
