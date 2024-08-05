<?php
// Iniciar el buffer de salida
ob_start();

// Incluir la clase FPDF
require('./fpdf/fpdf.php');

// Crear el documento PDF
$pdf = new FPDF();
$pdf->AddPage();

// Establecer la fuente y agregar texto
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Generar archivos PDF con PHP', 0, 1);

// Salida del archivo PDF
$pdf->Output('', 'basic.pdf');

// Limpiar y terminar el buffer de salida
ob_end_clean();
?>
