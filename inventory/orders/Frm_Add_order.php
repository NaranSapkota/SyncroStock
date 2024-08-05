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
    header("Location: ../../home.php?redirect=" . urlencode($currentUrl));  // 
    exit;

}

// Include the functions file for database connection
include '../../inc/functions.php';


$userSystem =$_SESSION['FullName'];
$UserLogin=$_SESSION['username'];
$UserID=$_SESSION['UserID'];
$warehouseName= $_SESSION['WarehouseName'];


// SQL queries to fetch data
$sql_OrderNro = "SELECT 
    CONCAT('PO-', LPAD(IFNULL(MAX(Order_ID) + 1, 1), 4, '0')) AS OrderNum,
    IFNULL(MAX(Order_ID), 0) AS Order_ID,
    CONCAT('REF#-', LPAD(IFNULL(MAX(Order_ID) + 1, 1), 4, '0')) AS Reference
FROM orders;
";

$sql_Warehosue = "SELECT * FROM Warehouses";
$sql_Suppliers = "SELECT * FROM Suppliers";
$sql_Products = "SELECT * FROM Products"; // Query for products

// Connect to the database
$conn = connect();

// Execute the queries
$result = $conn->query($sql_OrderNro);
$result1 = $conn->query($sql_Warehosue);
$result2 = $conn->query($sql_Suppliers);
$result3 = $conn->query($sql_Products); // Execute query for products

