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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $correo = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($correo) || empty($password)) {
        header("Location: login.php?error=" . urlencode("Por favor completa todos los campos"));
        exit();
    }
    
    $stmt = $conexion->prepare("SELECT id_Usuario, nombre_usuario, correo, contraseña_hash, rol FROM Usuario WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        
        if (password_verify($password, $usuario['contraseña_hash'])) {
            
            $_SESSION['usuario_id'] = $usuario['id_Usuario'];
            $_SESSION['usuario_nombre'] = $usuario['nombre_usuario'];
            $_SESSION['usuario_email'] = $usuario['correo'];
            $_SESSION['usuario_rol'] = $usuario['rol'];
            
            if ($usuario['rol'] === 'Administrador') {
                header("Location: admin_dashboard.php");
                exit();
            } else {
                header("Location: index.php"); 
                exit();
            }
            
        } else {
            header("Location: login.php?error=" . urlencode("Email o contraseña incorrectos"));
            exit();
        }
    } else {
        header("Location: login.php?error=" . urlencode("Email o contraseña incorrectos"));
        exit();
    }
    
    $stmt->close();
} else {
    header("Location: login.php");
    exit();
}

$conexion->close();
?>