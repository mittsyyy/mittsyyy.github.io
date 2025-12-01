<?php
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_GET['pedido'])) {
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

$id_pedido = intval($_GET['pedido']);
$id_usuario = $_SESSION['usuario_id'];

// Obtener información del pedido
$stmt = $conexion->prepare("SELECT * FROM pedidos WHERE id_Pedido = ? AND id_Usuario = ?");
$stmt->bind_param("ii", $id_pedido, $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$pedido = $resultado->fetch_assoc();
$stmt->close();

if (!$pedido) {
    header("Location: index.php");
    exit();
}

// Obtener detalles del pedido
$stmt = $conexion->prepare("SELECT dp.*, s.nombre, s.marca, s.modelo, s.imagen_principal 
                            FROM detalle_pedidos dp 
                            JOIN sneaker s ON dp.id_Tenis = s.id_Tenis 
                            WHERE dp.id_Pedido = ?");
$stmt->bind_param("i", $id_pedido);
$stmt->execute();
$resultado = $stmt->get_result();
$detalles = [];
while ($row = $resultado->fetch_assoc()) {
    $detalles[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra Confirmada | SNKRX</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: "Archivo Black", sans-serif;
            background: #12313d;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #43606B;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px white;
        }
        .success-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: #4CAF50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: scaleIn 0.5s ease-in-out;
        }
        .success-icon i {
            font-size: 40px;
            color: white;
        }
        @keyframes scaleIn {
            0% {
                transform: scale(0);
            }
            50% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
            }
        }
        h1 {
            color: white;
            margin-bottom: 10px;
        }
        .subtitle {
            color: rgba(255,255,255,0.8);
            font-size: 16px;
        }
        .order-info {
            background: #12313d;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .order-info h2 {
            color: white;
            margin-bottom: 20px;
            font-size: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            color: white;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: rgba(255,255,255,0.7);
        }
        .info-value {
            font-weight: bold;
            text-align: right;
        }
        .order-items {
            background: #12313d;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .order-items h2 {
            color: white;
            margin-bottom: 20px;
            font-size: 20px;
        }
        .order-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .order-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }
        .item-details {
            flex: 1;
        }
        .item-details h4 {
            color: white;
            margin-bottom: 5px;
        }
        .item-details p {
            color: rgba(255,255,255,0.7);
            font-size: 14px;
        }
        .item-price {
            color: white;
            font-weight: bold;
            text-align: right;
        }
        .order-total {
            background: #3babd7;
            padding: 20px 25px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-primary {
            background: white;
            color: #12313d;
        }
        .btn-primary:hover {
            background: #f0f0f0;
        }
        .btn-secondary {
            background: #12313d;
            color: white;
            border: 2px solid white;
        }
        .btn-secondary:hover {
            background: #1a3f4d;
        }
        .next-steps {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .next-steps h3 {
            color: white;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .next-steps ul {
            color: white;
            padding-left: 20px;
        }
        .next-steps li {
            margin-bottom: 10px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-header">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1>¡Compra Realizada con Éxito!</h1>
            <p class="subtitle">Gracias por tu compra. Hemos recibido tu pedido correctamente.</p>
        </div>

        <div class="order-info">
            <h2><i class="fas fa-info-circle"></i> Información del Pedido</h2>
            <div class="info-row">
                <span class="info-label">Número de Pedido:</span>
                <span class="info-value">#<?php echo str_pad($pedido['id_Pedido'], 6, '0', STR_PAD_LEFT); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha:</span>
                <span class="info-value"><?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Estado:</span>
                <span class="info-value"><?php echo htmlspecialchars($pedido['estado']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Método de Pago:</span>
                <span class="info-value"><?php echo ucfirst(htmlspecialchars($pedido['metodo_pago'])); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Dirección de Envío:</span>
                <span class="info-value"><?php echo htmlspecialchars($pedido['direccion_envio']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Teléfono:</span>
                <span class="info-value"><?php echo htmlspecialchars($pedido['telefono']); ?></span>
            </div>
        </div>

        <div class="order-items">
            <h2><i class="fas fa-shopping-bag"></i> Productos Comprados</h2>
            <?php foreach ($detalles as $item): ?>
            <div class="order-item">
                <img src="<?php echo htmlspecialchars($item['imagen_principal']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>">
                <div class="item-details">
                    <h4><?php echo htmlspecialchars($item['nombre']); ?></h4>
                    <p><?php echo htmlspecialchars($item['marca']); ?> - <?php echo htmlspecialchars($item['modelo']); ?></p>
                    <p>Cantidad: <?php echo $item['cantidad']; ?> x $<?php echo number_format($item['precio_unitario'], 2); ?></p>
                </div>
                <div class="item-price">
                    $<?php echo number_format($item['subtotal'], 2); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="order-total">
            <div class="total-row">
                <span>Total Pagado:</span>
                <span>$<?php echo number_format($pedido['monto_total'], 2); ?></span>
            </div>
        </div>

        <div class="next-steps">
            <h3><i class="fas fa-clipboard-list"></i> Próximos Pasos</h3>
            <ul>
                <li>Recibirás un correo de confirmación en breve con los detalles de tu pedido</li>
                <li>Tu pedido será procesado en las próximas 24-48 horas</li>
                <li>El envío es GRATIS en toda la República Mexicana</li>
                <li>Recibirás notificaciones sobre el estado de tu pedido</li>
                <li>Tiempo estimado de entrega: 3-5 días hábiles</li>
            </ul>
        </div>

        <div class="actions">
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home"></i> Volver al Inicio
            </a>
            <a href="perfil.php" class="btn btn-secondary">
                <i class="fas fa-user"></i> Ver Mi Perfil
            </a>
        </div>
    </div>
</body>
</html>