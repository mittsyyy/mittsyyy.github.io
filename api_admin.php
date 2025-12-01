<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Administrador') {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$servidor = "localhost";
$usuario_db = "root";
$password_db = "";
$base_datos = "sneakerx";

$conexion = new mysqli($servidor, $usuario_db, $password_db, $base_datos);

if ($conexion->connect_error) {
    echo json_encode(['error' => 'Error de conexión']);
    exit();
}

$conexion->set_charset("utf8mb4");

$accion = $_GET['accion'] ?? '';

switch ($accion) {
    case 'estadisticas':
        $stats = [];
        
        $result = $conexion->query("SELECT COUNT(*) as total FROM sneaker");
        $stats['total_productos'] = $result->fetch_assoc()['total'];
        
        $result = $conexion->query("SELECT COUNT(*) as total FROM Usuario WHERE rol = 'cliente'");
        $stats['total_clientes'] = $result->fetch_assoc()['total'];
        
        $result = $conexion->query("SELECT COUNT(*) as total FROM pedidos");
        $stats['total_pedidos'] = $result->fetch_assoc()['total'];
        
        $result = $conexion->query("SELECT COALESCE(SUM(monto_total), 0) as total FROM pedidos WHERE estado != 'Cancelado'");
        $stats['ventas_totales'] = $result->fetch_assoc()['total'];
        
        echo json_encode($stats);
        break;
        
    case 'productos_bajo_stock':
        $result = $conexion->query("SELECT id_Tenis, nombre, marca, stock FROM sneaker WHERE stock < 10 ORDER BY stock ASC LIMIT 5");
        $productos = [];
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
        echo json_encode($productos);
        break;
        
    case 'top_productos':
        $query = "SELECT s.id_Tenis, s.nombre, s.marca, COALESCE(SUM(dp.cantidad), 0) as total_vendido 
                  FROM sneaker s 
                  LEFT JOIN detalle_pedidos dp ON s.id_Tenis = dp.id_Tenis 
                  GROUP BY s.id_Tenis 
                  ORDER BY total_vendido DESC 
                  LIMIT 5";
        $result = $conexion->query($query);
        $productos = [];
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
        echo json_encode($productos);
        break;
        
    case 'listar_productos':
        $buscar = $_GET['buscar'] ?? '';
        $query = "SELECT * FROM sneaker";
        if ($buscar) {
            $buscar = $conexion->real_escape_string($buscar);
            $query .= " WHERE nombre LIKE '%$buscar%' OR marca LIKE '%$buscar%' OR modelo LIKE '%$buscar%'";
        }
        $query .= " ORDER BY fecha_creacion DESC";
        
        $result = $conexion->query($query);
        $productos = [];
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
        echo json_encode($productos);
        break;
        
    case 'listar_usuarios':
        $buscar = $_GET['buscar'] ?? '';
        $query = "SELECT id_Usuario, nombre_usuario, correo, rol, fecha_registro FROM Usuario";
        if ($buscar) {
            $buscar = $conexion->real_escape_string($buscar);
            $query .= " WHERE nombre_usuario LIKE '%$buscar%' OR correo LIKE '%$buscar%'";
        }
        $query .= " ORDER BY fecha_registro DESC";
        
        $result = $conexion->query($query);
        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        echo json_encode($usuarios);
        break;
        
    case 'listar_pedidos':
        $estado = $_GET['estado'] ?? '';
        $query = "SELECT p.*, u.nombre_usuario FROM pedidos p 
                  LEFT JOIN Usuario u ON p.id_Usuario = u.id_Usuario";
        if ($estado) {
            $estado = $conexion->real_escape_string($estado);
            $query .= " WHERE p.estado = '$estado'";
        }
        $query .= " ORDER BY p.fecha_pedido DESC";
        
        $result = $conexion->query($query);
        $pedidos = [];
        while ($row = $result->fetch_assoc()) {
            $pedidos[] = $row;
        }
        echo json_encode($pedidos);
        break;
        
    case 'detalle_pedido':
        $id_pedido = intval($_GET['id']);
        $stmt = $conexion->prepare("SELECT dp.*, s.nombre, s.marca, s.modelo, s.imagen_principal 
                                     FROM detalle_pedidos dp 
                                     JOIN sneaker s ON dp.id_Tenis = s.id_Tenis 
                                     WHERE dp.id_Pedido = ?");
        $stmt->bind_param("i", $id_pedido);
        $stmt->execute();
        $result = $stmt->get_result();
        $detalles = [];
        while ($row = $result->fetch_assoc()) {
            $detalles[] = $row;
        }
        $stmt->close();
        echo json_encode($detalles);
        break;
        
    case 'actualizar_estado_pedido':
        $data = json_decode(file_get_contents('php://input'), true);
        $id_pedido = intval($data['id_pedido']);
        $nuevo_estado = $data['estado'];
        
        $stmt = $conexion->prepare("UPDATE pedidos SET estado = ? WHERE id_Pedido = ?");
        $stmt->bind_param("si", $nuevo_estado, $id_pedido);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Error al actualizar']);
        }
        $stmt->close();
        break;
        
    case 'guardar_producto':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['id_Tenis']) && $data['id_Tenis']) {
            // Actualizar
            $stmt = $conexion->prepare("UPDATE sneaker SET nombre=?, marca=?, modelo=?, combinacion_colores=?, descripcion=?, precio=?, stock=?, fecha_lanzamiento=?, imagen_principal=? WHERE id_Tenis=?");
            $stmt->bind_param("sssssdissi", 
                $data['nombre'], 
                $data['marca'], 
                $data['modelo'], 
                $data['combinacion_colores'], 
                $data['descripcion'], 
                $data['precio'], 
                $data['stock'], 
                $data['fecha_lanzamiento'], 
                $data['imagen_principal'],
                $data['id_Tenis']
            );
        } else {
            // Insertar
            $stmt = $conexion->prepare("INSERT INTO sneaker (nombre, marca, modelo, combinacion_colores, descripcion, precio, stock, fecha_lanzamiento, imagen_principal, fecha_creacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssssdiss", 
                $data['nombre'], 
                $data['marca'], 
                $data['modelo'], 
                $data['combinacion_colores'], 
                $data['descripcion'], 
                $data['precio'], 
                $data['stock'], 
                $data['fecha_lanzamiento'], 
                $data['imagen_principal']
            );
        }
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'id' => $conexion->insert_id]);
        } else {
            echo json_encode(['error' => 'Error al guardar: ' . $stmt->error]);
        }
        $stmt->close();
        break;
        
    case 'eliminar_producto':
        $id = intval($_GET['id']);
        $stmt = $conexion->prepare("DELETE FROM sneaker WHERE id_Tenis = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Error al eliminar']);
        }
        $stmt->close();
        break;
        
    case 'reporte_ventas':
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
        
        $query = "SELECT DATE(p.fecha_pedido) as fecha, COUNT(*) as num_pedidos, SUM(p.monto_total) as total_ventas
                  FROM pedidos p
                  WHERE DATE(p.fecha_pedido) BETWEEN ? AND ?
                  AND p.estado != 'Cancelado'
                  GROUP BY DATE(p.fecha_pedido)
                  ORDER BY fecha DESC";
        
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
        $stmt->execute();
        $result = $stmt->get_result();
        $ventas = [];
        while ($row = $result->fetch_assoc()) {
            $ventas[] = $row;
        }
        $stmt->close();
        echo json_encode($ventas);
        break;
        
    case 'reporte_usuarios':
        $query = "SELECT DATE_FORMAT(fecha_registro, '%Y-%m') as mes, COUNT(*) as nuevos_usuarios
                  FROM Usuario
                  WHERE rol = 'cliente'
                  GROUP BY DATE_FORMAT(fecha_registro, '%Y-%m')
                  ORDER BY mes DESC
                  LIMIT 12";
        
        $result = $conexion->query($query);
        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        echo json_encode($usuarios);
        break;
        
    case 'reporte_inventario':
        $query = "SELECT nombre, marca, modelo, stock, precio, (stock * precio) as valor_inventario
                  FROM sneaker
                  ORDER BY stock ASC";
        
        $result = $conexion->query($query);
        $inventario = [];
        while ($row = $result->fetch_assoc()) {
            $inventario[] = $row;
        }
        echo json_encode($inventario);
        break;
    
    case 'listar_auditoria':
        $accion_filtro = $_GET['accion'] ?? '';
        $fecha_filtro = $_GET['fecha'] ?? '';
        
        $query = "SELECT * FROM auditoria WHERE 1=1";
        
        if ($accion_filtro) {
            $accion_filtro = $conexion->real_escape_string($accion_filtro);
            $query .= " AND accion = '$accion_filtro'";
        }
        
        if ($fecha_filtro) {
            $fecha_filtro = $conexion->real_escape_string($fecha_filtro);
            $query .= " AND DATE(fecha_accion) = '$fecha_filtro'";
        }
        
        $query .= " ORDER BY fecha_accion DESC LIMIT 100";
        
        $result = $conexion->query($query);
        $auditoria = [];
        while ($row = $result->fetch_assoc()) {
            $auditoria[] = $row;
        }
        echo json_encode($auditoria);
        break;
    
    case 'estadisticas_auditoria':
        $stats = [];
        
        $result = $conexion->query("SELECT COUNT(*) as total FROM auditoria");
        $stats['total_registros'] = $result->fetch_assoc()['total'];
        
        $result = $conexion->query("SELECT accion, COUNT(*) as total FROM auditoria GROUP BY accion");
        $stats['por_accion'] = [];
        while ($row = $result->fetch_assoc()) {
            $stats['por_accion'][$row['accion']] = $row['total'];
        }
        
        $result = $conexion->query("SELECT COUNT(*) as total FROM auditoria WHERE DATE(fecha_accion) = CURDATE()");
        $stats['hoy'] = $result->fetch_assoc()['total'];
        
        $result = $conexion->query("SELECT COUNT(*) as total FROM auditoria WHERE YEARWEEK(fecha_accion) = YEARWEEK(NOW())");
        $stats['esta_semana'] = $result->fetch_assoc()['total'];
        
        echo json_encode($stats);
        break;
        
    default:
        echo json_encode(['error' => 'Acción no válida']);
}

$conexion->close();
?>