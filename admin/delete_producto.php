<?php
require_once 'config.php';
verificarAdmin();

$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id']);

$stmt = $conexion->prepare("CALL sp_eliminar_sneaker(?)");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Producto eliminado exitosamente'
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error al eliminar producto: ' . $conexion->error
    ]);
}

$conexion->close();
?>