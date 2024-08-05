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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract and sanitize POST data
    $orderNumber = $_POST['orderNumber'] ?? '';
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
        // Delete existing details_orders for the given orderNumber
        $deleteDetailsSql = "DELETE FROM details_orders WHERE Order_Number = '$orderNumber'";
        $conn->query($deleteDetailsSql);

        // Update orders table
        $updateOrderSql = "UPDATE orders 
                           SET Order_Date = '$date',
                               Warehouse_ID = '$warehouse',
                               Supplier_id = '$supplier',
                               Reference = '$reference',
                               Delivery_Date = '$deliverydate'
                           WHERE Order_Number = '$orderNumber'";

        $conn->query($updateOrderSql);

        // Insert new details_orders records for the updated order
        foreach ($items as $key => $item) {
            $productId = intval($item);
            $quantity = intval($quantities[$key]);
            $price = floatval($prices[$key]);
            $amount = floatval($amounts[$key]);

            // Validate if Product_ID exists in Products table
            $checkProductSql = "SELECT COUNT(*) AS count FROM Products WHERE Product_ID = '$productId'";
            $result = $conn->query($checkProductSql);
            $row = $result->fetch_assoc();
            $productExists = ($row['count'] > 0);

            if (!$productExists) {
                throw new Exception("Product with ID $productId does not exist.");
            }

            // Insert into details_orders table
            $insertItemSql = "INSERT INTO details_orders (Order_Number, Product_ID, Quantity, Price, amount) 
                              VALUES ('$orderNumber', '$productId', '$quantity', '$price', '$amount')";

            
            //echo $insertItemSql;
            //die ('');            

            $conn->query($insertItemSql);
        }

        // Commit transaction
        $conn->commit();

        $_SESSION['message'] = "Purchase order <b> $orderNumber </b> Updated Successfully.";
	echo '<script type="text/javascript">window.location.href = "./Frm_Resu_order.php";</script>';
        exit();

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo "<script>alert('Error Editing purchase order: " . $e->getMessage() . "'); window.location.href = './Frm_Resu_order.php';</script>";
        exit;
    }

} else {
    // If not submitted via POST, handle accordingly
    echo "<script>alert('Form submission method not allowed.'); window.location.href = './Frm_Resu_order.php';</script>";
    exit;
}
?>
