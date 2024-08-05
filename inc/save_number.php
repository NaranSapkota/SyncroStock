<?php
session_start();

include '../inc/functions.php';
$conn = connect();

// Obtener los datos enviados desde el frontend
$data = json_decode($_POST['numbers'], true);

// Iterar sobre los datos y realizar la inserción en la tabla final_table
foreach ($data as $item) {
    $numeroTemporal = $item['number'];
    $valorDefecto = $item['defaultValue'];
    $monto = $item['amount'];

    // Preparar la consulta SQL
    $sql = "INSERT INTO final_table (number, default_value, amount) VALUES ('$numeroTemporal', '$valorDefecto', '$monto')";

    // Ejecutar la consulta
    if ($conn->query($sql) !== TRUE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
        exit;
    }
}

// Cerrar conexión
$conn->close();

// Respuesta de éxito
echo "Datos guardados correctamente";
?>
