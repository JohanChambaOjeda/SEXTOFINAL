<?php
require('fpdf/fpdf.php'); // Asegúrate de que la ruta sea correcta

class PDF extends FPDF {
    // Cabecera de página
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Factura', 0, 1, 'C');
        $this->Ln(10);
    }

    // Pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Datos de ejemplo
$invoiceData = [
    'invoice_number' => '001',
    'date' => '2024-09-15',
    'customer_name' => 'Juan Pérez',
    'items' => [
        ['description' => 'Producto 1', 'quantity' => 2, 'price' => 25.00],
        ['description' => 'Producto 2', 'quantity' => 1, 'price' => 45.00],
    ],
    'total' => 95.00
];

// Imprimir datos de factura
$pdf->Cell(0, 10, 'Número de Factura: ' . $invoiceData['invoice_number'], 0, 1);
$pdf->Cell(0, 10, 'Fecha: ' . $invoiceData['date'], 0, 1);
$pdf->Cell(0, 10, 'Cliente: ' . $invoiceData['customer_name'], 0, 1);
$pdf->Ln(10);

$pdf->Cell(80, 10, 'Descripción', 1);
$pdf->Cell(30, 10, 'Cantidad', 1);
$pdf->Cell(30, 10, 'Precio', 1);
$pdf->Ln();

foreach ($invoiceData['items'] as $item) {
    $pdf->Cell(80, 10, $item['description'], 1);
    $pdf->Cell(30, 10, $item['quantity'], 1);
    $pdf->Cell(30, 10, number_format($item['price'], 2), 1);
    $pdf->Ln();
}

$pdf->Cell(110, 10, 'Total', 1);
$pdf->Cell(30, 10, number_format($invoiceData['total'], 2), 1);

$pdf->Output();
?>
