<?php
// limpiar buffer de PHP para almacenar en memoria
ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            background-color: #ffffff;
            font-size: 14px;
        }

        .S-iframe-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        /* Main container for content */
        .S-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
 
        }

        /* Main content area */
        .S-main-content {
            padding: 20px;
            flex: 1;
            background-color: #ffffff;
            border-left: 1px solid #ddd;
            border-radius: 0 10px 10px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: auto;
            margin-top: 5px;
            position: relative;
            z-index: 1;
        }

        /* Table styling */
        .S-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .S-table thead {
            background: #2596be;
            color: white;
        }

        .S-table th,
        .S-table td {
            padding: 8px;
            border: 1px solid #dee2e6;
            text-align: center;
        }

        /* Establecemos colores alternos para las filas */
        .S-table tbody tr:nth-child(even) {
            background-color: #f2f2f2; /* Color de fila par */
        }

        .S-table tbody tr:nth-child(odd) {
            background-color: #ffffff; /* Color de fila impar */
        }

    </style>
</head>
<body>

<div class="S-container">
        <!-- Tabla de productos -->
        <table class="S-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Category</th>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>SKU</th>
                    <th>Unit Price</th>
                    <th>Reorder</th>
                </tr>
            </thead>

            <tbody>
                <?php
                // Incluimos las funciones y nos conectamos a la base de datos
                include '../inc/functions.php';
                $conn = connect();

                // Construimos la consulta SQL base
                $sql = "SELECT p.*, pc.Category_Name
                        FROM Products p
                        JOIN Product_Categories pc ON p.Category_ID = pc.Category_id
                        WHERE 1=1 ";

                // Obtenemos el total de registros
                $result = $conn->query($sql);
                $total_records = $result->num_rows;

                // Mostrar los resultados en la tabla
                if ($result->num_rows > 0) {
                    $it=1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $it++ . "</td>";
                        echo "<td>" . $row['Category_Name'] . "</td>";
                        echo "<td>" . $row['Product_ID'] . "</td>";
                        echo "<td>" . $row['Product_Name'] . "</td>";
                        echo "<td>" . $row['Brand'] . "</td>";
                        echo "<td>" . $row['Model'] . "</td>";
                        echo "<td>" . $row['SKU'] . "</td>";
                        echo "<td>" . $row['Price'] . "</td>";
                        echo "<td>" . $row['Minimum_Level'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>No products found</td></tr>";
                }

                // Cerrar la conexión a la base de datos
                $conn->close();
                ?>
            </tbody>
        </table>

    </div>

</body>
</html>

<?php

// almacenar el contenido HTML en variable
$html = ob_get_clean();

//echo $html;


require_once '../libraries/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
$dompdf = new Dompdf();


// opciones para el renderizado
$options = $dompdf->getOptions();
$options->set(array('isRemoteEnabled' => true));
$dompdf->setOptions($options);

// cargar el contenido HTML generado
$dompdf->loadHtml($html);

// establecer el formato del papel
$dompdf->setPaper('letter');

// renderizar el PDF
$dompdf->render();

// mostrar el PDF en el navegador sin opción de descarga
$dompdf->stream('prueba.pdf', array('Attachment' => false));
?>
