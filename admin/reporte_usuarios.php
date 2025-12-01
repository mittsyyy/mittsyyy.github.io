<?php
require_once 'config.php';
verificarAdmin();

$stmt = $conexion->prepare("CALL sp_reporte_usuarios_mes()");
$stmt->execute();
$resultado = $stmt->get_result();

$usuarios = [];
while ($row = $resultado->fetch_assoc()) {
    $usuarios[] = $row;
}

header('Content-Type: application/json');
echo json_encode($usuarios);
$conexion->close();
?>