<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no ha iniciado sesión, redirige a la página de inicio de sesión
    header("Location: ../../login.php"); 
    exit;
}

include '../../inc/functions.php';

$status = $_POST['Ordernumber'];

// Verificar si se ha enviado el parámetro Ordernumber por POST
if(isset($_POST['Ordernumber'])) {

    $OrderNum = $_POST['Ordernumber'];

    // Definir el estado predeterminado como 'Cancelled'
    $status = isset($_POST['status']) && !empty($_POST['status']) ? $_POST['status'] : 'Cancelled';

    //echo $status;
    //die('');

    // Conectar a la base de datos
    $conn = connect(); 

    // Preparar la consulta SQL para actualizar el estado de la orden
    $sql_update = "UPDATE orders SET Order_Status = '$status' WHERE Order_Number = '$OrderNum'";

    // Ejecutar la consulta SQL
    if ($conn->query($sql_update) === TRUE) {

        // Redireccionar (Frm_Resu_order.php) 
        $_SESSION['message'] = "Purchase order <b> $OrderNum </b> Cancelled";
  	echo "<script>window.location.href = './Frm_Resu_order.php';</script>";

    } else {
        // Manejar errores si la ejecución falla
        echo "Error updating order status: " . $conn->error;
    }
    
} else {
    // Redireccionar si no se proporciona el número de orden por POST
    header("Location: ./Frm_Resu_order.php");
    exit();
}

?>

