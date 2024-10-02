<?php
require 'db.php'; 
require 'vendor/autoload.php';
use setasign\Fpdf\Fpdf;

class PDF extends Fpdf
{
    // Encabezado del PDF
    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Reporte de Pedidos - UVG-Shop', 0, 1, 'C');
        $this->Ln(10);
    }

    // Pie de página del PDF
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Tabla de pedidos
    function PedidoTable($header, $data)
    {
        $w = array(20, 50, 50, 30);

        $this->SetFont('Arial', 'B', 12);
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
        }
        $this->Ln();

        $this->SetFont('Arial', '', 10);
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row['id'], 1);
            $this->Cell($w[1], 6, $row['cliente_username'], 1);
            $this->Cell($w[2], 6, $row['estado'], 1);
            $this->Cell($w[3], 6, $row['fecha'], 1);
            $this->Ln();
        }
    }
}

// Crear PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// Títulos de la tabla
$header = array('ID', 'Cliente', 'Estado', 'Fecha');

// Consultar pedidos de la base de datos
$sql = "SELECT id, cliente_username, estado, fecha FROM pedidos";
$result = $conn->query($sql);

if ($result === false) {
    die('Error en la consulta SQL: ' . $conn->error);
}

// Almacenar datos
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Crear la tabla en el PDF
$pdf->PedidoTable($header, $data);

// Descargar el PDF
$pdf->Output('D', 'pedidos_reporte.pdf');
?>
