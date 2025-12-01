<?php
require_once 'config.php';
verificarAdmin();

$query = "SELECT * FROM auditoria_sneakers ORDER BY fecha_accion DESC LIMIT 100";
$resultado = $conexion->query($query);

$auditoria = [];
while ($row = $resultado->fetch_assoc()) {
    $auditoria[] = $row;
}

header('Content-Type: application/json');
echo json_encode($auditoria);
$conexion->close();
?>