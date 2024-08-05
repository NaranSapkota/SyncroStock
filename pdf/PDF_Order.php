<?php    
ob_start();

    // Include the functions file for database connection
    include '../inc/functions.php';
    $today = date('d-m-Y, H:i:s');  
    $Logo_company="./logo.jpg";
    $company_mail="company@gmail.com";

    
    // Initialize variables
    $OrderNum = isset($_POST['Ordernumber']) ? $_POST['Ordernumber'] : 0;

    // Check if Ordernumber is provided via POST
    if(isset($_POST['Ordernumber'])) {
        $OrderNum = $_POST['Ordernumber'];
        $conn = connect(); 

        // Query to retrieve order
        $sql_OrderNro = "SELECT 
                        o.Order_Number, 
                        o.Order_Date, 
                        o.Order_Status, 
                        w.Warehouse_Name AS Delivery, 
                        w.Address,
                        w.City,
                        w.Province,
                        w.Phone,
                        CONCAT(u.FirstName, ' ', u.LastName) AS Responsable,
                        s.Company_Name,
                        o.Reference,
                        o.Delivery_Date 
                        FROM 
                        orders o 
                        LEFT JOIN Warehouses w ON o.Warehouse_ID = w.Warehouse_ID
                        LEFT JOIN Suppliers s ON o.Supplier_id = s.Supplier_id
                        LEFT JOIN user u ON o.User_ID_Responsable = u.user_id
                        WHERE 
                        o.Order_Number = '$OrderNum'";

       
        $result = $conn->query($sql_OrderNro);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $Order_number = $row["Order_Number"];
            $order_date = $row["Order_Date"];
            $order_status = $row["Order_Status"];
            $warehouse = $row["Delivery"];
            $warehouse_Address = $row["Address"];
            $warehouse_City = $row["City"];
            $warehouse_Province = $row["Province"];
            $warehouse_Phone = $row["Phone"];
            $responsable = $row["Responsable"];
            $supplier = $row["Company_Name"];
            $reference = $row["Reference"];
            $delivery_date = $row["Delivery_Date"];

            // Queries for dropdowns
         
            $sql_Products = "SELECT * FROM Products"; 
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
        //header("Location: ../inventory/orders/Frm_Resu_order.php");
	echo '<script>window.location.href = "../inventory/orders/Frm_Resu_order.php";</script>';
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
    <title>Pdf Order</title>

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
                    <td colspan="2"><span class="S-required">
                        <img src="./Logo.jpg" width="230" height="auto">
                    </td>
                        
                    <td colspan="4">
                            <h3>Purchase Order <span><?php echo ''.'<p class="S-required "># '. $OrderNum.'</p>';?> <span style="color: black;"></h3>Status: 
                            <?php echo $order_status;?></span> <br>Document Date: <?php echo $today;?>
                    </td>
                </tr>
           
                <tbody>
                    <tr class="form-group">
                        <td><span class="S-required"> </span>Reference:</td>
                        <td>
                            <input type="text" class="form-control form-control-sm" id="reference" name="reference" autocomplete="off" value="<?php echo $reference; ?>">
                        </td>
                        <td><span class="S-required"> </span><b> Supplier: </b></td>
                        <td>
                            <input type="text" class="form-control  form-control-sm" id="supplier" name="supplier" autocomplete="off" value="<?php echo $supplier; ?>">
                        </td>
                    </tr>

                    <tr class="form-group">
                        <td class="col-sm-2 col-form-label"><span class="S-required"> </span>Order Date:</td>
                        <td class="col-sm-3">
                            <input type="date" class="form-control form-control-sm" id="date" name="date" required autocomplete="off" value="<?php echo $order_date; ?>">
                        </td>
                        <td class="col-sm-2 col-form-label"><span class="S-required"> </span><b>Delivery Date: </b></td>
                        <td class="col-sm-3">
                            <input type="date" class="form-control form-control-sm" id="deliverydate" name="deliverydate" required autocomplete="off" value="<?php echo $delivery_date; ?>">
                        </td>
                    </tr>


                 <!------------- Part 2: Delivery ----------->
                    <tr>
                        <td colspan="1">
                            <span><h4>Delivery</h4></span>
                        </td>
                        <td span class="S-required">
                            <p>Place: <input type="text" class="form-control-plaintext form-control-sm" id="warehouse" name="warehouse" autocomplete="off" value="<?php echo $warehouse; ?>"></p>
                        </td>

                        <td ><span class="S-required"> 
                            Address: <input type="text" class="form-control-plaintext form-control-sm" id="address" name="address" autocomplete="off" value="<?php echo $warehouse_Address, $warehouse_City; ?>">
                        </td>

                        <td colspan="1" span class="S-required">
                            <p>Contact:<input type="text" class="form-control-plaintext form-control-sm" id="Contact" name="Contact" autocomplete="off" value="<?php echo $responsable . "\n" . "Phone:". $warehouse_Phone;?>"></p>
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
                <p>Note: * This Purchase Order has a maximum of 5 days to be completed, in case of any news, please contact Us, Phone: <?php echo "$warehouse_Phone, Email: $company_mail"; ?></p>
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

    Include '../libraries/dompdf/autoload.inc.php';


    use Dompdf\Dompdf;
    use Dompdf\Options;
    //$dompdf = new Dompdf();

    $options = new Options();
    $options->set('chroot', realpath('/')); 
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
    $dompdf->stream($OrderNum.'pdf', array('Attachment' => false));


?>

