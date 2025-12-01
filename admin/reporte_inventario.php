<?php
require_once 'config.php';
verificarAdmin();

$query = "SELECT * FROM v_inventario_completo ORDER BY marca, nombre";
$resultado = $conexion->query($query);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=inventario_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');


fputcsv($output, ['ID', 'Nombre', 'Marca', 'Modelo', 'Precio', 'Stock', 'Fecha Lanzamiento', 'Nivel Stock', 'Descuento Activo']);

while ($row = $resultado->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
$conexion->close();
?>