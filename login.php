<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SNKRX</title>
    <link rel="stylesheet" href="./css/login.css"> 
    <link rel="stylesheet" href="./css/mediaquerys.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <style>
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-form">
                <h1>Iniciar Sesión</h1>

                <?php
                if (isset($_GET['error'])) {
                    echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
                }
                if (isset($_GET['registro'])) {
                    echo '<div class="success-message">¡Registro exitoso! Ahora puedes iniciar sesión</div>';
                }
                ?>

                <form action="procesar_login.php" method="POST">
                    <div class="input-group">
                        <label for="email">EMAIL</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="input-group">
                        <label for="password">PASSWORD</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <div class="options-row">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Remember me</label>
                        </div>
                        <a href="#" class="forgot-password">Forgot your password?</a>
                    </div>
                    
                    <button type="submit" class="login-btn">LOGIN</button>
                </form>

                <div class="switch-form">
                    ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>