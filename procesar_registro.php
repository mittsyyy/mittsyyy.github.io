<?php
$servidor = "localhost";
$usuario_db = "root";
$password_db = "";
$base_datos = "sneakerx"; 

$conexion = new mysqli($servidor, $usuario_db, $password_db, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nombre_usuario = trim($_POST['nombre']);
    $correo = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errores = [];
    
    if (empty($nombre_usuario)) {
        $errores[] = "El nombre es obligatorio";
    }
    
    if (empty($correo)) {
        $errores[] = "El email es obligatorio";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no es válido";
    }
    
    if (empty($password)) {
        $errores[] = "La contraseña es obligatoria";
    } elseif (strlen($password) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres";
    }
    
    if ($password !== $confirm_password) {
        $errores[] = "Las contraseñas no coinciden";
    }
    
    if (empty($errores)) {
        $stmt = $conexion->prepare("SELECT id_Usuario FROM Usuario WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            $errores[] = "Este email ya está registrado";
        }
        $stmt->close();
    }
    
    if (!empty($errores)) {
        $mensaje_error = implode(", ", $errores);
        header("Location: registro.php?error=" . urlencode($mensaje_error));
        exit();
    }
    
    $contrasena_hash = password_hash($password, PASSWORD_DEFAULT);
    $rol = "cliente";
    
    $stmt = $conexion->prepare("INSERT INTO Usuario (nombre_usuario, correo, contraseña_hash, rol, fecha_registro) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $nombre_usuario, $correo, $contrasena_hash, $rol);
    
    if ($stmt->execute()) {
        header("Location: login.php?registro=exitoso");
        exit();
    } else {
        if ($conexion->errno == 1062) {
            header("Location: registro.php?error=" . urlencode("Este correo ya está registrado"));
        } else {
            header("Location: registro.php?error=" . urlencode("Error al crear la cuenta. Intenta nuevamente."));
        }
        exit();
    }
    
    $stmt->close();
    
} else {
    header("Location: registro.php");
    exit();
}

$conexion->close();
?>