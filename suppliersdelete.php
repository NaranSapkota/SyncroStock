<?php

if (isset($_GET["Supplier_ID"])) {
    $Supplier_ID = $_GET["Supplier_ID"];

    include 'connection.php';
    $sql = "DELETE FROM Suppliers WHERE Supplier_ID=$Supplier_ID";
    $conn->query($sql);

}

echo '<script>window.location.href = "./suppliers.php";</script>';
exit;


?>