<?php

session_start(); 

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no ha iniciado sesión, redirige a la página de inicio de sesión
    header("Location: ../../login.php"); 
    exit;
}

include '../../inc/functions.php'; // Asegúrate de que el archivo de funciones está incluido

// Verificar si se recibió el ID del producto por POST
if (isset($_POST['productId'])) {
    $productId = $_POST['productId'];

    // Realizar la consulta para obtener el precio del producto
    $conn = connect(); // Conectar a la base de datos, usa tu propia función de conexión

    $sql = "SELECT Price FROM Products WHERE Product_ID = ?"; // Ajusta la consulta según tu esquema de base de datos
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $price = $row['Price'];

        // Devolver el precio en formato JSON
        echo json_encode(array('success' => true, 'price' => $price));
    } else {
        echo json_encode(array('success' => false));
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(array('success' => false));
}
?>

