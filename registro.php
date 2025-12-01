<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | SNKRX</title>
    <link rel="stylesheet" href="./css/login.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <style>
        .success-message {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
        .error-message {
            background-color: #f44336;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
        .switch-form {
            text-align: center;
            margin-top: 15px;
            color: #666;
        }
        .switch-form a {
            color: #000;
            text-decoration: none;
            font-weight: bold;
        }
        .switch-form a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-form">
                <h1>Crear Cuenta</h1>

                <?php
                if (isset($_GET['error'])) {
                    echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
                }
                if (isset($_GET['success'])) {
                    echo '<div class="success-message">¡Cuenta creada exitosamente! Ahora puedes <a href="login.php" style="color: white; text-decoration: underline;">iniciar sesión</a></div>';
                }
                ?>

                <form action="procesar_registro.php" method="POST">
                    <div class="input-group">
                        <label for="nombre">NOMBRE</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>

                    <div class="input-group">
                        <label for="email">EMAIL</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="input-group">
                        <label for="password">PASSWORD</label>
                        <input type="password" id="password" name="password" required minlength="6">
                    </div>

                    <div class="input-group">
                        <label for="confirm_password">CONFIRMAR PASSWORD</label>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                    </div>

                    <div class="options-row">
                        <div class="remember-me">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms">Acepto términos y condiciones</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="login-btn">REGISTRARME</button>
                </form>

                <div class="switch-form">
                    ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 