<?php
require_once 'config.php';
verificarAdmin();

$data = json_decode(file_get_contents('php://input'), true);

$stmt = $conexion->prepare("CALL sp_actualizar_sneaker(?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssssdi",
    $data['id'],
    $data['nombre'],
    $data['marca'],
    $data['modelo'],
    $data['colores'],
    $data['descripcion'],
    $data['precio'],
    $data['stock']
);

if ($stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Producto actualizado exitosamente'
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar producto: ' . $conexion->error
    ]);
}

$conexion->close();
?>