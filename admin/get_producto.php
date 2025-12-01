<?php
require_once 'config.php';
verificarAdmin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conexion->prepare("SELECT * FROM sneaker WHERE id_Tenis = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$producto = $resultado->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($producto);
$conexion->close();
?>