<?php
session_start(); 

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no ha iniciado sesión, redirige a la página de inicio de sesión
    header("Location: ../../login.php"); 
    exit;
}

include '../../inc/functions.php';

// Variable para almacenar mensajes de error o éxito
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';

unset($_SESSION['message']);
unset($_SESSION['error']);

// Parameters
$TransferNum = $_POST['Transfernumber'];



// SQL Transfer
$sql = "SELECT 	t.Transfer_Number, 
		t.Transfer_Date, 
        t.Transfer_Status, 
        t.Delivery_Date, 
        t.Warehouse_Origin AS WhOriginID, 
        worig.Warehouse_Name AS Origin,
        t.Warehouse_Destination AS WhdeliveryID, 
        wdeli.Warehouse_Name AS Destination,
        wdeli.wh_user_manager_id AS WhResponsable,
        wdeli.Phone,
        CONCAT(u.FirstName, ' ', u.LastName) AS Contact, 
        u.username 
      FROM transfers t 
      JOIN 
      	user u ON t.User_ID_Responsable = u.user_ID  
      JOIN
      	Warehouses worig ON t.Warehouse_Origin = worig.Warehouse_ID
      JOIN
        Warehouses wdeli ON t.Warehouse_Destination = wdeli.Warehouse_ID
      WHERE t.Transfer_Number = '$TransferNum'";


// SQL details_Transfer
$sql2 = "SELECT t.*, p.Product_Name
        FROM details_transfer t
        JOIN Products p ON t.Product_ID = p.Product_ID
        WHERE t.Transfer_Number='$TransferNum'
        ORDER BY t.Product_ID";

//echo $sql2;
//die('hola');
//
$conn = connect();
$result = $conn->query($sql);
$row = $result->fetch_assoc();

/**** Field Sql1 *****/
$Date = $row['Transfer_Date'];
$WhOrigin = $row['Origin'];
$WhOriginID = $row['WhOriginID'];
$WhDelivery = $row['Destination'];
$WhDeliveryID = $row['WhdeliveryID'];
$Delivery_Date = $row['Delivery_Date'];
$Responsable = $row['username'];
$phone = $row['Phone'];

//