// Check if queries were successful
if ($result && $result1 && $result2 && $result3) {
    // Fetch the order number
    $row = $result->fetch_assoc();
    $OrderNum = $row["OrderNum"];
    $Orderid = $row["Order_ID"];
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
                <a href="./Frm_Resu_order.php">Orders </a><span>/</span>
            </div>

            <h2>Add Purchase Orders</h2>
            <!-- End breadcrumbs container -->

            <!-- Form for adding items -->
            <form id="addItemForm" action="Frm_Add_Orden_run.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                <!-- Hidden input for Order_ID -->
                <input type="hidden" name="orderid" value="<?php echo $Orderid; ?>">

                <!-- Part 1: Supplier, Delivery, Order Number, Reference, Date, Delivery Date -->
                <div class="form-group row py-4">
                    <label for="Supplier" class="col-sm-1 col-form-label"><span class="S-required">* </span>Supplier:</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="Supplier" name="Supplier" required>
                            <option value="" disabled selected hidden> -- Supplier --</option>
                            <?php
                            if ($result2->num_rows > 0) {
                                while($row = $result2->fetch_assoc()) {
                                    echo '<option value="' . $row["Supplier_ID"] . '">' . $row["Company_Name"] . '</option>';
                                }
                            } else {
                                echo '<option value="">There are no Suppliers available</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <label for="delivery" class="col-sm-1 col-form-label"><span class="S-required">* </span>Delivery:</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="delivery" name="delivery" required>
                            <option value="" disabled selected hidden> -- Select Warehouse --</option>
                            <?php
                            if ($result1->num_rows > 0) {
                                while($row = $result1->fetch_assoc()) {
                                    echo '<option value="' . $row["Warehouse_ID"] . '">' . $row["Warehouse_Name"] . '</option>';
                                }
                            } else {
                                echo '<option value="">There are no Deliveries available</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row py-1">
                    <label for="order" class="col-sm-1 col-form-label"><span class="S-required">* </span>Purchase Order#:</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="order" name="order" required readonly autocomplete="off" value="<?php echo $OrderNum; ?>">
                    </div>

                    <label for="reference" class="col-sm-1 col-form-label"><span class="S-required">* </span>Reference:</label>
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
                <h4>Item Order </h4>

                <!-- Table to display dynamically added items -->
                <table id="numberTable" class="S-table">
                    <thead>
                        <tr>
                            <th>Item</th>
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
                        <a href="./Frm_Resu_order.php" class="btn btn-secondary">Cancel</a> <!-- Cancel button -->
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
                <button type="button" class="btn btn-secondary" id="modalCloseBtn">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    // Function to show modal with message
    function showModal(message) {
        var notificationMessage = document.getElementById('notificationMessage');
        notificationMessage.textContent = message;
        $('#notificationModal').modal('show');
    }

    // Function to dynamically add rows to the table
    $(document).ready(function() {
        $("#addItemBtn").on("click", function() {
            var html = '';
            html += '<tr>';
            html += '<td><select class="form-control" name="item[]" required>';
            html += '<option value="" disabled selected hidden> -- Select Item --</option>';
            <?php
            // Reset pointer for products query
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
            html += '<td><input type="number" min="1" class="form-control quantity" name="quantity[]" required min="1" value="1"></td>'; // Minimum quantity set to 1
            html += '<td><input type="number" min="0" class="form-control price" name="price[]" step="any" required></td>'; // Editable field for price
            html += '<td><input type="number" class="form-control amount" name="amount[]" readonly></td>'; // Readonly field for amount
            html += '<td><button type="button" class="btn btn-danger deleteRow">Delete</button></td>';
            html += '</tr>';
            $("#numberTable tbody").append(html);
            checkFormValidity(); // Check form validity after adding row
        });

        // Delete row from table
        $("#numberTable").on("click", ".deleteRow", function() {
            $(this).closest("tr").remove();
            checkFormValidity(); // Check form validity after deletion
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

        // AJAX call to get product details including price
        $("#numberTable").on("change", "select[name='item[]']", function() {
            var productId = $(this).val();
            var row = $(this).closest("tr");

            // Check for duplicate items
            var isDuplicate = false;
            $("select[name='item[]']").each(function() {
                if ($(this).val() == productId && this !== row.find("select[name='item[]']")[0]) {
                    isDuplicate = true;
                }
            });

            if (isDuplicate) {
                showModal("This item has already been added. Please choose a different item.");
                $(this).val("");
            } else {
                $.ajax({
                    url: 'get_product_price.php', // PHP script to fetch product price
                    method: 'POST',
                    data: { productId: productId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            row.find(".price").val(response.price); // Set the price in the editable input field
                            var quantity = parseFloat(row.find(".quantity").val());
                            var amount = quantity * response.price;
                            if (!isNaN(amount)) {
                                row.find(".amount").val(amount.toFixed(2)); // Update amount with 2 decimal places
                            }
                            checkFormValidity(); // Check form validity after price update
                        } else {
                            console.error('Failed to fetch product price');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        });

        // Function to check form validity
        function checkFormValidity() {
            var isValid = true;

            // Check required fields
            if ($("#Supplier").val() == '' || $("#delivery").val() == '' || $("#order").val() == '' || $("#date").val() == '' || $("#deliverydate").val() == '') {
                isValid = false;
            }

            // Check if there is at least one item added
            if ($("#numberTable tbody tr").length === 0) {
                isValid = false;
            }

            // Check if quantity for each item is at least 1
            $("#numberTable tbody tr").each(function() {
                var quantity = parseFloat($(this).find(".quantity").val());
                if (isNaN(quantity) || quantity < 1) {
                    isValid = false;
                }
            });

            // Enable/disable save button based on validity
            $("#saveBtn").prop("disabled", !isValid);
        }

        // Call checkFormValidity initially and on any change
        checkFormValidity();
        $("#addItemForm").on("input change", checkFormValidity);

        // Form submission event
        $("#addItemForm").on("submit", function(event) {
            // Prevent form submission if save button is disabled
            if ($("#saveBtn").prop("disabled")) {
                event.preventDefault();
                alert("Please fill in all required fields and ensure each item has a quantity of at least 1.");
            }
        });

    });

    // Check if there is a message to show
    $(document).ready(function() {
        // Get success or error message from PHP session
        var message = "<?php echo isset($_SESSION['message']) ? $_SESSION['message'] : ''; ?>";
        var error = "<?php echo isset($_SESSION['error']) ? $_SESSION['error'] : ''; ?>";

        // Show the modal based on the message type
        if (message.trim() !== '') {
            showModal(message);
        } else if (error.trim() !== '') {
            showModal(error);
        }
        
        // Clear the session messages after displaying them
        <?php unset($_SESSION['message']); ?>
        <?php unset($_SESSION['error']); ?>
    });

</script>

</body>
</html>
