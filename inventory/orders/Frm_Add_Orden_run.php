<?php

session_start(); 

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no ha iniciado sesión, redirige a la página de inicio de sesión
    header("Location: ../../login.php"); 
    exit;
}

include '../../inc/functions.php';

$userSystem =$_SESSION['FullName'];
$UserLogin=$_SESSION['username'];
$UserID=$_SESSION['UserID'];
$warehouseName= $_SESSION['WarehouseName'];

// Parameters from Frm_Add.prd

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract and sanitize POST data
    $orderNumber = $_POST['order'] ?? '';
    $date = $_POST['date'] ?? '';
    $warehouse = $_POST['delivery'] ?? '';
    $supplier = $_POST['Supplier'] ?? '';
    $reference = $_POST['reference'] ?? '';
    $deliverydate = $_POST['deliverydate'] ?? '';
    
    // Items data
    $items = $_POST['item'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    $prices = $_POST['price'] ?? [];
    $amounts = $_POST['amount'] ?? [];



    // Connect to the database
    $conn = connect();

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert into orders table
        $insertOrderSql = "INSERT INTO orders (Order_Number, Order_Date, Order_Status, Warehouse_ID, User_ID_Responsable, Supplier_ID, Reference, Delivery_Date)
                           VALUES ('$orderNumber', '$date', 'Open', '$warehouse', '$UserID', '$supplier', '$reference', '$deliverydate')";

                           //echo $insertOrderSql;

        $conn->query($insertOrderSql);

        // Get the last inserted order_id
        $order_id = $conn->insert_id;

        // Insert into details_orders table for selected items
        $insertItemSql = "INSERT INTO details_orders (Order_Number, Product_ID, Quantity, Price, amount) VALUES ";
       
        $first = true; // Flag to manage commas in the query

        // Iterate over items, quantities, prices, and amounts arrays
        $numItems = count($items);
        for ($i = 0; $i < $numItems; $i++) {
            $productId = intval($items[$i]);
            $quantity = intval($quantities[$i]);
            $price = intval($prices[$i]);
            $amount = intval($amounts[$i]);

            // Validate if Product_ID exists in Products table
            $checkProductSql = "SELECT COUNT(*) AS count FROM Products WHERE Product_ID = '$productId'";
            $result = $conn->query($checkProductSql);
            $row = $result->fetch_assoc();
            $productExists = ($row['count'] > 0);

            if (!$productExists) {
                throw new Exception("Product with ID $productId does not exist.");
            }

            // Add values to the SQL query
            if (!$first) {
                $insertItemSql .= ", ";
            } else {
                $first = false;
            }

            // Use $orderNumber and validated $productId
            $insertItemSql .= "('$orderNumber', '$productId', '$quantity', '$price', '$amount')";
            //echo $insertItemSql;
        }

        // Execute the insert statement for items
        
            if ($conn->query($insertItemSql) === TRUE) {
      
                $_SESSION['message'] = 'Order created successfully.';
            } else {
                // Error: Guardar mensaje de error en sesión
                $_SESSION['error'] = 'Error inserting product: ' . $conn->error;
            }

        // Commit transaction
        $conn->commit();
        
       

     
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo "<script>
            $(document).ready(function(){
                $('#notificationModal .modal-body').text('Error adding purchase order: " . $e->getMessage() . "');
                $('#notificationModal').modal('show');
            });
        </script>";
        exit;
    }

} else {
    // If not submitted via POST, handle accordingly
    echo "<script>
        $(document).ready(function(){
            $('#notificationModal .modal-body').text('Form submission method not allowed.');
            $('#notificationModal').modal('show');
        });
    </script>";
    exit;
}

 // Close database connection
 $conn->close();
// Redireccionar después de cerrar la conexión y haber manejado las sesiones
echo '<script type="text/javascript">window.location.href = "./Frm_Add_order.php";</script>';
exit();

?>
