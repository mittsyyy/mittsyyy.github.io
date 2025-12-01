<?php
require_once 'config.php';
verificarAdmin();

$limite = isset($_GET['limite']) ? intval($_GET['limite']) : 10;

$stmt = $conexion->prepare("CALL sp_productos_bajo_stock(?)");
$stmt->bind_param("i", $limite);
$stmt->execute();
$resultado = $stmt->get_result();

$productos = [];
while ($row = $resultado->fetch_assoc()) {
    $productos[] = $row;
}

header('Content-Type: application/json');
echo json_encode($productos);
$conexion->close();
?>