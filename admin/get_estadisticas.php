<?php
require_once 'config.php';
verificarAdmin();

$stmt = $conexion->prepare("CALL sp_estadisticas_generales()");
$stmt->execute();
$resultado = $stmt->get_result();
$estadisticas = $resultado->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($estadisticas);
$conexion->close();
?>