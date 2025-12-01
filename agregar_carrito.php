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

if (!isset($_GET['id_tenis']) || !is_numeric($_GET['id_tenis'])) {
    header("Location: index.php?error=" . urlencode("ID de producto inválido."));
    exit();
}

$id_tenis = intval($_GET['id_tenis']);
$cantidad = isset($_GET['cantidad']) ? intval($_GET['cantidad']) : 1;

if ($cantidad <= 0) {
    $cantidad = 1;
}

$stmt = $conexion->prepare("SELECT * FROM sneaker WHERE id_Tenis = ?");
$stmt->bind_param("i", $id_tenis);
$stmt->execute();
$resultado = $stmt->get_result();
$sneaker = $resultado->fetch_assoc();
$stmt->close();

if (!$sneaker) {
    header("Location: index.php?error=" . urlencode("Producto no encontrado."));
    exit();
}

if ($cantidad > $sneaker['stock']) {
    header("Location: detalles.php?id=$id_tenis&error=" . urlencode("Stock insuficiente."));
    exit();
}

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$existe = false;
foreach ($_SESSION['carrito'] as $indice => &$item) {
    if ($item['id_tenis'] === $id_tenis) {
        $nueva_cantidad = $item['cantidad'] + $cantidad;
        
        // Verificar que no exceda el stock
        if ($nueva_cantidad > $sneaker['stock']) {
            header("Location: detalles.php?id=$id_tenis&error=" . urlencode("No hay suficiente stock disponible."));
            exit();
        }
        
        $item['cantidad'] = $nueva_cantidad;
        $existe = true;
        break;
    }
}

if (!$existe) {
    $_SESSION['carrito'][] = [
        'id_tenis' => $id_tenis,
        'nombre' => $sneaker['nombre'],
        'marca' => $sneaker['marca'],
        'modelo' => $sneaker['modelo'],
        'precio' => $sneaker['precio'],
        'cantidad' => $cantidad,
        'imagen' => $sneaker['imagen_principal']
    ];
}

if (isset($_SESSION['usuario_id'])) {
    $id_usuario = $_SESSION['usuario_id'];
    
    $stmt = $conexion->prepare("SELECT id_Carrito, cantidad FROM carrito WHERE id_Usuario = ? AND id_Tenis = ?");
    $stmt->bind_param("ii", $id_usuario, $id_tenis);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        $nueva_cantidad = $row['cantidad'] + $cantidad;
        
        $stmt_update = $conexion->prepare("UPDATE carrito SET cantidad = ? WHERE id_Carrito = ?");
        $stmt_update->bind_param("ii", $nueva_cantidad, $row['id_Carrito']);
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        $stmt_insert = $conexion->prepare("INSERT INTO carrito (id_Usuario, id_Tenis, cantidad, precio_unitario, fecha_adicion) VALUES (?, ?, ?, ?, NOW())");
        $stmt_insert->bind_param("iiid", $id_usuario, $id_tenis, $cantidad, $sneaker['precio']);
        $stmt_insert->execute();
        $stmt_insert->close();
    }
    
    $stmt->close();
}

header("Location: ver_carrito.php?added=1");
exit();
?>
