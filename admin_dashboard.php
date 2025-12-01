<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Administrador') {
    header("Location: index.php");
    exit();
}

$nombreUsuario = $_SESSION['usuario_nombre'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | SNKRX</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="./css/admin_dashboard.css">
</head>
<body>
   
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-shoe-prints"></i>
            <h2>SNKRX Admin</h2>
        </div>
        <nav class="menu">
            <a href="#dashboard" class="menu-item active" data-section="dashboard">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="#productos" class="menu-item" data-section="productos">
                <i class="fas fa-box"></i>
                <span>Productos</span>
            </a>
            <a href="#usuarios" class="menu-item" data-section="usuarios">
                <i class="fas fa-users"></i>
                <span>Usuarios</span>
            </a>
            <a href="#pedidos" class="menu-item" data-section="pedidos">
                <i class="fas fa-shopping-cart"></i>
                <span>Pedidos</span>
            </a>
            <a href="#reportes" class="menu-item" data-section="reportes">
                <i class="fas fa-file-alt"></i>
                <span>Reportes</span>
            </a>
            
        </nav>
        <div class="sidebar-footer">
            
            <a href="logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Cerrar sesión
            </a>
        </div>
    </div>

    <div class="main-content">
        <header class="top-header">
            <h1 id="section-title">Dashboard</h1>
            <div class="user-info">
                <span>Bienvenido, <strong><?php echo htmlspecialchars($nombreUsuario); ?></strong></span>
                <img src="img/contact.svg" alt="Avatar" class="avatar">
            </div>
        </header>

        <div class="content-area">
            
            <section id="dashboard" class="content-section active">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="total-productos">0</h3>
                            <p>Total Productos</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="total-clientes">0</h3>
                            <p>Clientes</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="total-pedidos">0</h3>
                            <p>Pedidos</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon purple">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="ventas-totales">$0</h3>
                            <p>Ventas Totales</p>
                        </div>
                    </div>
                </div>

                <div class="dashboard-grid">
                    <div class="card">
                        <h3><i class="fas fa-exclamation-triangle"></i> Productos con Stock Bajo</h3>
                        <div id="productos-bajo-stock" class="table-container"></div>
                    </div>
                    <div class="card">
                        <h3><i class="fas fa-star"></i> Productos Más Vendidos</h3>
                        <div id="top-productos" class="table-container"></div>
                    </div>
                </div>
            </section>

            <section id="productos" class="content-section">
                <div class="section-header">
                    <button class="btn btn-primary" onclick="mostrarFormularioProducto()">
                        <i class="fas fa-plus"></i> Nuevo Producto
                    </button>
                    <input type="text" id="buscar-producto" placeholder="Buscar producto..." class="search-input">
                </div>
                <div id="tabla-productos" class="table-container"></div>
            </section>

            <section id="usuarios" class="content-section">
                <div class="section-header">
                    <input type="text" id="buscar-usuario" placeholder="Buscar usuario..." class="search-input">
                </div>
                <div id="tabla-usuarios" class="table-container"></div>
            </section>

            <section id="pedidos" class="content-section">
                <div class="section-header">
                    <select id="filtro-estado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Procesando">Procesando</option>
                        <option value="Enviado">Enviado</option>
                        <option value="Entregado">Entregado</option>
                        <option value="Cancelado">Cancelado</option>
                    </select>
                </div>
                <div id="tabla-pedidos" class="table-container"></div>
            </section>

            <section id="reportes" class="content-section">
                <div class="reports-grid">
                    <div class="card">
                        <h3><i class="fas fa-chart-bar"></i> Reporte de Ventas</h3>
                        <div class="form-group">
                            <label>Fecha Inicio:</label>
                            <input type="date" id="fecha-inicio-ventas" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Fecha Fin:</label>
                            <input type="date" id="fecha-fin-ventas" class="form-control">
                        </div>
                        <button class="btn btn-primary" onclick="generarReporteVentas()">
                            <i class="fas fa-file-pdf"></i> Generar Reporte
                        </button>
                        <div id="resultado-ventas" class="table-container"></div>
                    </div>

                    <div class="card">
                        <h3><i class="fas fa-user-plus"></i> Nuevos Usuarios por Mes</h3>
                        <button class="btn btn-primary" onclick="generarReporteUsuarios()">
                            <i class="fas fa-file-excel"></i> Generar Reporte
                        </button>
                        <div id="resultado-usuarios" class="table-container"></div>
                    </div>

                    <div class="card">
                        <h3><i class="fas fa-boxes"></i> Inventario Completo</h3>
                        <button class="btn btn-primary" onclick="generarReporteInventario()">
                            <i class="fas fa-download"></i> Descargar Inventario
                        </button>
                        <div id="resultado-inventario" class="table-container"></div>
                    </div>
                </div>
            </section>

           
        </div>
    </div>

    <div id="modal-producto" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal('modal-producto')">&times;</span>
            <h2 id="modal-producto-titulo">Nuevo Producto</h2>
            <form id="form-producto">
                <input type="hidden" id="producto-id">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre *</label>
                        <input type="text" id="producto-nombre" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Marca *</label>
                        <input type="text" id="producto-marca" required class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Modelo *</label>
                        <input type="text" id="producto-modelo" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Combinación de Colores</label>
                        <input type="text" id="producto-colores" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea id="producto-descripcion" rows="3" class="form-control"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Precio *</label>
                        <input type="number" id="producto-precio" step="0.01" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Stock *</label>
                        <input type="number" id="producto-stock" required class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Fecha Lanzamiento</label>
                        <input type="date" id="producto-fecha" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Imagen URL</label>
                        <input type="text" id="producto-imagen" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModal('modal-producto')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/admin_dashboard.js"></script>
</body>
</html>