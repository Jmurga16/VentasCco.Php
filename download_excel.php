<?php
ob_clean();
error_reporting(0);

require 'vendor/autoload.php'; // Incluye PhpSpreadsheet
include 'db.php'; // Conexión a la base de datos

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Obtener los parámetros de filtro
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$pos_group = isset($_GET['pos_group']) ? $_GET['pos_group'] : '';

// Crear una nueva hoja de cálculo
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Títulos de las columnas
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Date');
$sheet->setCellValue('C1', 'Time');
$sheet->setCellValue('D1', 'POS Group');
$sheet->setCellValue('E1', 'Total Net Revenue');
$sheet->setCellValue('F1', 'Total VAT');
$sheet->setCellValue('G1', 'Total Due Amount');

// Construir la consulta con los filtros
$sql = "SELECT ID, Date, Time, `POS group`, `Total net revenue`, `Total VAT`, `Total due amount` 
        FROM tickets_ventas WHERE 1=1";

if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $sql .= " AND DATE(Date) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}

if (!empty($pos_group)) {
    $sql .= " AND `POS group` = '$pos_group'";
}

$sql .= " ORDER BY Date";

// Ejecutar la consulta
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $rowIndex = 2; // Inicia desde la segunda fila
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue("A{$rowIndex}", $row['ID']);
        $sheet->setCellValue("B{$rowIndex}", $row['Date']);
        $sheet->setCellValue("C{$rowIndex}", $row['Time']);
        $sheet->setCellValue("D{$rowIndex}", $row['POS group']);
        $sheet->setCellValue("E{$rowIndex}", $row['Total net revenue']);
        $sheet->setCellValue("F{$rowIndex}", $row['Total VAT']);
        $sheet->setCellValue("G{$rowIndex}", $row['Total due amount']);
        $rowIndex++;
    }
}

// Antes de los headers
if (ob_get_length()) ob_end_clean();

// Configurar encabezados para la descarga
$filename = 'filtered_tickets.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Enviar el archivo al navegador
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
