<?php
session_start(); 

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no ha iniciado sesión, redirige a la página de inicio de sesión
    header("Location: ../../login.php"); 
    exit;
}

$authorizedRoles = ['1', '2'];

// Verificar si el rol del usuario está en la lista de roles autorizados
if (!in_array($_SESSION['Role'], $authorizedRoles)) {
    
    $currentUrl = $_SERVER['REQUEST_URI'];
    header("Location: ../../home.php?redirect=" . urlencode($currentUrl));  // Redirige a la página de acceso no autorizado
    exit;

}

include '../../inc/functions.php';


// Variable para almacenar mensajes de error o éxito
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';

unset($_SESSION['message']);
unset($_SESSION['error']);


$Warehouse = isset($_POST['warehouse_id']) ? $_POST['warehouse_id'] : 0;
$WarehouseName = $_SESSION['WarehouseName'];
$Current_WarehouseID = $_SESSION['WarehouseID'];

If ( $Current_WarehouseID !=1){
	$AND = "WHERE Warehouse_ID != '$Current_WarehouseID'";
}else
{ $AND = "";
}


// SQL queries to fetch data
$sql_TransferNro = "SELECT 
    CONCAT('TRF-', LPAD(IFNULL(MAX(Transfer_ID) + 1, 1), 4, '0')) AS TransferNum,
    IFNULL(MAX(Transfer_ID), 0) AS Transfer_ID,
    CONCAT('REF#-', LPAD(IFNULL(MAX(Transfer_ID) + 1, 1), 4, '0')) AS Reference
FROM transfers;
";
$sql_wh_orig = "SELECT * FROM Warehouses $AND ";
$sql_wh_dest = "SELECT * FROM Warehouses WHERE Warehouse_ID != '$Current_WarehouseID'";
$sql_Products = "SELECT p.Product_ID,p.Product_Name,p.Price, a.Quantity 
                    FROM Products p
                    JOIN item_availabilities a ON a.Product_ID=p.Product_ID
                    WHERE a.Warehouse_ID = $Warehouse";
            
