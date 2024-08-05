<?php

session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no ha iniciado sesión, redirige a la página de inicio de sesión
    header("Location: ../login.php"); 
    exit;
}
// incluir funciones y conectar a la base de datos
include '../inc/functions.php';

if(isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];

    // Cambiar el estado del producto a 'Inactive'
    $conn = connect(); // conectar a la base de datos

    // Preparar la sentencia SQL
    $sql_update = "UPDATE Products SET Status = 'Inactive' WHERE Product_ID = $product_id";

    // Ejecutar la declaración preparada
    if ($conn->query($sql_update) === TRUE) {
        // Éxito: Guardar mensaje en sesión
        session_start();
        $_SESSION['message'] = "Product <b> $product_name </b> deleted Successfully.";
    } else {
        // Error: Guardar mensaje de error en sesión
        session_start();
        $_SESSION['error'] = "Error deleting product: " . $conn->error;
    }

    // Redireccionar a Frm_Resu_prd.php después de actualizar el producto
  echo "<script>window.location.href = './Frm_Resu_prd.php';</script>";

} else {
    // Redireccionar si no se proporciona el id del producto por POST
    header("Location: ./Frm_Resu_prd.php");
    exit();
}
?>

