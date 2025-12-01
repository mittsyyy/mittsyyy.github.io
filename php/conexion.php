<?php
session_start();

$servidor = "localhost";
$usuario_db = "root";
$password_db = "";
$base_datos = "sneakerx";

$conexion = new mysqli($servidor, $usuario_db, $password_db, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");

function obtenerDetallesSneaker($conexion, $id_tenis) {
    $stmt = $conexion->prepare("SELECT * FROM sneaker WHERE id_Tenis = ?");
    $stmt->bind_param("i", $id_tenis);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $sneaker = $resultado->fetch_assoc();
    $stmt->close();
    return $sneaker;
}
function obtenerTodosSneakers($conexion) {
    $query = "SELECT * FROM sneaker WHERE stock > 0 ORDER BY fecha_creacion DESC";
    $resultado = $conexion->query($query);
    $sneakers = [];
    while ($row = $resultado->fetch_assoc()) {
        $sneakers[] = $row;
    }
    return $sneakers;
}
function contarItemsCarrito() {
    if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        return 0;
    }
    $total = 0;
    foreach ($_SESSION['carrito'] as $item) {
        $total += $item['cantidad'];
    }
    return $total;
}
?>
