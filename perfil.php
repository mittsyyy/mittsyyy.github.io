<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$nombreUsuario = $_SESSION['usuario_nombre'];
$emailUsuario = $_SESSION['usuario_email'];
$rolUsuario = $_SESSION['usuario_rol'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil | SNKRX</title>
    <link rel="stylesheet" href="./css/login.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .profile-info {
            margin: 20px 0;
        }
        .profile-info label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        .profile-info p {
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
            color: #666;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            margin-right: 10px;
            padding: 10px 20px;
            background: #385764;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-btn:hover {
            background: #4f7c8e;;
        }
        .logout-profile-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #CF352B;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .logout-profile-btn:hover {
            background: #cc0000;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <h1>Mi Perfil</h1>
        </div>
        
        <div class="profile-info">
            <label>Nombre:</label>
            <p><?php echo htmlspecialchars($nombreUsuario); ?></p>
        </div>
        
        <div class="profile-info">
            <label>Email:</label>
            <p><?php echo htmlspecialchars($emailUsuario); ?></p>
        </div>
        
        <div class="profile-info">
            <label>Tipo de cuenta:</label>
            <p><?php echo htmlspecialchars($rolUsuario); ?></p>
        </div>
        
        <a href="index.php" class="back-btn">Volver al inicio</a>
        <a href="logout.php" class="logout-profile-btn">Cerrar sesión</a>
    </div>
</body>
</html>