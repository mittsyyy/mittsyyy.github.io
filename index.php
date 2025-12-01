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

$usuarioLogueado = isset($_SESSION['usuario_id']);
$nombreUsuario = $usuarioLogueado ? $_SESSION['usuario_nombre'] : null;

$itemsCarrito = 0;
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $itemsCarrito += $item['cantidad'];
    }
}
$query = "SELECT * FROM sneaker WHERE stock > 0 ORDER BY fecha_creacion DESC LIMIT 6";
$resultado = $conexion->query($query);
$destacados = [];
if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $destacados[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SneakerX</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/mediaquerys.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Exo+2:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Irish+Grover&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-name {
            font-weight: bold;
            color: white;
            font-size: 14px;
        }
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
    </style>
</head>
<body>
    <section id="home">
        <header class="top">
            <div class="nap">SNKRX</div>
            <div class="menu">
                <nav class="menuu">
                    <ul>
                        <li><a href="#">Inicio</a></li>
                        <li><a href="#">Modelos</a></li>
                        <li><a href="ofertas.php">Ofertas</a></li>
                        <li><a href="#">Contacto</a></li>
                    </ul>
                </nav>
            </div>
            <div class="iconos">
                <?php if ($usuarioLogueado): ?>
                    <div class="user-info">
                        <span class="user-name">¡Hola, <?php echo htmlspecialchars($nombreUsuario); ?>!</span>
                        <a href="perfil.php"><img class="contact" src="img/contact.svg" alt="Perfil"></a>
                    </div>
                <?php else: ?>
                    <a href="login.php"><img class="contact" src="img/contact.svg" alt="Login"></a>
                <?php endif; ?>
                <img class="search" src="img/search.svg" alt="">
                <a href="ver_carrito.php" class="cart-icon">
                    <img class="bag" src="img/bag.svg" alt="">
                    <?php if ($itemsCarrito > 0): ?>
                        <span class="cart-count"><?php echo $itemsCarrito; ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </header>
        
        <nav class="mobile-menu">
            <ul>
                <li><a href="#"><img src="img/n.svg" alt=""></a></li>
                <li><a href="#"><img src="img/saves.svg" alt=""></a></li>
                <li><a href="ofertas.php"><img src="img/d.svg" alt=""></a></li>
            </ul>
        </nav>

        <div class="cuerpo">
            <div class="text-content">
                <h1>TU HUELLA,<br><span>TU FIRMA</span></h1>
                <p>En SNKRX creemos que el estilo empieza desde los pies. Combinamos 
                    moda, confort y autenticidad para que cada paso hable por ti.
                </p>
                <div class="buttons">
                    <button class="btn buy">Compra ahora</button>
                    <button class="btn learn">Leer más</button>
                </div>
            </div>
            <div class="image-content">
                <div class="discount">-10% <br>DISCOUNT</div>
                <img src="img/jordan.webp" alt="" loading="lazy">
            </div>
        </div>
    </section>

    <section id="stock">
        <h2>Productos Destacados</h2>
        <div class="productos">
            <?php if (count($destacados) > 0): ?>
                <?php foreach ($destacados as $sneaker): ?>
                    <div class="producto" data-id="<?php echo $sneaker['id_Tenis']; ?>">
                        <img src="<?php echo htmlspecialchars($sneaker['imagen_principal']); ?>" alt="<?php echo htmlspecialchars($sneaker['nombre']); ?>">
                        <div class="info-producto">
                            <h4><?php echo htmlspecialchars($sneaker['nombre']); ?></h4>
                            <h5><?php echo htmlspecialchars($sneaker['modelo']); ?></h5>
                            <p class="precio">$<?php echo number_format($sneaker['precio'], 2); ?></p>
                            <button class="añadir" onclick="verDetalles(<?php echo $sneaker['id_Tenis']; ?>)">+</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1/-1; text-align: center; padding: 40px;">No hay productos disponibles. Por favor, agrega productos desde el panel de administración.</p>
            <?php endif; ?>
            <div class="p-yoss">
                <a href="#"><button>></button></a>
            </div>
                 <div class="p-yoss">
                    <a href="#">
                        <button>></button>
                    </a>
                 </div>
        </div>
        <h2 class="yoss">Completa tu Look</h2>
        <div class="productos">
                <div class="producto">
                    <img src="img/gorras.webp" alt="">
                        <div class="info-producto">
                            <h4>Hurley</h4>
                            <h5>4 colores</h5>
                            <p class="precio">$600</p>
                            <button class="añadir">+</button>
                         </div>
                 </div>
                <div class="producto">
                    <img src="img/ts1.webp" alt="">
                        <div class="info-producto">
                            <h4>T-Shirt</h4>
                            <h5>Sniffin Glue</h5>
                            <p class="precio">$949</p>
                            <button class="añadir">+</button>
                         </div>
                 </div>
                <div class="producto">
                    <img src="img/ts2.webp" alt="">
                        <div class="info-producto">
                            <h4>Jersey</h4>
                            <h5>52 Green</h5>
                            <p class="precio">$999</p>
                            <button class="añadir">+</button>
                         </div>
                 </div>
                <div class="producto">
                    <img src="img/ts3.webp" alt="">
                        <div class="info-producto">
                            <h4>Jersey</h4>
                            <h5>Chicago</h5>
                            <p class="precio">$899</p>
                            <button class="añadir">+</button>
                         </div>
                 </div>
                <div class="producto">
                    <img src="img/ts4.webp" alt="">
                        <div class="info-producto">
                            <h4>Shirt</h4>
                            <h5>Black</h5>
                            <p class="precio">$599</p>
                            <button class="añadir">+</button>
                         </div>
                 </div>
                <div class="producto">
                    <img src="img/s.webp" alt="">
                        <div class="info-producto">
                            <h4>Hoddie</h4>
                            <h5>Black</h5>
                            <p class="precio">$1899</p>
                            <button class="añadir">+</button>
                         </div>
                 </div>
                 <div class="p-yoss">
                    <a target="_blank" href="https://wtfYoss.github.io">
                        <button>></button>
                    </a>
                 </div>
        </div>
        </div>
    </section>

    <script>
        function verDetalles(idTenis) {
            window.location.href = 'detalles.php?id=' + idTenis;
        }
    </script>
</body>
<footer class="main-footer">
    <div class="footer-container">
        <div class="footer-info">
            <p class="footer-logo">SNKRX</p>
            <div class="social-icons">
                <a target="_blank" href="https://www.facebook.com/share/1Apa1JWw24/?mibextid=wwXIfr" class="social-icon"><i class="fa-brands fa-facebook-f"></i></a> 
                <a target="_blank" href="https://www.tiktok.com/@mittsyandz05?_t=ZS-90mLiXOeT01&_r=1" class="social-icon"><i class="fa-brands fa-tiktok"></i></a>
                <a target="_blank" href="https://www.instagram.com/mittsyandazola?igsh=MXJrc2Zma2dqam9sdQ%3D%3D&utm_source=qr" class="social-icon"><i class="fa-brands fa-instagram"></i></a>
            </div>
        </div>
        <div class="footer-links company-links">
            <h4>Company</h4>
            <ul>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Our Services</a></li>
                <li><a href="#">Our Blog</a></li>
                <li><a href="#">Life & Updates</a></li>
            </ul>
        </div>
        <div class="footer-links other-links">
            <h4>Links</h4>
            <ul>
                <li><a href="#">FAQ</a></li>
                <li><a href="#">Testimonials</a></li>
                <li><a href="#">Recent Work</a></li>
                <li><a href="#">Features</a></li>
            </ul>
        </div>
        <div class="footer-contact">
            <h4>Contact</h4>
            <p><i class="fa-solid fa-phone"></i>  +123 456 7890</p>
            <p><i class="fa-regular fa-envelope"></i> 23CG0268@itsncg.edu.mx</p>
        </div>
    </div>
</footer>
</html>
