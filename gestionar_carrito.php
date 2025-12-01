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

if ($accion === 'eliminar') {
    $id_tenis = $_SESSION['carrito'][$indice]['id_tenis'];
    
    unset($_SESSION['carrito'][$indice]);
    $_SESSION['carrito'] = array_values($_SESSION['carrito']);
    
    if (isset($_SESSION['usuario_id'])) {
        $id_usuario = $_SESSION['usuario_id'];
        $stmt = $conexion->prepare("DELETE FROM carrito WHERE id_Usuario = ? AND id_Tenis = ?");
        $stmt->bind_param("ii", $id_usuario, $id_tenis);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: ver_carrito.php");
exit();
?>
