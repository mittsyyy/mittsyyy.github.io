<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php?redirect=checkout");
    exit();
}

if (empty($_SESSION['carrito'])) {
    header("Location: ver_carrito.php");
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

$total_general = 0;
$carrito = $_SESSION['carrito'];

foreach ($carrito as $item) {
    $total_general += $item['precio'] * $item['cantidad'];
}

$nombreUsuario = $_SESSION['usuario_nombre'];
$emailUsuario = $_SESSION['usuario_email'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | SNKRX</title>
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
            max-width: 1200px;
            margin: 0 auto;
            background: #43606B;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px white;
        }
        h1 {
            color: white;
            margin-bottom: 30px;
            border-bottom: 3px solid white;
            padding-bottom: 15px;
        }
        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
            margin-top: 30px;
        }
        .order-summary {
            background: #12313d;
            padding: 25px;
            border-radius: 8px;
            height: fit-content;
        }
        .order-summary h2 {
            color: white;
            margin-bottom: 20px;
            font-size: 22px;
        }
        .summary-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .summary-item img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 15px;
        }
        .summary-item-details {
            flex: 1;
        }
        .summary-item-details h4 {
            color: white;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .summary-item-details p {
            color: rgba(255,255,255,0.7);
            font-size: 12px;
        }
        .summary-item-price {
            color: white;
            font-weight: bold;
        }
        .summary-totals {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid white;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: white;
        }
        .summary-total {
            font-size: 24px;
            font-weight: bold;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255,255,255,0.2);
        }
        .customer-info {
            background: #12313d;
            padding: 25px;
            border-radius: 8px;
        }
        .customer-info h2 {
            color: white;
            margin-bottom: 20px;
            font-size: 22px;
        }
        .info-group {
            margin-bottom: 20px;
        }
        .info-group label {
            display: block;
            color: white;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .info-group input,
        .info-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 5px;
            background: rgba(255,255,255,0.1);
            color: white;
            font-size: 14px;
        }
        .info-group input:focus,
        .info-group textarea:focus {
            outline: none;
            border-color: white;
        }
        .info-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        .payment-method {
            background: #43606B;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .payment-method h3 {
            color: white;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .payment-options {
            display: flex;
            gap: 15px;
        }
        .payment-option {
            flex: 1;
            padding: 15px;
            background: #12313d;
            border: 2px solid transparent;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }
        .payment-option:hover {
            border-color: white;
        }
        .payment-option.active {
            border-color: #3babd7;
            background: rgba(59, 171, 215, 0.1);
        }
        .payment-option i {
            font-size: 30px;
            color: white;
            margin-bottom: 10px;
        }
        .payment-option p {
            color: white;
            font-size: 12px;
        }
        .checkout-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
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
            text-align: center;
        }
        .btn-primary {
            background: #3babd7;
            color: white;
            flex: 1;
        }
        .btn-primary:hover {
            background: #2a9bc6;
        }
        .btn-secondary {
            background: white;
            color: #12313d;
            border: 2px solid white;
        }
        .btn-secondary:hover {
            background: #CF352B;
            color: white;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        @media (max-width: 768px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
            .payment-options {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-shopping-bag"></i> Finalizar Compra</h1>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <div class="checkout-grid">
            <div class="customer-info">
                <h2><i class="fas fa-user"></i> Información del Cliente</h2>
                
                <form id="checkout-form" action="procesar_compra.php" method="POST">
                    <div class="info-group">
                        <label for="nombre">Nombre Completo *</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombreUsuario); ?>" required>
                    </div>

                    <div class="info-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($emailUsuario); ?>" required>
                    </div>

                    <div class="info-group">
                        <label for="telefono">Teléfono *</label>
                        <input type="tel" id="telefono" name="telefono" placeholder="Ej: 6141234567" required>
                    </div>

                    <div class="info-group">
                        <label for="direccion">Dirección de Envío *</label>
                        <textarea id="direccion" name="direccion" placeholder="Calle, número, colonia, ciudad, estado, código postal" required></textarea>
                    </div>

                    <div class="payment-method">
                        <h3><i class="fas fa-credit-card"></i> Método de Pago</h3>
                        <div class="payment-options">
                            <div class="payment-option active" onclick="selectPayment(this, 'tarjeta')">
                                <i class="fas fa-credit-card"></i>
                                <p>Tarjeta</p>
                            </div>
                            <div class="payment-option" onclick="selectPayment(this, 'transferencia')">
                                <i class="fas fa-university"></i>
                                <p>Transferencia</p>
                            </div>
                            <div class="payment-option" onclick="selectPayment(this, 'efectivo')">
                                <i class="fas fa-money-bill-wave"></i>
                                <p>Efectivo</p>
                            </div>
                        </div>
                        <input type="hidden" id="metodo-pago" name="metodo_pago" value="tarjeta">
                    </div>

                    <div class="info-group">
                        <label for="notas">Notas del Pedido (Opcional)</label>
                        <textarea id="notas" name="notas" placeholder="Instrucciones especiales para la entrega..."></textarea>
                    </div>

                    <div class="checkout-actions">
                        <a href="ver_carrito.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Carrito
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Realizar Compra
                        </button>
                    </div>
                </form>
            </div>

            <div class="order-summary">
                <h2><i class="fas fa-list"></i> Resumen del Pedido</h2>
                
                <?php foreach ($carrito as $item): 
                    $subtotal = $item['precio'] * $item['cantidad'];
                ?>
                <div class="summary-item">
                    <img src="<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>">
                    <div class="summary-item-details">
                        <h4><?php echo htmlspecialchars($item['nombre']); ?></h4>
                        <p>Cantidad: <?php echo $item['cantidad']; ?> | $<?php echo number_format($item['precio'], 2); ?> c/u</p>
                    </div>
                    <div class="summary-item-price">
                        $<?php echo number_format($subtotal, 2); ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="summary-totals">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>$<?php echo number_format($total_general, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Envío:</span>
                        <span style="color: #3babd7;">GRATIS</span>
                    </div>
                    <div class="summary-row">
                        <span>IVA (16%):</span>
                        <span>$<?php echo number_format($total_general * 0.16, 2); ?></span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Total:</span>
                        <span>$<?php echo number_format($total_general * 1.16, 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function selectPayment(element, method) {
            document.querySelectorAll('.payment-option').forEach(option => {
                option.classList.remove('active');
            });
            element.classList.add('active');
            document.getElementById('metodo-pago').value = method;
        }

        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            const telefono = document.getElementById('telefono').value;
            const direccion = document.getElementById('direccion').value;
            
            if (telefono.length < 10) {
                e.preventDefault();
                alert('Por favor ingresa un teléfono válido (mínimo 10 dígitos)');
                return;
            }
            
            if (direccion.length < 20) {
                e.preventDefault();
                alert('Por favor ingresa una dirección completa');
                return;
            }
        });
    </script>
</body>
</html>