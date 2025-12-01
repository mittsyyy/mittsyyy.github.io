<?php
session_start();

$total_general = 0;
$carrito = $_SESSION['carrito'] ?? []; 
$usuarioLogueado = isset($_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito de Compras | SNKRX</title>
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
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px white;
            background-color:#43606B;
        }
        h1 {
            color: white;
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .alert-success {
            background-color: #d4edda;
            color: #00b82bff;
            border: 1px solid #c3e6cb;
        }
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-cart i {
            font-size: 80px;
            color: white;
            margin-bottom: 20px;
        }
        .empty-cart p {
            font-size: 20px;
            color: white;
            margin-bottom: 30px;
        }
        .cart-items {
            margin-bottom: 30px;
        }
        .cart-item {
            display: flex;
            align-items: center;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: box-shadow 0.3s;
        }
        .cart-item:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .item-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 20px;
        }
        .item-details {
            flex: 1;
        }
        .item-details h3 {
            color: whte;
            margin-bottom: 5px;
        }
        .item-details p {
            color: white;
            margin-bottom: 5px;
        }
        .item-price {
            font-size: 20px;
            font-weight: bold;
            color: white;
            margin: 10px 0;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 15px 0;
        }
        .quantity-btn {
            width: 30px;
            height: 30px;
            border: 1px solid #12313d;
            background: #12313d;
            cursor: pointer;
            border-radius: 5px;
            font-size: 18px;
            transition: all 0.3s;
            text-decoration: none;
            padding-left: 10px;
            color:white;
            padding-top:5px;
        }
        .quantity-btn:hover {
            background: #000;
            color: white;
        }
        .quantity-display {
            padding: 5px 15px;
            border: 1px solid white;
            border-radius: 5px;
            min-width: 50px;
            text-align: center;
        }
        .item-actions {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        .btn-remove {
            background: #ff4444;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-remove:hover {
            background: #cc0000;
        }
        .cart-summary {
            background: #43606B;
            padding: 25px;
            border-radius: 8px;
            margin-top: 30px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .summary-total {
            border-top: 2px solid white;
            padding-top: 15px;
            margin-top: 15px;
            font-size: 24px;
            font-weight: bold;
        }
        .cart-actions {
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
            background: #12313d;
            color: white;
            flex: 1;
        }
        .btn-primary:hover {
            background: #5b84a1;
        }
        .btn-secondary {
            background: white;
            color: #12313d;
            border: 2px solid #12313d;
        }
        .btn-secondary:hover {
            background: #CF352B;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-shopping-cart"></i> Tu Carrito de Compras</h1>
        
        <?php if (isset($_GET['added'])): ?>
            <div class="alert alert-success">
                ¡Producto agregado al carrito exitosamente!
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success">
                Carrito actualizado
            </div>
        <?php endif; ?>
        
        <?php if (empty($carrito)): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <p>Tu carrito está vacío</p>
                <a href="index.php" class="btn btn-primary">¡Empieza a comprar!</a>
            </div>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach ($carrito as $indice => $item): 
                    $subtotal = $item['precio'] * $item['cantidad'];
                    $total_general += $subtotal;
                ?>
                <div class="cart-item">
                    <img src="<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>" class="item-image">
                    
                    <div class="item-details">
                        <h3><?php echo htmlspecialchars($item['nombre']); ?></h3>
                        <p><strong>Marca:</strong> <?php echo htmlspecialchars($item['marca']); ?></p>
                        <p><strong>Modelo:</strong> <?php echo htmlspecialchars($item['modelo']); ?></p>
                        <p class="item-price">$<?php echo number_format($item['precio'], 2); ?> c/u</p>
                        
                        <div class="quantity-controls">
                            <a href="actualizar_carrito.php?accion=decrementar&indice=<?php echo $indice; ?>" class="quantity-btn">-</a>
                            <span class="quantity-display"><?php echo $item['cantidad']; ?></span>
                            <a href="actualizar_carrito.php?accion=incrementar&indice=<?php echo $indice; ?>" class="quantity-btn">+</a>
                        </div>
                        
                        <p><strong>Subtotal:</strong> $<?php echo number_format($subtotal, 2); ?></p>
                    </div>
                    
                    <div class="item-actions">
                        <a href="gestionar_carrito.php?accion=eliminar&indice=<?php echo $indice; ?>" class="btn-remove" onclick="return confirm('¿Eliminar este producto del carrito?')">
                            <i class="fas fa-trash"></i> Eliminar
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>$<?php echo number_format($total_general, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Envío:</span>
                    <span style="color: #3babd7;">GRATIS</span>
                </div>
                <div class="summary-row summary-total">
                    <span>Total:</span>
                    <span>$<?php echo number_format($total_general, 2); ?></span>
                </div>
            </div>

            <div class="cart-actions">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Seguir Comprando
                </a>
                <?php if ($usuarioLogueado): ?>
                    <a href="checkout.php" class="btn btn-primary">
                        <i class="fas fa-credit-card"></i> Proceder al Pago
                    </a>
                <?php else: ?>
                    <a href="login.php?redirect=checkout" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión para Comprar
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
