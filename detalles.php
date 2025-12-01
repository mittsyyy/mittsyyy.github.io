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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_tenis = intval($_GET['id']);

$stmt = $conexion->prepare("SELECT * FROM sneaker WHERE id_Tenis = ?");
$stmt->bind_param("i", $id_tenis);
$stmt->execute();
$resultado = $stmt->get_result();
$sneaker = $resultado->fetch_assoc();
$stmt->close();

if (!$sneaker) {
    header("Location: index.php?error=Producto no encontrado");
    exit();
}

$usuarioLogueado = isset($_SESSION['usuario_id']);

$itemsCarrito = 0;
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $itemsCarrito += $item['cantidad'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($sneaker['nombre']); ?> - Detalles</title>
  <link rel="stylesheet" href="./css/detalles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    .cart-icon {
      position: relative;
      cursor: pointer;
    }
    .cart-count {
      position: absolute;
      top: -8px;
      right: -8px;
      background: #ff4444;
      color: white;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: bold;
    }
    .alert {
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 5px;
      text-align: center;
    }
    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    .alert-error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
  </style>
</head>
<body>

  <header>
    <div class="logo"><a href="index.php" style="text-decoration: none; color: inherit;">SNKRX</a></div>
    <nav>
      <a href="index.php">Inicio</a>
      <a href="#">Brands</a>
      <a href="ofertas.php">Ofertas</a>
      <a href="#">Blog</a>
      <a href="#">About</a>
    </nav>
    <div class="icons">
      <i class="fas fa-search"></i>
      <a href="ver_carrito.php" class="cart-icon">
        <i class="fas fa-shopping-cart"></i>
        <?php if ($itemsCarrito > 0): ?>
          <span class="cart-count"><?php echo $itemsCarrito; ?></span>
        <?php endif; ?>
      </a>
    </div>
  </header>

  <?php if (isset($_GET['added'])): ?>
    <div class="alert alert-success">
      ¡Producto agregado al carrito exitosamente!
    </div>
  <?php endif; ?>

  <section class="product-section">
    <div class="product-gallery">
      <div class="thumbs">
        <img src="<?php echo htmlspecialchars($sneaker['imagen_principal']); ?>" alt="thumb" class="thumb active">
      </div>
      <div class="main-img">
        <img id="main-product-img" src="<?php echo htmlspecialchars($sneaker['imagen_principal']); ?>" alt="<?php echo htmlspecialchars($sneaker['nombre']); ?>">
      </div>
    </div>

    <div class="product-info">
      <p class="brand"><?php echo htmlspecialchars($sneaker['marca']); ?></p>
      <h1><?php echo htmlspecialchars($sneaker['nombre']); ?></h1>
      <div class="rating">
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
        <i class="fas fa-star-half-alt"></i>
        <span>Nuevo</span>
      </div>
      <div class="price">
        <span class="current">$<?php echo number_format($sneaker['precio'], 2); ?></span>
      </div>
      <p class="desc"><?php echo htmlspecialchars($sneaker['descripcion'] ?: 'Un sneaker excepcional que combina estilo y comodidad.'); ?></p>

      <div class="option">
        <h3>Modelo:</h3>
        <p><?php echo htmlspecialchars($sneaker['modelo']); ?></p>
      </div>

      <?php if ($sneaker['combinacion_colores']): ?>
      <div class="option">
        <h3>Colores:</h3>
        <p><?php echo htmlspecialchars($sneaker['combinacion_colores']); ?></p>
      </div>
      <?php endif; ?>

      <div class="option">
        <h3>Stock disponible:</h3>
        <p><?php echo $sneaker['stock']; ?> unidades</p>
      </div>

      <form action="agregar_carrito.php" method="GET">
        <input type="hidden" name="id_tenis" value="<?php echo $sneaker['id_Tenis']; ?>">
        
        <div class="quantity">
          <button type="button" id="decrease">-</button>
          <input type="number" id="quantity" name="cantidad" value="1" min="1" max="<?php echo $sneaker['stock']; ?>">
          <button type="button" id="increase">+</button>
        </div>

        <button type="submit" class="add-cart">
          <i class="fas fa-shopping-cart"></i> AGREGAR AL CARRITO
        </button>
      </form>

      <p class="shipping">Envío gratis en toda la república mexicana.</p>

      <div class="product-icons">
        <div><i class="fas fa-shield-alt"></i> Producto Original</div>
        <div><i class="fas fa-truck"></i> Envío Rápido</div>
        <div><i class="fas fa-undo"></i> Devoluciones Fáciles</div>
        <div><i class="fas fa-check"></i> Garantía</div>
      </div>

      <div class="detail">
        <h3>Detalles</h3>
        <p><strong>Marca:</strong> <?php echo htmlspecialchars($sneaker['marca']); ?></p>
        <p><strong>Modelo:</strong> <?php echo htmlspecialchars($sneaker['modelo']); ?></p>
        <?php if ($sneaker['fecha_lanzamiento']): ?>
        <p><strong>Fecha de lanzamiento:</strong> <?php echo date('d/m/Y', strtotime($sneaker['fecha_lanzamiento'])); ?></p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <script>
    const decreaseBtn = document.getElementById('decrease');
    const increaseBtn = document.getElementById('increase');
    const quantityInput = document.getElementById('quantity');
    const maxStock = <?php echo $sneaker['stock']; ?>;

    decreaseBtn.addEventListener('click', () => {
      let currentValue = parseInt(quantityInput.value);
      if (currentValue > 1) {
        quantityInput.value = currentValue - 1;
      }
    });

    increaseBtn.addEventListener('click', () => {
      let currentValue = parseInt(quantityInput.value);
      if (currentValue < maxStock) {
        quantityInput.value = currentValue + 1;
      }
    });

    const thumbs = document.querySelectorAll('.thumb');
    const mainImg = document.getElementById('main-product-img');

    thumbs.forEach(thumb => {
      thumb.addEventListener('click', () => {
        thumbs.forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
        mainImg.src = thumb.src;
      });
    });
  </script>

</body>
</html>