$conn = connect();
// Execute the queries
$result = $conn->query($sql_TransferNro);
$result_sql_wh_orig = $conn->query($sql_wh_orig);
$result_sql_wh_dest = $conn->query($sql_wh_dest);
$result3 = $conn->query($sql_Products); // Execute query for products
// Check if queries were successful
if ($result && $sql_wh_orig && $result3) {
    // Fetch the Transfer number
    $row = $result->fetch_assoc();
    $OrderNum = $row["TransferNum"];
    $Orderid = $row["Transfer_ID"];
    $Referenceid = $row["Reference"];
} else {
    // Print error if query failed
    echo "Error executing query: " . $conn->error;
}
// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Items</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
        <!-- jQuery, Popper.js, Bootstrap JS -->
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
        /* Main container for content */
        .S-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin-left: 270px;
            width: calc(100% - 270px);
        }
        /* Main content area */
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
        /* Header styling */
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
        /* Breadcrumbs styling */
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
        /* Estilo para el contenedor de la imagen previa */
        .image-container {
            border: 1px solid #ccc; /* Border Color */
            padding: 10px; /* Internal padding */
            height: 120px;  
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer; /* Make the container clickable */
        }
        .image-container img {
            max-width: 100%;
            max-height: 100%;
            display: none;
        }
        .image-container .choose-image-text {
            display: block;
            color: #666;
            font-size: 14px;
            text-align: center;
        }
        .image-container .cancel-button {
            display: none;
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 5px;
        }
        .image-container .cancel-button:hover {
            background-color: #cc0000;
        }
        .S-required {
            color: red;
            font-size: 17px;
            
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
    <!-- Iframe container -->
    <div class="S-iframe-container">
        <iframe id="S-iframe1" src="../../navbar.php?n=1"></iframe>
    </div>
    <!-- Main content area -->
    <div class="S-main-content">
        <div>
            <!-- Breadcrumbs container -->
            <div class="S-breadcrumbs">
                <span>Home </span><span>/</span>
                <a href="../inventory.php">Inventory </a><span>/</span>
                <a href="./Frm_Resu_trf.php">Transfers </a><span>/</span>
            </div>
            
            <h2>Transfer Requirement</h2>
         
            <form id="addItemForm" action="./Frm_Add_trf_run.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                         
                <!-- Part 1: Destination, Delivery -->
                <div class="form-group row py-4">
                  
                <?php 
                    if ($Current_WarehouseID != '1') 
                    { 
                        


			?>
                       
                        <label for="origin" class="col-sm-1 col-form-label">
                            <span class="S-required">* </span>Origin:
                        </label>
                        <div class="col-sm-2">
                            <select class="form-control" id="origin" name="origin" onchange="reloadPage(this.value)" required>
                                <option value="" disabled selected hidden> -- Select Warehouse --</option>
                                <?php
                                if ($result_sql_wh_orig->num_rows > 0) {
                                    while($row = $result_sql_wh_orig->fetch_assoc()) {
                                        $selected = ($row['Warehouse_ID'] == $Warehouse) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($row["Warehouse_ID"]) . '" ' . $selected . '>' . htmlspecialchars($row["Warehouse_Name"]) . '</option>';
                                    }
                                } else {
                                    echo '<option value="">There are no Deliveries available</option>';
                                }
                                ?>
                            </select>
                        </div>
			
			 <label for="delivery" class="col-sm-1 col-form-label">
                            <span class="S-required">* </span>Destination:
                        </label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="delivery" name="delivery" required readonly autocomplete="off" value="<?php echo htmlspecialchars($WarehouseName); ?>">
                            <input type="hidden" name="deliveryID" value="<?php echo htmlspecialchars($Current_WarehouseID); ?>">
                        </div>

                        <?php





                    } 
                    else 
                    { 
                        ?>


                        <label for="origin1" class="col-sm-1 col-form-label">
                            <span class="S-required">* </span>Origin:
                        </label>
                        <div class="col-sm-2">
                            <select class="form-control" id="origin" name="origin" onchange="reloadPage(this.value)" required>
                                <option value="" disabled selected hidden> -- Select Warehouse --</option>
                                <?php
                                if ($result_sql_wh_orig->num_rows > 0) {
                                    while($row = $result_sql_wh_orig->fetch_assoc()) {
                                        $selected = ($row['Warehouse_ID'] == $Warehouse) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($row["Warehouse_ID"]) . '" ' . $selected . '>' . htmlspecialchars($row["Warehouse_Name"]) . '</option>';
                                    }
                                } else {
                                    echo '<option value="">There are no Deliveries available</option>';
                                }
                                ?>
                            </select>
                        </div>

			<label for="deliveryID" class="col-sm-1 col-form-label">
                            <span class="S-required">* </span>Destination:
                        </label>
                        <div class="col-sm-2">
                            <select class="form-control" id="deliveryID" name="deliveryID"  required>
                                <option value="" disabled selected hidden> -- Select Warehouse --</option>
                                <?php
                                if ($result_sql_wh_dest->num_rows > 0) { 
                                    while($row = $result_sql_wh_dest->fetch_assoc()) {
                                        $selected = ($row['Warehouse_ID'] == $Warehouse) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($row["Warehouse_ID"]) . '" ' . $selected . '>' . htmlspecialchars($row["Warehouse_Name"]) . '</option>';
                                    }
                                } else {
                                    echo '<option value="">There are no Deliveries available</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <?php
                    }
                    ?>

                </div>
                <!-- Part 1: -->       

                
                <div class="form-group row py-1">
                    <label for="order" class="col-sm-1 col-form-label"><span class="S-required">* </span>Transfer #:</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="transfer" name="transfer" required readonly autocomplete="off" value="<?php echo $OrderNum; ?>">
                    </div>
                    <label for="reference" class="col-sm-1 col-form-label"><span class="S-required">Reference:</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="reference" name="reference" autocomplete="off" VALUE="<?php ECHO $Referenceid; ?>">
                    </div>
                </div>
                <div class="form-group row py-1">
                    <label for="date" class="col-sm-1 col-form-label "><span class="S-required">* </span>Date:</label>
                    <div class="col-sm-2">
                        <input type="date" class="form-control" id="date" name="date" required autocomplete="off" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <label for="deliverydate" class="col-sm-1 col-form-label"><span class="S-required">* </span>Delivery Date:</label>
                    <div class="col-sm-2">
                        <input type="date" class="form-control" id="deliverydate" name="deliverydate" required autocomplete="off" value="<?php echo date('Y-m-d', strtotime('+3 days')); ?>">
                    </div>
                </div>
                <!-- Part 2: Item Order -->
                <h4>Items </h4> <p class="small"><span class="S-required">* </span>The list of products is according to the Capacity of the origin Warehouse.</p>
                <!-- Table to display dynamically added items -->
                <table id="numberTable" class="S-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Availability</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Amount</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Rows will be added dynamically here -->
                    </tbody>
                </table>
                <!-- Button to add more items dynamically -->
                <div class="form-group row py-3">
                    <div class="col-sm-10">
                        <button type="button" class="btn btn-primary" id="addItemBtn">Add Item</button>
                        <button type="submit" class="btn btn-success" id="saveBtn" disabled>Save</button>
                        <a href="./Frm_Resu_trf.php" class="btn btn-secondary">Cancel</a> <!-- Cancel button -->
                    </div>
                </div>
            </form>
            <!-- End form -->
            <!-- Footer -->
            <footer class="S-footer">
                <!-- Footer content -->
            </footer>
            <!-- End footer -->
        </div>
    </div>
    <!-- End main content area -->
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
<!-- JavaScript scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Variable global para almacenar el warehouse_id seleccionado
    var selectedWarehouseId = "<?php echo $Warehouse; ?>"; 

    // Función para recargar la página con el warehouse_id seleccionado
    function reloadPage(selectedValue) {
        // Crear un formulario dinámico
        var form = document.createElement('form');
        form.method = 'post';
        form.action = window.location.pathname; // La misma página

        // Crear un input oculto para el parámetro 'warehouse_id'
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'warehouse_id';
        input.value = selectedValue;

        // Agregar el input al formulario
        form.appendChild(input);

        // Agregar el formulario al cuerpo del documento y enviarlo
        document.body.appendChild(form);
        form.submit();
    }

    // Función para mostrar un modal con un mensaje
    function showModal(message) {
        var notificationMessage = document.getElementById('notificationMessage');
        notificationMessage.textContent = message;
        $('#notificationModal').modal('show');
    }

    // Función para verificar validez del formulario
    function checkFormValidity() {
        var isValid = true;

        // Verificar campos requeridos
        if ($("#Supplier").val() == '' || $("#delivery").val() == '' || $("#order").val() == '' || $("#date").val() == '' || $("#deliverydate").val() == '') {
            isValid = false;
        }

        // Verificar si se ha añadido al menos un artículo
        if ($("#numberTable tbody tr").length === 0) {
            isValid = false;
        }

        // Verificar si la cantidad para cada artículo es al menos 1
        $("#numberTable tbody tr").each(function() {
            var quantity = parseFloat($(this).find(".quantity").val());
            if (isNaN(quantity) || quantity < 1) {
                isValid = false;
            }
        });

        // Habilitar/deshabilitar botón de guardar según validez
        $("#saveBtn").prop("disabled", !isValid);
    }

    // Función para inicializar el manejo de eventos después de que el documento esté listo
    $(document).ready(function() {
        // Manejar el clic en el botón para agregar artículo
        $("#addItemBtn").on("click", function() {
            var html = '';
            html += '<tr>';
            html += '<td><select class="form-control" name="item[]" required>';
            html += '<option value="" disabled selected hidden> -- Select Item --</option>';
            <?php
            // Resetear el puntero de la consulta de productos
            $result3->data_seek(0);
            if ($result3->num_rows > 0) {
                while($row = $result3->fetch_assoc()) {
                    echo 'html += \'<option value="' . $row["Product_ID"] . '">' . $row["Product_Name"] . '</option>\';';
                }
            } else {
                echo 'html += \'<option value="">There are no Products available</option>\';';
            }
            ?>
            html += '</select></td>';
            html += '<td><input type="number" min="0" class="form-control text-center availability" name="availability[]" min="0" step="any" readonly></td>'; // Campo solo de lectura para la disponibilidad
            html += '<td><input type="number" min="1" class="form-control text-center quantity" name="quantity[]" required value="1"></td>'; // Cantidad mínima configurada a 1
            html += '<td><input type="number" min="0" class="form-control text-center price" name="price[]" step="any" required></td>'; // Campo editable para el precio
            html += '<td><input type="number" class="form-control amount" text-center name="amount[]" readonly></td>'; // Campo solo de lectura para el monto
            html += '<td><button type="button" class="btn btn-danger deleteRow">Delete</button></td>';
            html += '</tr>';
            $("#numberTable tbody").append(html);
            checkFormValidity(); // Verificar validez del formulario después de agregar fila
        });

        // Manejar el clic en el botón para eliminar fila de la tabla
        $("#numberTable").on("click", ".deleteRow", function() {
            $(this).closest("tr").remove();
            checkFormValidity(); // Verificar validez del formulario después de eliminar fila
        });

         // Calculate amount based on quantity and price
         $("#numberTable").on("input", ".quantity, .price", function() {
            var row = $(this).closest("tr");
            var quantity = parseFloat(row.find(".quantity").val());
            var price = parseFloat(row.find(".price").val());
            var amount = quantity * price;
            if (!isNaN(amount)) {
                row.find(".amount").val(amount.toFixed(2)); // Update amount with 2 decimal places
            }
            checkFormValidity(); // Check form validity after input change
        });

        // Manejar el cambio en la tabla para calcular precio y disponibilidad
        $("#numberTable").on("change", "select[name='item[]']", function() {
            var productId = $(this).val();
            var row = $(this).closest("tr");

            // Verificar duplicados
            var isDuplicate = false;
            $("select[name='item[]']").each(function() {
                if ($(this).val() == productId && this !== row.find("select[name='item[]']")[0]) {
                    isDuplicate = true;
                }
            });

            if (isDuplicate) {
                showModal("Este artículo ya ha sido añadido. Por favor elige otro artículo.");
                $(this).val("");
            } else {
                // Realizar la solicitud AJAX con productId y warehouseId
                $.ajax({
                    url: './get_product_available.php', // Script PHP para obtener el precio del producto
                    method: 'POST',
                    data: {
                        productId: productId,
                        warehouseId: selectedWarehouseId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            row.find(".price").val(response.price); // Establecer el precio en el campo editable
                            row.find(".availability").val(response.availability); // Establecer la disponibilidad en el campo solo de lectura
                            var quantity = parseFloat(row.find(".quantity").val());
               
                            var amount = quantity * response.price;
                            if (!isNaN(amount)) {
                                row.find(".amount").val(amount.toFixed(2)); // Actualizar monto con 2 decimales
                            }
                            checkFormValidity(); // Verificar validez del formulario después de actualizar precio y disponibilidad
                        } else {
                            console.error('Error al obtener el precio del producto');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        });

        // Verificar si hay mensaje para mostrar
        var message = "<?php echo isset($_SESSION['message']) ? $_SESSION['message'] : ''; ?>";
        var error = "<?php echo isset($_SESSION['error']) ? $_SESSION['error'] : ''; ?>";

        // Mostrar el modal según el tipo de mensaje
        if (message.trim() !== '') {
            showModal(message);
        } else if (error.trim() !== '') {
            showModal(error);
        }

        // Limpiar los mensajes de sesión después de mostrarlos
        <?php unset($_SESSION['message']); ?>
        <?php unset($_SESSION['error']); ?>
    });
</script>


</body>
</html>
