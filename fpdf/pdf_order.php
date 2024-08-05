<?php
session_start();
require('fpdf.php'); 

include '../PHPMailer/PHPMailer/Exception.php';
include '../PHPMailer/PHPMailer/PHPMailer.php';
include '../PHPMailer/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

// Include the functions file for database connection
include '../inc/functions.php';

$today = date('d-m-Y, H:i:s');  
$Logo_company = "./Logo.jpg";
$company_mail = "company@gmail.com";
$company = "Company 1";

// Initialize variables
$OrderNum = isset($_POST['Ordernumber']) ? $_POST['Ordernumber'] : 0;

if ($OrderNum) {
    ob_start(); // Start output buffering

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
                        s.Email,
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
        $warehouse_Address = $row["Address"];
        $warehouse_City = $row["City"];
        $warehouse_Phone = $row["Phone"];
        $responsable = $row["Responsable"];
        $supplier = $row["Company_Name"];
        $emailsupplier = $row["Email"];
        $reference = $row["Reference"];
        $delivery_date = $row["Delivery_Date"];

        // Query to retrieve order details
        $sql_Orderdetails = "SELECT 
                                od.Order_Number, 
                                od.Product_ID, 
                                p.Product_Name,
                                od.Quantity, 
                                od.Price, 
                                od.amount 
                            FROM 
                                details_orders od 
                                JOIN Products p ON p.Product_Id = od.Product_ID
                            WHERE 
                                od.Order_Number = '$OrderNum'
                            ORDER BY od.Product_ID";

        $result4 = $conn->query($sql_Orderdetails);

        // Create PDF
        class PDF extends FPDF
        {
            function Header()
            {
                $this->Image('Logo.jpg', 10, 8, 30); // Path, X position, Y position, Width
                $this->SetFont('Arial', 'B', 12);
                $this->Cell(0, 10, 'Purchase Order', 0, 1, 'C');
                $this->Ln(10);
            }

            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 8);
                $this->Cell(0, 10, 'Page '.$this->PageNo().'/{nb}', 0, 0, 'C');
                $this->SetY(-10);
                $this->MultiCell(0, 10, 'Note: This Purchase Order must be completed within 5 days. For any inquiries, please contact Us.', 0, 'C');
            }

            function ImprovedTable($header, $data)
            {
                $this->SetFont('Arial', 'B', 12);

                // Set header background color
                $this->SetFillColor(100, 100, 255); // RGB color for header background
                $this->SetTextColor(255, 255, 255); // RGB color for header text
                $this->SetDrawColor(0, 0, 0); // RGB color for borders
                $this->SetLineWidth(0.3);

                foreach ($header as $col) {
                    $this->Cell(40, 10, $col, 1, 0, 'C', true);
                }
                $this->Ln();

                // Reset color for body
                $this->SetFillColor(255, 255, 255); // RGB color for body background
                $this->SetTextColor(0, 0, 0); // RGB color for body text
                $this->SetFont('Arial', '', 12);

                foreach ($data as $row) {
                    foreach ($row as $col) {
                        $this->Cell(40, 10, $col, 1, 0, 'C', true);
                    }
                    $this->Ln();
                }
            }
        }

        // Create instance of FPDF
        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();

        // Add content
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Order Number: ' . $Order_number, 0, 1, 'L');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Order Date: ' . $order_date, 0, 1, 'L');
        $pdf->Cell(0, 10, 'Order Status: ' . $order_status, 0, 1, 'L');
        $pdf->Cell(0, 10, 'Supplier: ' . $supplier, 0, 1, 'L');
        $pdf->Cell(0, 10, 'Reference: ' . $reference, 0, 1, 'L');
        $pdf->Cell(0, 10, 'Delivery Date: ' . $delivery_date, 0, 1, 'L');
        $pdf->Cell(0, 10, 'Delivery Address: ' . $warehouse_Address . ', ' . $warehouse_City, 0, 1, 'L');
        $pdf->Cell(0, 10, 'Contact: ' . $responsable . ', Phone: ' . $warehouse_Phone, 0, 1, 'L');
        $pdf->Ln(10);

        // Table headers
        $header = array('Item', 'Quantity', 'Price', 'Amount');
        $data = array();
        if ($result4->num_rows > 0) {
            $total = 0;
            while ($row = $result4->fetch_assoc()) {
                $data[] = array(
                    $row["Product_Name"],
                    $row["Quantity"],
                    '$' . $row["Price"],
                    '$' . $row["amount"]
                );
                $total += $row["amount"];
            }
            $data[] = array('Subtotal', '', '', '$' . number_format($total, 2));
            $data[] = array('Tax', '', '', '$0.00'); // Add tax logic if needed
            $data[] = array('Total', '', '', '$' . number_format($total, 2));
        }

        $pdf->ImprovedTable($header, $data);

        // Output PDF to the browser
       $pdf->Output('D', 'Order_' . $Order_number . '.pdf');


        ob_end_clean(); // Clear the output buffer and end buffering

    } else {
        ob_end_clean();
        echo 'Order not found.';
    }
    $conn->close();

} else {
    echo 'Order number not provided.';
}
?>
