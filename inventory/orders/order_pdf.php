<?php
include('../../inc/fpdf/fpdf.php');
include '../../inc/functions.php';

// Iniciar el buffer de salida
ob_start();

// Obtener el número de orden desde la URL
$order_number = $_POST['Ordernumber'];

// Consulta SQL para obtener los datos de la orden
$sql = "SELECT 
            o.Order_Number, 
            o.Order_Date, 
            o.Order_Status, 
            o.Warehouse_ID, 
            o.User_ID_Responsable, 
            o.Supplier_id, 
            o.Delivery_Date, 
            SUM(od.amount) AS total_amount 
        FROM 
            orders o 
        JOIN 
            details_orders od 
        ON 
            o.Order_Number = od.Order_Number 
        WHERE 
            o.Order_Number = '$order_number' 
        GROUP BY 
            o.Order_Number, 
            o.Order_Date, 
            o.Order_Status, 
            o.Warehouse_ID, 
            o.User_ID_Responsable, 
            o.Supplier_id, 
            o.Delivery_Date";

$conn = connect();
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $order = mysqli_fetch_assoc($result);

    // Crear el PDF
    //$pdf = new FPDF();
    //$pdf->AddPage();
    echo "Orden 5";

die('');


    // Añadir título
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40, 10, 'Order Report');

    // Añadir datos de la orden
    $pdf->SetFont('Arial', '', 12);
    $pdf->Ln(10);
    $pdf->Cell(40, 10, 'Order Number: ' . $order['Order_Number']);
    $pdf->Ln(10);
    $pdf->Cell(40, 10, 'Order Date: ' . $order['Order_Date']);
    $pdf->Ln(10);
    $pdf->Cell(40, 10, 'Order Status: ' . $order['Order_Status']);
    $pdf->Ln(10);
    $pdf->Cell(40, 10, 'Warehouse ID: ' . $order['Warehouse_ID']);
    $pdf->Ln(10);
    $pdf->Cell(40, 10, 'User ID Responsable: ' . $order['User_ID_Responsable']);
    $pdf->Ln(10);
    $pdf->Cell(40, 10, 'Supplier ID: ' . $order['Supplier_id']);
    $pdf->Ln(10);
    $pdf->Cell(40, 10, 'Delivery Date: ' . $order['Delivery_Date']);
    $pdf->Ln(10);
    $pdf->Cell(40, 10, 'Total Amount: ' . $order['total_amount']);

    // Salida del PDF
    $pdf->Output();

    // Limpiar y terminar el buffer de salida
    ob_end_clean();
} else {
    // Handle error if no data found or query failed
    echo "Error: No data found for Order Number $order_number.";
}
?>
