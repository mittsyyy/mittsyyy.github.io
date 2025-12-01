<?php

require_once 'config.php';
verificarAdmin();

$query = "SELECT * FROM sneaker ORDER BY fecha_creacion DESC";
$resultado = $conexion->query($query);

$productos = [];
while ($row = $resultado->fetch_assoc()) {
    $productos[] = $row;
}

header('Content-Type: application/json');
echo json_encode($productos);
$conexion->close();
?>