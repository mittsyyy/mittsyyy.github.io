<?php
require_once 'config.php';
verificarAdmin();

$fechaInicio = $_GET['inicio'];
$fechaFin = $_GET['fin'];

$stmt = $conexion->prepare("CALL sp_reporte_ventas(?, ?)");
$stmt->bind_param("ss", $fechaInicio, $fechaFin);
$stmt->execute();
$resultado = $stmt->get_result();

$ventas = [];
while ($row = $resultado->fetch_assoc()) {
    $ventas[] = $row;
}

header('Content-Type: application/json');
echo json_encode($ventas);
$conexion->close();
?>