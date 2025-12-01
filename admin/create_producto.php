<?php
require_once 'config.php';
verificarAdmin();

$data = json_decode(file_get_contents('php://input'), true);

$stmt = $conexion->prepare("CALL sp_crear_sneaker(?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssdisd",
    $data['nombre'],
    $data['marca'],
    $data['modelo'],
    $data['colores'],
    $data['descripcion'],
    $data['precio'],
    $data['stock'],
    $data['fecha'],
    $data['imagen']
);

if ($stmt->execute()) {
    $resultado = $stmt->get_result();
    $row = $resultado->fetch_assoc();
    $id_nuevo = $row['id_nuevo'];
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Producto creado exitosamente',
        'id' => $id_nuevo
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error al crear producto: ' . $conexion->error
    ]);
}

$conexion->close();
?>