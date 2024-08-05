<?php
require('fpdf.php');

// Include the functions file for database connection
include '../inc/functions.php';


// Initialize variables
$TransferNum = isset($_POST['Transfernumber']) ? $_POST['Transfernumber'] : 0;

if ($TransferNum) {
    $conn = connect(); 

    // Query to retrieve Transfer
    $sql_TransferNro = "SELECT 
    t.Transfer_Number, 
    t.Transfer_Date, 
    t.Transfer_Status, 
    t.Delivery_Date, 
    t.Warehouse_Origin AS WhOriginID,
    w_orig.Warehouse_Name AS whOrigin, 
    t.Warehouse_Destination AS WhDeliveryID, 
    w_dest.Warehouse_Name AS Destination,
    CONCAT(u.FirstName, ' ', u.LastName) AS Responsable, 
    u.username 
FROM 
    transfers t 
JOIN 
    user u ON t.User_ID_Responsable = u.user_ID 
JOIN 
    Warehouses w_orig ON t.Warehouse_Origin = w_orig.Warehouse_ID
JOIN 
    Warehouses w_dest ON t.Warehouse_Destination = w_dest.Warehouse_ID
WHERE t.Transfer_Number='$TransferNum'";


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
	$warehouse_Phone = $row["Phone"];


        // Query to retrieve order details
        $sql_Orderdetails = "SELECT 
                                od.Transfer_Number,
                                od.Product_ID, 
				p.Product_Name,
                                od.Quantity, 
                                od.Price, 
                                od.amount 
                            FROM 
                                details_transfer od 
			    JOIN Products p ON p.Product_Id=od.Product_ID
                            WHERE 
                                od.Transfer_Number = '$TransferNum'
			    ORDER BY od.Product_ID";

        $result4 = $conn->query($sql_Orderdetails);

        // Create PDF
        class PDF extends FPDF
        {
            function Header()
            {
		$this->Image('Transfer.jpeg', 10, 8, 30); // Path, X position, Y position, Width
                $this->SetFont('Arial', 'B', 12);
                $this->Cell(0, 10, 'Transfer Requirements', 0, 1, 'C');
                $this->Ln(10);
            }

            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 8);
                $this->Cell(0, 10, 'Page '.$this->PageNo().'/{nb}', 0, 0, 'C');
		$this->SetY(-10);
		$this->MultiCell(0, 10, 'Note: This Transfer must be completed within 5 days. For any inquiries, please contact Us.', 0, 'C');
            }

            function ChapterTitle($title)
            {
                $this->SetFont('Arial', 'B', 12);
                $this->Cell(0, 10, $title, 0, 1, 'L');
                $this->Ln(4);
            }

            function ChapterBody($body)
            {
                $this->SetFont('Arial', '', 12);
                $this->MultiCell(0, 10, $body);
                $this->Ln();
            }

            function ImprovedTable($header, $data)
            {
                $this->SetFont('Arial', 'B', 12);

		// Set header background color
                $this->SetFillColor(100, 100, 255); // RGB color for header background
                $this->SetTextColor(255, 255, 255); // RGB color for header text
                $this->SetDrawColor(0, 0, 0); // RGB color for borders
                $this->SetLineWidth(0.3);

                foreach($header as $col) {
                    $this->Cell(40, 10, $col, 1, 0, 'C', true);
                }
                $this->Ln();

                // Reset color for body
                $this->SetFillColor(255, 255, 255); // RGB color for body background
                $this->SetTextColor(0, 0, 0); // RGB color for body text
                $this->SetFont('Arial', '', 12);

                foreach($data as $row) {
                    foreach($row as $col) {
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
        $pdf->Cell(0, 10, 'Transfer Number: ' . $Transfer_number, 0, 1, 'L');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Document Date: ' . $transfer_date, 0, 1, 'L');
	$pdf->SetTextColor(255, 0, 0); // RGB color for red
        $pdf->Cell(0, 10, 'Status: ' . $transfer_status, 0, 1, 'L');

	// Reset color for other text
        $pdf->SetTextColor(0, 0, 0); // RGB color for black
        $pdf->Cell(0, 10, 'Origin Warehouse: ' . $warehouseOrigin, 0, 1, 'L');
        $pdf->Cell(0, 10, 'Delivery Date: ' . $delivery_date, 0, 1, 'L');
        $pdf->Cell(0, 10, 'Destination: ' . $warehouseDestin, 0, 1, 'L');
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

        // Output PDF
        $pdf->Output();

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="documento.pdf"');
$pdf->Output();

    }
    $conn->close();
} else {
    echo 'Transfer number not provided.';
}
?>
