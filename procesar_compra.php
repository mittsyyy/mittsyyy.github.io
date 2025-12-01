<?php
session_start();

if (!isset($_SESSION['usuario_id']) || empty($_SESSION['carrito'])) {
    header("Location: index.php");
    exit();
}

$servidor = "localhost";
$usuario_db = "root";
$password_db = "";
$base_datos = "sneakerx";

$conexion = new mysqli($servidor, $usuario_db, $password_db, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id_usuario = $_SESSION['usuario_id'];
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $metodo_pago = $_POST['metodo_pago'];
    $notas = trim($_POST['notas'] ?? '');
    
    if (empty($nombre) || empty($email) || empty($telefono) || empty($direccion)) {
        header("Location: checkout.php?error=" . urlencode("Todos los campos son obligatorios"));
        exit();
    }
    $subtotal = 0;
    foreach ($_SESSION['carrito'] as $item) {
        $subtotal += $item['precio'] * $item['cantidad'];
    }
    $iva = $subtotal * 0.16;
    $monto_total = $subtotal + $iva;
    $conexion->begin_transaction();
    
    try {
        foreach ($_SESSION['carrito'] as $item) {
            $stmt = $conexion->prepare("SELECT stock FROM sneaker WHERE id_Tenis = ?");
            $stmt->bind_param("i", $item['id_tenis']);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $sneaker = $resultado->fetch_assoc();
            $stmt->close();
            
            if (!$sneaker || $sneaker['stock'] < $item['cantidad']) {
                throw new Exception("Stock insuficiente para: " . $item['nombre']);
            }
        }
        $estado = "Pendiente";
        $stmt = $conexion->prepare("INSERT INTO pedidos (id_Usuario, monto_total, estado, direccion_envio, telefono, metodo_pago, notas, fecha_pedido) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("idsssss", $id_usuario, $monto_total, $estado, $direccion, $telefono, $metodo_pago, $notas);
        $stmt->execute();
        $id_pedido = $conexion->insert_id;
        $stmt->close();
        
        foreach ($_SESSION['carrito'] as $item) {
            $stmt = $conexion->prepare("INSERT INTO detalle_pedidos (id_Pedido, id_Tenis, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
            $subtotal_item = $item['precio'] * $item['cantidad'];
            $stmt->bind_param("iiidd", $id_pedido, $item['id_tenis'], $item['cantidad'], $item['precio'], $subtotal_item);
            $stmt->execute();
            $stmt->close();
            
            $stmt = $conexion->prepare("UPDATE sneaker SET stock = stock - ? WHERE id_Tenis = ?");
            $stmt->bind_param("ii", $item['cantidad'], $item['id_tenis']);
            $stmt->execute();
            $stmt->close();
            
            $stmt = $conexion->prepare("DELETE FROM carrito WHERE id_Usuario = ? AND id_Tenis = ?");
            $stmt->bind_param("ii", $id_usuario, $item['id_tenis']);
            $stmt->execute();
            $stmt->close();
        }
        $conexion->commit();
    
        unset($_SESSION['carrito']);
        header("Location: confirmacion_compra.php?pedido=" . $id_pedido);
        exit();
        
    } catch (Exception $e) {
        $conexion->rollback();
        header("Location: checkout.php?error=" . urlencode($e->getMessage()));
        exit();
    }
    
} else {
    header("Location: checkout.php");
    exit();
}

$conexion->close();
?>