<?php
require_once 'config.php';
verificarAdmin();

$query = "SELECT p.*, u.nombre_usuario 
          FROM pedidos p 
          INNER JOIN usuario u ON p.id_Usuario = u.id_Usuario 
          ORDER BY p.fecha_pedido DESC";
$resultado = $conexion->query($query);

$pedidos = [];
while ($row = $resultado->fetch_assoc()) {
    $pedidos[] = $row;
}

header('Content-Type: application/json');
echo json_encode($pedidos);
$conexion->close();
?>