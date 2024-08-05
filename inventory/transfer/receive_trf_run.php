<?php


session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no ha iniciado sesión, redirige a la página de inicio de sesión
    header("Location: ../../login.php"); 
    exit;
}

include '../../inc/functions.php';
$conn = connect();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['Transaction']) && $_POST['Transaction'] === 'TRF') {
        if (!empty($_POST['transferNum'])) {
            // Sanitizar y extraer datos del POST
            $transactionnumber = $_POST['transferNum'];
            $transactiontype = $_POST['Transaction'];
            $receiveddate = $_POST['today'];
            $originid = $_POST['WhoriginID'];
            $destination = intval($_POST['WhdeliveryID']);

            // Iniciar transacción
            $conn->begin_transaction();

            try {
            
                // Eliminar registros existentes en details_transfer
                $deleteDetailsSql = "DELETE FROM details_transfer WHERE Transfer_Number = '$transactionnumber'";
                if ($conn->query($deleteDetailsSql) === FALSE) {
                    throw new Exception("Error deleting existing details_Transfers: " . $conn->error);
                }
            
                // Insertar nuevos registros en details_orders
                foreach ($_POST['Quantity_End'] as $productID => $quantityend) {
                    // Sanitize inputs
                    $productid = intval($productID);
                    $quantity = intval($_POST['quantity'][$productID]);
                    $price = floatval($_POST['Price'][$productID]);
                    $amount = floatval($_POST['amount'][$productID]);
                    $quantityend = intval($_POST['Quantity_End'][$productID]);
                    
                    // Insert each product into details_orders table
                    $insertItemSql = "INSERT INTO details_transfer (Transfer_Number, Product_ID, Quantity, Price, Amount, Quantity_END) 
                                      VALUES ('$transactionnumber', '$productid', '$quantity', '$price', '$amount', '$quantityend')";
                                      
                    if ($conn->query($insertItemSql) === FALSE) {
                        throw new Exception("Error inserting into details_Transfers: " . $conn->error);
                    }

                    // Query para insertar en la tabla transactions
                    $insertTransactionSql = "INSERT INTO transactions (Type, Transactions_Number, Date, Product_ID, Quantity, Price, Origin_ID, Destination_ID) 
                                             VALUES ('$transactiontype', '$transactionnumber', '$receiveddate', '$productid', '$quantityend', '$price', '$originid', '$destination')";


                    // transactions Query
                    if ($conn->query($insertTransactionSql) === FALSE) {
                        throw new Exception("Error inserting into transactions: " . $conn->error);
                    }
                }

                // Update orders table
                $updateOrderSql = "UPDATE transfers 
                                   SET Transfer_Status = 'Completed'
                                   WHERE Transfer_Number = '$transactionnumber'";
       
                //echo $updateOrderSql;               
                $conn->query($updateOrderSql);

                // Commit si todas las inserciones fueron exitosas
                $conn->commit();
                $_SESSION['message'] = "Transfer <b> $transactionnumber </b> completed Successfully";
		echo '<script type="text/javascript">window.location.href = "./Frm_Resu_trf.php";</script>';
                exit();
            } 
            catch (Exception $e) {
                // Rollback y mostrar error si ocurre una excepción
                $conn->rollback();
                $_SESSION['error'] = 'Error editing Transfer: ' . $e->getMessage();
                header("Location: ./Frm_Resu_trf.php");
                exit();
            }
        } else {
            // Manejo si falta el número de orden
            $_SESSION['error'] = "Transfer Number is required.";
            header("Location: ./Frm_Resu_trf.php");
            exit();
        }
    } else {
        // Manejo si Transaction no es 'PO'
        $_SESSION['error'] = "Transaction Type must be 'TRF'.";
        header("Location: ./Frm_Resu_trf.php");
        exit();
    }
} else {
    // Manejo si no es una solicitud POST
    $_SESSION['error'] = "Only POST requests are allowed.";
    header("Location: ./Frm_Resu_Order.php");
    exit();
}

$conn->close();
?>
