<?php    
ob_start();

// Include the functions file for database connection
include '../inc/functions.php';
$today = date('d-m-Y, H:i:s');  
$Logo_transfer = "./Transfer.jpeg";
$company_mail = "company@xyz.com";

// Initialize variables
$TransferNum = isset($_POST['Transfernumber']) ? $_POST['Transfernumber'] : 0;

// Check if Transfernumber is provided via POST
if (isset($_POST['Transfernumber'])) {
    $TransferNum = $_POST['Transfernumber'];
    $conn = connect(); 

    // Query to retrieve Transfer
    $sql_TransferNro = "SELECT 
                            t.Transfer_Number, 
                            t.Transfer_Date,
                            t.Transfer_Status,
                            t.Delivery_Date,
                            t.Warehouse_Destination AS WhOriginID,
                            t.Warehouse_Destination AS WhdeliveryID,
                            w_origin.Warehouse_Name AS whOrigin, 
                            w_dest.Warehouse_Name AS Destination,
                            CONCAT(u.FirstName, ' ', u.LastName) AS Responsable,
                            w_dest.Phone,
                            u.username
                        FROM 
                            transfers t
                        JOIN 
                            Warehouses w_dest ON t.Warehouse_Destination = w_dest.Warehouse_ID 
                        JOIN 
                            user u ON t.User_ID_Responsable = u.user_ID
                        JOIN 
                            Warehouses w_origin ON t.Warehouse_Origin = w_origin.Warehouse_ID 
                        JOIN 
                            Warehouses w_manager ON w_manager.wh_user_manager_id = u.user_id 
                        WHERE 
                            t.Transfer_Number = '$TransferNum'";

 
    $result = $conn->query($sql_TransferNro);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $Transfer_number = $row["Transfer_Number"];
        $transfer_date = $row["Transfer_Date"];
        $transfer_status = $row["Transfer_Status"];
        $warehouseOrigin = $row["whOrigin"];
        $warehouseDestin = $row["Destination"];
        $responsable = $row["Responsable"];
        $delivery_date = $row["Delivery_Date"];

        // Queries for dropdowns
        $sql_Products = "SELECT * FROM Products"; 
        $result3 = $conn->query($sql_Products); 

        // Query to retrieve Transfer details
        $sql_Transferdetails = "SELECT 
                                    od.Transfer_Number, 
                                    od.Product_ID, 
                                    od.Quantity, 
                                    od.Price, 
                                    od.amount 
                                FROM 
                                    details_transfer od 
                                WHERE 
                                    od.Transfer_Number = '$TransferNum'";        
        $result4 = $conn->query($sql_Transferdetails);
    } else {
        $TransferNum = 0;
    }
} else {
    // Redirect if Transfernumber is not provided via POST
    header("Location: ../inventory/transfer/Frm_Resu_trf.php");
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
    <title>Pdf Transfer</title>

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
        .S-required {
            color: red;
            
        }

    </style>
</head>

<body>


<div class="container-fluid">

    <!-- Main content area -->
    <div class="S-main-content">
        <div>      
            <!-- Form for adding items -->
            <form id="editItemForm" style="width: 100%;" enctype="multipart/form-data"> 

            <table class="table">
                <tr class="form-group">
                    <td colspan="2" class="text-center"><span class="S-required">
                        <img src="<?php echo $Logo_transfer; ?>" width="230" height="auto">
                        <spam><?php echo $company_mail; ?></spam>
                    </td>
                        
                    <td colspan="4">
                            <h3>Transfer Requirement <span><?php echo ''.'<p class="S-required "># '. $TransferNum.'</p>';?> <span style="color: black;"></h3>Status: 
                            <?php echo $transfer_status;?></span> <br>Document Date: <?php echo $today;?>
                    </td>
                </tr>
           
                <tbody>
                    <tr class="form-group">
                        <td><span class="S-required"> </span><b> Origin Warehouse: </b></td>
                        <td>
                            <input type="text" class="form-control  form-control-sm" id="WhOrigin" name="WhOrigin" autocomplete="off" value="<?php echo $warehouseOrigin; ?>">
                        </td>

                        <td><span class="S-required"> </span><b> Destination Warehouse: </b></td>
                        <td>
                            <input type="text" class="form-control  form-control-sm" id="WhDelivey" name="WhDelivey" autocomplete="off" value="<?php echo $warehouseDestin; ?>">
                        </td>

                    </tr>

                    <tr class="form-group">
                        <td class="col-sm-2 col-form-label"><span class="S-required"> </span>Transfer Date:</td>
                        <td class="col-sm-3">
                            <input type="date" class="form-control form-control-sm" id="date" name="date" required autocomplete="off" value="<?php echo $transfer_date; ?>">
                        </td>
                        <td class="col-sm-2 col-form-label"><span class="S-required"> </span><b>Delivery Date: </b></td>
                        <td class="col-sm-3">
                            <input type="date" class="form-control form-control-sm" id="deliverydate" name="deliverydate" required autocomplete="off" value="<?php echo $delivery_date; ?>">
                        </td>
                    </tr>

                </tbody>
            </table>  
                    
                <!-- Table to display dynamically added items -->
                <table id="numberTable" class="S-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                          
                            if ($result4->num_rows > 0) {
                                $total=0;
                                $tax=0;
                                while ($row = $result4->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>';
                                    $result3->data_seek(0); 
                                    if ($result3->num_rows > 0) {
                                        while ($product = $result3->fetch_assoc()) {
                                            if ($product["Product_ID"] == $row["Product_ID"]) {
                                                echo '<input type="text" class="form-control-plaintext" name="product_name" value="' . $product["Product_Name"] . '" readonly>';
                                            } else {
                                                // Aquí podrías añadir una lógica alternativa si no se debe mostrar ningún input
                                            }
                                        }
                                    } else {
                                        echo '<p>No products available.</p>';
                                    }
                                    
                                    echo '</td>';
                                    echo '<td><input style="text-align: center;" type="number" min="1" class="form-control-plaintext" name="quantity[]" required min="1" value="' . $row["Quantity"] . '"></td>'; // Amount
                                    echo '<td><input style="text-align: center;" type="text" class="form-control-plaintext" name="price[]" value="$' . $row["Price"] . '"></td>'; // Price
                                    echo '<td><input style="text-align: center;" type="text" class="form-control-plaintext" name="amount[]" value="$' . $row["amount"] . '"></td>';

                                    echo '</tr>';
                                    
                                    $total=$row["amount"]+$total;
                                }
                                echo '<td colspan="3">';
                                    echo "Subtotal";
                                echo '</td>';

                                echo '<td style="text-align: center; font-size: 16px; font-weight: bold;">';
                                    echo "$".number_format($total, 2);
                                echo '</td>';
                                //
                                echo '<tr>';
                                    echo '<td colspan="3">';
                                        echo "Tax";
                                    echo '</td>';
                                    echo '<td style="text-align: center; font-size: 16px; font-weight: bold;">';
                                        echo number_format($tax, 2);
                                    echo '</td>';
                                echo '</tr>';

                                //
                                echo '<tr>';
                                echo '<td colspan="3">';
                                    echo "Total";
                                echo '</td>';
                                echo '<td style="text-align: center; font-size: 16px; font-weight: bold;">';
                                    echo "$".number_format($total+$tax, 2);
                                echo '</td>';
                            echo '</tr>';

                            } else {
                                echo '<tr><td colspan="5">There are no Items available</td></tr>';
                            }
                        ?>

                    </tbody>
                </table>
            </form>
            <!-- End form -->

            <!-- Footer -->
            <footer class="small text-muted py-3">
                <p>Note: * This Transfer Transfer has a maximum of 5 days to be completed, in case of any news, please contact Us.</p>
            </footer>
            <!-- End footer -->
        </div>
    </div>
    <!-- End main content area -->
</div>

<script>
            
            function ImageToDataUrl(String $filename) : String {
    if(!file_exists($filename))
        throw new Exception('File not found.');
    
    $mime = mime_content_type($filename);
    if($mime === false) 
        throw new Exception('Illegal MIME type.');

    $raw_data = file_get_contents($filename);
    if(empty($raw_data))
        throw new Exception('File not readable or empty.');
    
    return "data:{$mime};base64," . base64_encode($raw_data);
}
</script>

</body>
</html>

<?php

    // almacenar el contenido HTML en variable
    $html = ob_get_clean();

    //echo $html;
    require_once '../libraries/dompdf/autoload.inc.php';

    use Dompdf\Dompdf;
    use Dompdf\Options;
    $dompdf = new Dompdf();

    $options = new Options();
    $options->set('chroot', realpath('./')); 
    $options->set(array('isRemoteEnabled' => true));

    $dompdf = new Dompdf($options);

    // cargar el contenido HTML generado
    $dompdf->loadHtml($html);

    // establecer el formato del papel
    $dompdf->setPaper('letter');
    //$dompdf->setPaper('A4', 'portrait');

    // renderizar el PDF
    $dompdf->render();

    // mostrar el PDF en el navegador con opción de descarga
    $dompdf->stream($TransferNum.'pdf', array('Attachment' => false));

?>