/**** Query $sql2 *****/
$result2 = $conn->query($sql2);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery, Popper.js, Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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

        #S-iframe1 {
            width: 100%;
            height: 100%;
            border: none;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 0;
        }

        .S-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin-left: 270px;
            width: calc(100% - 270px);
        }

        .S-main-content {
            padding: 20px;
            flex: 1;
            background-color: #ffffff;
            border-left: 0px solid #ddd;
            border-radius: 0 10px 10px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: auto;
            margin-top: 5px;
            position: relative;
            z-index: 1;
        }

        #S-header {
            width: 100%;
            background: #007bff;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 2;
        }

        .S-breadcrumbs {
            margin-bottom: 20px;
            font-size: 14px;
            color: #555;
        }

        .S-breadcrumbs a {
            text-decoration: none;
            color: #007bff;
        }

        .S-breadcrumbs a:hover {
            text-decoration: underline;
        }

        .placeholder {
            font-size: 12px;
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

        .S-table th {
            width: 200px; /* Ancho ajustado del th */
        }

        .S-table tbody tr:nth-child(even) {
            background-color: #f2f2f2; /* Color de fila par */
        }

        .S-table tbody tr:nth-child(odd) {
            background-color: #ffffff; /* Color de fila impar */
        }

        /* Estilo condicional para Quantity_End */
        .highlight-row {
            background-color: #ffcccc; /* Fondo rojo claro */
        }

        .highlight-row input {
            background-color: #ffcccc; /* Fondo rojo claro para el input */
        }

        /* CSS para ocultar las flechas de incremento en input type='number' */
        .text-center {
            text-align: center;
        }

        .no-spinner::-webkit-outer-spin-button,
        .no-spinner::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .no-spinner {
            -moz-appearance: textfield;
        }
        .receive-header {
            background-color: #FFCE33; 
            color:black;
        }

    </style>
</head>

<body>

<div class="S-container">
    <!-- Contenedor del iframe -->
    <div class="S-iframe-container">
        <iframe id="S-iframe1" src="../../navbar.php?n=1"></iframe>
    </div>

    <div class="S-main-content">
        <div>
            <!-- Contenedor de migas de pan -->
            <div class="S-breadcrumbs">
                <span>Home </span><span>/</span>
                <a href="../inventory.php">Inventory </a><span>/</span>
                <a href="./Frm_Resu_trf.php">Transfer </a><span>/</span>
            </div>
            
            <h2>Receive Transfer</h2>

            <!-- Controles del formulario -->
            <form id="addItemForm" action="./receive_trf_run.php" method="POST" enctype="multipart/form-data" onsubmit="return confirmSubmit()"> 

                <!-- Parte 1: Nombre -->
                <div class="form-group row py-4">
                    <div class="col-sm-3">
                        <img src="../../images/Transfer.jpeg" alt="Transactions">
                    </div>
                    <div class="col-sm-2">
                        <h2><?php echo $TransferNum; ?></h2>
                        <?php 
                            echo "Transfer Date: ". $Date."<br>"; 
                            echo "Transfer Created by: ". $Responsable; 
                        ?>
                    </div>

                    <div class="col-sm-2">
                        <h2><?php echo "Delivery Warehouse"; ?></h2>
                        <?php 
                            echo "<b>".$WhDelivery."</b><br>"; 
                            echo "Delivery Date: ". $Delivery_Date."<br>"; 
                            $today = date('Y-m-d');
                            echo "Reception Date: "."<b>".$today."</b>";
                        ?>
                    </div>

                    <div class="col-sm-2">
                        <h2><?php echo "Origin Warehouse"; ?></h2>
                        <?php 
                            echo "<b>".$WhOrigin."</b><br>"; 
                            echo "Contact Name: ". $Responsable."<br>"; 
                            echo "Phone: ". $phone."<br>";    
                        ?>
                    </div>

                </div>
                
                <!-- Parte 2: Table -->
                <table class="S-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product_ID</th>
                            <th>Product_Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                            <th class="receive-header">Received</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        if ($result2->num_rows > 0) {
                            $it = 1;
                            while ($row2 = $result2->fetch_assoc()) {
                                $productID = $row2["Product_ID"];
                                $productName = $row2["Product_Name"];
                                $price = $row2["Price"];
                                $quantity = $row2["Quantity"];
                                $amount = $row2["Amount"];
                                
                                echo "<tr>";
                                echo "<td>{$it}</td>";
                                echo "<td>{$productID}</td>";
                                echo "<input type='hidden' name='ProductID[]' value='" . htmlspecialchars($productID) . "'>";
                                echo "<td>{$productName}</td>";
                                echo "<td>{$price}</td>";
                                echo "<input type='hidden' id='Price[{$productID}]' name='Price[{$productID}]' value='" . htmlspecialchars($price) . "'>";
                                echo "<td>{$quantity}</td>";
                                echo "<input type='hidden' id='quantity[{$productID}]' name='quantity[{$productID}]' value='" . htmlspecialchars($quantity) . "'>";
                                echo "<td>{$amount}</td>";
                                echo "<input type='hidden' id='amount[{$productID}]' name='amount[{$productID}]' value='" . htmlspecialchars($amount) . "'>";
                                echo "<td><input type='number' min='0' max='$quantity' id='Quantity_End{$productID}' name='Quantity_End[{$productID}]' value='{$quantity}' onchange='checkQuantityEnd(this, {$quantity})' oninput='validateNumericInput(this)' class='form-control no-spinner text-center' autofocus onfocus='this.select()'></td>";
                                echo "<input type='hidden' name='Transaction' value='TRF'>";
                                echo "<input type='hidden' name='transferNum' value='$TransferNum'>";
                                echo "<input type='hidden' name='WhoriginID' value='$WhOriginID'>";
                                echo "<input type='hidden' name='WhdeliveryID' value='$WhDeliveryID'>";

                                echo "<input type='hidden' name='today' value='$today'>";
                                echo "</tr>";
                                
                                $it++;
                            }
                        }
                        ?>

                    </tbody>

                </table>
                <!-- End Parte 2: Table -->
                
                <!-- Parte 3: Botones -->
                <div class="form-group row">
                   <div class="col-sm-12 py-3">
        		<button id="saveButton" type="submit" class="btn btn-primary" disabled>Save</button>
        		<a href="./Frm_Resu_trf.php" class="btn btn-secondary">Cancel</a>
    		   </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de notificación -->
    <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">Notification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="notificationMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para el manejo del modal y validación -->
    <script>

		// Función para habilitar o deshabilitar el botón "Save"
    function toggleSaveButton() {
        let totalReceived = 0;
        const inputs = document.querySelectorAll('input[name^="Quantity_End"]'); // Selecciona todos los inputs de Quantity_End

        inputs.forEach(input => {
            const value = parseFloat(input.value) || 0; // Obtiene el valor actual, considerando cero si no es un número
            totalReceived += value; // Suma el valor al total
        });

        // Habilita o deshabilita el botón dependiendo del totalReceived
        document.getElementById('saveButton').disabled = totalReceived <= 0;
    }

    // Agrega un evento para llamar a la función toggleSaveButton cuando cambie el valor de los inputs
    document.querySelectorAll('input[name^="Quantity_End"]').forEach(input => {
        input.addEventListener('input', toggleSaveButton);
    });

    // Llama a la función al cargar la página para establecer el estado inicial del botón
    toggleSaveButton();	

        // Función para mostrar el modal con el mensaje recibido
        function showModal(message) {
            var notificationMessage = document.getElementById('notificationMessage');
            notificationMessage.textContent = message;
            $('#notificationModal').modal('show');
        }

        // Verificar si hay un mensaje para mostrar
        $(document).ready(function() {
            // Obtener el mensaje de éxito o error de la sesión PHP
            var message = "<?php echo $message; ?>";
            var error = "<?php echo $error; ?>";

            // Mostrar el modal según el tipo de mensaje
            if (message.trim() !== '') {
                showModal(message);
            } else if (error.trim() !== '') {
                showModal(error);
            }
        });

        // Función para verificar Quantity_End al cambiar su valor
        function checkQuantityEnd(input, originalQuantity) {
            var quantityEnd = parseFloat(input.value); // Obtener el valor actual de Quantity_End como número

            // Comparar Quantity_End con Quantity original
            if (quantityEnd !== originalQuantity) {
                input.style.backgroundColor = '#ffcccc'; // Cambiar color de fondo del input
            } else {
                input.style.backgroundColor = ''; // Restablecer color de fondo del input
            }
        }

        // Función para validar entrada numérica
        function validateNumericInput(input) {
            // Obtener el valor actual del input
            let value = input.value;

            // Reemplazar cualquier caracter no numérico con una cadena vacía
            value = value.replace(/[^0-9]/g, '0');

            // Asignar el valor modificado de vuelta al input
            input.value = value;
        }

        // Función para mostrar confirmación antes de enviar el formulario
        function confirmSubmit() {
            if (confirm("¿Are you sure you can complete and close the Transfer?")) {
                return true; // Permitir el envío del formulario
            } else {
                return false; // Cancelar el envío del formulario
            }
        }
    </script>

</div>
</body>
</html>
