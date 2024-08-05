<?php    
session_start(); 

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no ha iniciado sesión, redirige a la página de inicio de sesión
    header("Location: ../../login.php"); 
    exit;
}
    // Include the functions file for database connection
    include '../../inc/functions.php';
    
    // Initialize variables
    $OrderNum = isset($_POST['Ordernumber']) ? $_POST['Ordernumber'] : 0;

    // Check if Ordernumber is provided via POST
    if(isset($_POST['Ordernumber'])) {
        $OrderNum = $_POST['Ordernumber'];
        $conn = connect(); 

        // Query to retrieve order details
        $sql_OrderNro = "SELECT 
                            o.Order_Number, 
                            o.Order_Date, 
                            o.Order_Status, 
                            o.Warehouse_ID, 
                            o.User_ID_Responsable, 
                            o.Supplier_id, 
                            o.Reference,
                            o.Delivery_Date 
                        FROM 
                            orders o 
                        WHERE 
                            o.Order_Number = '$OrderNum'";
       
        $result = $conn->query($sql_OrderNro);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $Order_number = $row["Order_Number"];
            $order_date = $row["Order_Date"];
            $order_status = $row["Order_Status"];
            $warehouse = $row["Warehouse_ID"];
            $responsable = $row["User_ID_Responsable"];
            $supplier = $row["Supplier_id"];
            $reference = $row["Reference"];
            $delivery_date = $row["Delivery_Date"];

            // Queries for dropdowns
            $sql_Warehouse = "SELECT * FROM Warehouses";
            $sql_Suppliers = "SELECT * FROM Suppliers";
            $sql_Products = "SELECT * FROM Products"; 
            
            $result1 = $conn->query($sql_Warehouse);
            $result2 = $conn->query($sql_Suppliers);
            $result3 = $conn->query($sql_Products); 

            // Query to retrieve order details
            $sql_Orderdetails = "SELECT 
                                    od.Order_Number, 
                                    od.Product_ID, 
                                    od.Quantity, 
                                    od.Price, 
                                    od.amount 
                                FROM 
                                    details_orders od 
                                WHERE 
                                    od.Order_Number = '$OrderNum'";
        
            $result4 = $conn->query($sql_Orderdetails);
        } else {
            $OrderNum = 0;
        }
    } else {
        // Redirect if Ordernumber is not provided via POST
        header("Location: ./Frm_Resu_order.php");
        exit();
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

        /* Stile for Previe Image */
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

            <h2>Editing Purchase Orders <span class="S-required"><?php echo '<h2># '.$OrderNum.'</h2>'; ?></span></h2>

             <!-- Button to change Status -->

           
          
    <div class="row justify-content-start">
        <div class="col-12">
            <div class="form-group row py-4" style="width: 50%; ">
                <div class="col-sm-4">
                <?php
                    // Formulario para enviar orden
                    echo "<form id='actionForm' action='../../fpdf/send_pdf_mail.php' method='post'>";
                        echo "<input type='hidden' name='Ordernumber' value='" . $OrderNum . "'>";
                        echo "<button type='submit' id='sendOrderBtn' class='btn btn-warning btn-block'> Send Order to Supplier </button>";
                    echo "</form>";
                ?>
                </div>
                
                <div class="col-sm-4">
                    <?php
                    // Form for receiving order
                    echo "<form action='./receive_order.php' method='post'>";
                    echo "<input type='hidden' name='Ordernumber' value='" . $OrderNum . "'>";
                    echo "<button type='submit' name='receive' class='btn btn-primary btn-block'> Receive Order </button>";
                    echo "</form>";
                    ?>
                </div>
                <div class="col-sm-4">
                    <?php
                    // Form for canceling order
                    echo "<form action='./Del_Order.php' method='post'>";
                    echo "<input type='hidden' name='Ordernumber' value='" . $OrderNum . "'>";
                    echo "<button type='button' name='del_order' onclick='confirmDelete(\"Cancelled\")' class='btn btn-danger btn-block'> Cancel Order </button>";
                    echo "</form>";
                    ?>
                </div>
            </div>
        </div>
    </div>

            <!-- Form for adding items -->
            <form id="editItemForm" action="Frm_edit_order_run.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()"> 
                
                <!-- Hidden input for Order_ID -->
                <input type="hidden" name="orderNumber" value="<?php echo $OrderNum; ?>">

                <!-- Part 1: Supplier, Delivery, Order Number, Reference, Date, Delivery Date -->
                <div class="form-group row py-1">
                    <label for="Supplier" class="col-sm-1 col-form-label"><span class="S-required">* </span>Supplier:</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="Supplier" name="Supplier" required>
                            <?php
                           
                            // show Supplier selected
                            $result2->data_seek(0); 
                            if ($result2->num_rows > 0) {
                                while ($supplierRow = $result2->fetch_assoc()) {
                                    if ($supplierRow["Supplier_ID"] == $supplier) {
                                        echo '<option value="' . $supplierRow["Supplier_ID"] . '" selected>' . $supplierRow["Company_Name"] . '</option>';
                                    } else {
                                        echo '<option value="' . $supplierRow["Supplier_ID"] . '">' . $supplierRow["Company_Name"] . '</option>';
                                    }
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
                            <?php

                            // show Warehouse selected
                            $result1->data_seek(0); 
                            if ($result1->num_rows > 0) {
                                while ($warehouseRow = $result1->fetch_assoc()) {
                                    if ($warehouseRow["Warehouse_ID"] == $warehouse) {
                                        echo '<option value="' . $warehouseRow["Warehouse_ID"] . '" selected>' . $warehouseRow["Warehouse_Name"] . '</option>';
                                    } else {
                                        echo '<option value="' . $warehouseRow["Warehouse_ID"] . '">' . $warehouseRow["Warehouse_Name"] . '</option>';
                                    }
                                }
                            } else {
                                echo '<option value="">There are no Deliveries available</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row py-1">
                    <label for="reference" class="col-sm-1 col-form-label"><span class="S-required">* </span>Reference:</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="reference" name="reference" autocomplete="off" value="<?php echo $reference; ?>">
                    </div>
                </div>

                <div class="form-group row py-1">
                    <label for="date" class="col-sm-1 col-form-label"><span class="S-required">* </span>Date:</label>
                    <div class="col-sm-2">
                        <input type="date" class="form-control" id="date" name="date" required autocomplete="off" value="<?php echo $order_date; ?>">
                    </div>

                    <label for="deliverydate" class="col-sm-1 col-form-label"><span class="S-required">* </span>Delivery Date:</label>
                    <div class="col-sm-2">
                        <input type="date" class="form-control" id="deliverydate" name="deliverydate" required autocomplete="off" value="<?php echo $delivery_date; ?>">
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
                        <?php
                          
                            if ($result4->num_rows > 0) {
                                while ($row = $result4->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td><select class="form-control" name="item[]" required>';
                                    $result3->data_seek(0); 
                                    if ($result3->num_rows > 0) {
                                        while ($product = $result3->fetch_assoc()) {
                                            if ($product["Product_ID"] == $row["Product_ID"]) {
                                                echo '<option value="' . $product["Product_ID"] . '" selected>' . $product["Product_Name"] . '</option>';
                                            } else {
                                                echo '<option value="' . $product["Product_ID"] . '">' . $product["Product_Name"] . '</option>';
                                            }
                                        }
                                    } else {
                                        echo '<option value="">There are no Products available</option>';
                                    }
                                    echo '</select></td>';
                                    echo '<td><input type="number" min="1" class="form-control quantity" name="quantity[]" required min="1" value="' . $row["Quantity"] . '"></td>'; // Amount
                                    echo '<td><input type="number" min="0" class="form-control price" name="price[]" step="any" required value="' . $row["Price"] . '"></td>'; // Price
                                    echo '<td><input type="number" class="form-control amount" name="amount[]" readonly value="' . $row["amount"] . '"></td>'; // Campo de solo lectura para el monto
                                    echo '<td><button type="button" class="btn btn-danger deleteRow">Delete</button></td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="5">There are no Items available</td></tr>';
                            }
                        ?>

                    </tbody>
                </table>

                <!-- Button to add more items dynamically -->
                <div class="form-group row">
                    <div class="col-sm-10 py-3">
                        <button type="button" class="btn btn-primary" id="addItemBtn">Add Item</button>
                        <button type="submit" class="btn btn-success" id="saveBtn">Save Changes</button>
                        <a href="./Frm_Resu_order.php" class="btn btn-secondary">Cancel</a> 
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
                    html += '<td><input type="number" class="form-control quantity" name="quantity[]" required min="1" value="1"></td>'; // Minimum quantity set to 1
                    html += '<td><input type="number" class="form-control price" name="price[]" step="any" required></td>'; // Price
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
                            url: 'get_product_price.php', // Get product price
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

            function confirmSend(status) {
                if (confirm("Are you sure you want to send the Order to Supplier?")) {
                    setStatusAndSubmit(status);
                } else {
                    
                }
            }

            function confirmDelete(status) {
                if (confirm("Are you sure you want to cancel the Order?")) {
                    setStatusAndSubmit(status);
                } else {
                    
                }
            }


</script>

</body>

</html>
