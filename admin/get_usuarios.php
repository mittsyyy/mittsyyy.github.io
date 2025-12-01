<?php
require_once 'config.php';
verificarAdmin();

$query = "SELECT id_Usuario, nombre_usuario, correo, rol, fecha_registro 
          FROM usuario 
          ORDER BY fecha_registro DESC";
$resultado = $conexion->query($query);

$usuarios = [];
while ($row = $resultado->fetch_assoc()) {
    $usuarios[] = $row;
}

header('Content-Type: application/json');
echo json_encode($usuarios);
$conexion->close();
?>