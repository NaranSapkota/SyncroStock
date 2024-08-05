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
    $warehouseId = $_POST['warehouseId'];

    // Realizar la consulta para obtener el precio y la cantidad del producto
    $conn = connect(); // Conectar a la base de datos, usa tu propia función de conexión

    $sql = "SELECT p.Price, a.Quantity AS Available
            FROM Products p
            JOIN item_availabilities a ON a.Product_ID = p.Product_ID
            WHERE p.Product_ID = ?
            AND Warehouse_ID = $warehouseId"; 

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $price = $row['Price'];
        $availability = $row['Available']; // Disponibilidad del producto

        // Devolver precio y cantidad en formato JSON
        echo json_encode(array('success' => true, 'price' => $price, 'availability' => $availability));
    } else {
        echo json_encode(array('success' => false));
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(array('success' => false));
}
?>


