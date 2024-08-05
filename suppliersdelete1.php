<?php

if (isset($_GET["Supplier_ID"])) {
    $Supplier_ID = $_GET["Supplier_ID"];

    include 'connection.php';
    $sql = "DELETE FROM suppliers WHERE Supplier_ID=$Supplier_ID";
    $conn->query($sql);

}

header("location:suppliers.php");
exit;


?>