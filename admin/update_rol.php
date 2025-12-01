<?php
require_once 'config.php';
verificarAdmin();

$data = json_decode(file_get_contents('php://input'), true);

$stmt = $conexion->prepare("UPDATE usuario SET rol = ? WHERE id_Usuario = ?");
$stmt->bind_param("si", $data['rol'], $data['id']);

if ($stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Rol actualizado exitosamente'
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar rol'
    ]);
}

$conexion->close();
?>