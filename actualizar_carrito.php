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

if (!isset($_GET['accion']) || !isset($_GET['indice'])) {
    header("Location: ver_carrito.php");
    exit();
}

$accion = $_GET['accion'];
$indice = intval($_GET['indice']);

if (!isset($_SESSION['carrito'][$indice])) {
    header("Location: ver_carrito.php");
    exit();
}

$item = &$_SESSION['carrito'][$indice];
$id_tenis = $item['id_tenis'];

$stmt = $conexion->prepare("SELECT stock FROM sneaker WHERE id_Tenis = ?");
$stmt->bind_param("i", $id_tenis);
$stmt->execute();
$resultado = $stmt->get_result();
$sneaker = $resultado->fetch_assoc();
$stmt->close();

if (!$sneaker) {
    header("Location: ver_carrito.php?error=Producto no encontrado");
    exit();
}

if ($accion === 'incrementar') {
    if ($item['cantidad'] < $sneaker['stock']) {
        $item['cantidad']++;
        
        if (isset($_SESSION['usuario_id'])) {
            $id_usuario = $_SESSION['usuario_id'];
            $stmt = $conexion->prepare("UPDATE carrito SET cantidad = ? WHERE id_Usuario = ? AND id_Tenis = ?");
            $stmt->bind_param("iii", $item['cantidad'], $id_usuario, $id_tenis);
            $stmt->execute();
            $stmt->close();
        }
    }
} elseif ($accion === 'decrementar') {
    if ($item['cantidad'] > 1) {
        $item['cantidad']--;
        
        if (isset($_SESSION['usuario_id'])) {
            $id_usuario = $_SESSION['usuario_id'];
            $stmt = $conexion->prepare("UPDATE carrito SET cantidad = ? WHERE id_Usuario = ? AND id_Tenis = ?");
            $stmt->bind_param("iii", $item['cantidad'], $id_usuario, $id_tenis);
            $stmt->execute();
            $stmt->close();
        }
    }
}

header("Location: ver_carrito.php?updated=1");
exit();
?>
