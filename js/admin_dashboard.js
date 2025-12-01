
document.addEventListener('DOMContentLoaded', function() {
    cargarEstadisticas();
    cargarProductosBajoStock();
    cargarTopProductos();
    
    const menuItems = document.querySelectorAll('.menu-item');
    const sections = document.querySelectorAll('.content-section');
    
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionId = this.getAttribute('data-section');
            
            menuItems.forEach(m => m.classList.remove('active'));
            this.classList.add('active');
            
            sections.forEach(s => s.classList.remove('active'));
            document.getElementById(sectionId).classList.add('active');
            
            const sectionTitle = this.querySelector('span').textContent;
            document.getElementById('section-title').textContent = sectionTitle;
            
            switch(sectionId) {
                case 'dashboard':
                    cargarEstadisticas();
                    cargarProductosBajoStock();
                    cargarTopProductos();
                    break;
                case 'productos':
                    cargarProductos();
                    break;
                case 'usuarios':
                    cargarUsuarios();
                    break;
                case 'pedidos':
                    cargarPedidos();
                    break;
                case 'auditoria':
                    cargarAuditoria();
                    break;
            }
        });
    });
    
    document.getElementById('buscar-producto')?.addEventListener('input', function() {
        cargarProductos(this.value);
    });
    
    document.getElementById('buscar-usuario')?.addEventListener('input', function() {
        cargarUsuarios(this.value);
    });
    
    document.getElementById('filtro-estado')?.addEventListener('change', function() {
        cargarPedidos(this.value);
    });
    
    document.getElementById('filtro-accion')?.addEventListener('change', function() {
        cargarAuditoria();
    });
    
    document.getElementById('filtro-fecha-auditoria')?.addEventListener('change', function() {
        cargarAuditoria();
    });
});


function cargarEstadisticas() {
    fetch('api_admin.php?accion=estadisticas')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-productos').textContent = data.total_productos || 0;
            document.getElementById('total-clientes').textContent = data.total_clientes || 0;
            document.getElementById('total-pedidos').textContent = data.total_pedidos || 0;
            document.getElementById('ventas-totales').textContent = '$' + (data.ventas_totales ? Number(data.ventas_totales).toFixed(2) : '0.00');
        })
        .catch(error => console.error('Error:', error));
}

function cargarProductosBajoStock() {
    fetch('api_admin.php?accion=productos_bajo_stock')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('productos-bajo-stock');
            if (data.length === 0) {
                container.innerHTML = '<p style="text-align: center; padding: 20px;">No hay productos con stock bajo</p>';
                return;
            }
            
            let html = '<table><thead><tr><th>Producto</th><th>Marca</th><th>Stock</th></tr></thead><tbody>';
            data.forEach(producto => {
                html += `
                    <tr>
                        <td>${producto.nombre}</td>
                        <td>${producto.marca}</td>
                        <td><span style="color: ${producto.stock < 5 ? '#ff4444' : '#ff8800'}; font-weight: bold;">${producto.stock}</span></td>
                    </tr>
                `;
            });
            html += '</tbody></table>';
            container.innerHTML = html;
        })
        .catch(error => console.error('Error:', error));
}

function cargarTopProductos() {
    fetch('api_admin.php?accion=top_productos')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('top-productos');
            if (data.length === 0) {
                container.innerHTML = '<p style="text-align: center; padding: 20px;">No hay datos de ventas</p>';
                return;
            }
            
            let html = '<table><thead><tr><th>Producto</th><th>Marca</th><th>Vendidos</th></tr></thead><tbody>';
            data.forEach(producto => {
                html += `
                    <tr>
                        <td>${producto.nombre}</td>
                        <td>${producto.marca}</td>
                        <td><span style="color: #4CAF50; font-weight: bold;">${producto.total_vendido}</span></td>
                    </tr>
                `;
            });
            html += '</tbody></table>';
            container.innerHTML = html;
        })
        .catch(error => console.error('Error:', error));
}

function cargarProductos(buscar = '') {
    fetch(`api_admin.php?accion=listar_productos&buscar=${buscar}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('tabla-productos');
            if (data.length === 0) {
                container.innerHTML = '<p style="text-align: center; padding: 40px;">No hay productos</p>';
                return;
            }
            
            let html = '<table><thead><tr><th>Imagen</th><th>Nombre</th><th>Marca</th><th>Modelo</th><th>Precio</th><th>Stock</th><th>Acciones</th></tr></thead><tbody>';
            data.forEach(producto => {
                html += `
                    <tr>
                        <td><img src="${producto.imagen_principal}" alt="${producto.nombre}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"></td>
                        <td>${producto.nombre}</td>
                        <td>${producto.marca}</td>
                        <td>${producto.modelo}</td>
                        <td>$${Number(producto.precio).toFixed(2)}</td>
                        <td>${producto.stock}</td>
                        <td>
                            <button class="btn-icon" onclick="editarProducto(${producto.id_Tenis})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-icon" onclick="eliminarProducto(${producto.id_Tenis})" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            html += '</tbody></table>';
            container.innerHTML = html;
        })
        .catch(error => console.error('Error:', error));
}

function mostrarFormularioProducto(producto = null) {
    const modal = document.getElementById('modal-producto');
    const titulo = document.getElementById('modal-producto-titulo');
    const form = document.getElementById('form-producto');
    
    if (producto) {
        titulo.textContent = 'Editar Producto';
        document.getElementById('producto-id').value = producto.id_Tenis;
        document.getElementById('producto-nombre').value = producto.nombre;
        document.getElementById('producto-marca').value = producto.marca;
        document.getElementById('producto-modelo').value = producto.modelo;
        document.getElementById('producto-colores').value = producto.combinacion_colores || '';
        document.getElementById('producto-descripcion').value = producto.descripcion || '';
        document.getElementById('producto-precio').value = producto.precio;
        document.getElementById('producto-stock').value = producto.stock;
        document.getElementById('producto-fecha').value = producto.fecha_lanzamiento || '';
        document.getElementById('producto-imagen').value = producto.imagen_principal || '';
    } else {
        titulo.textContent = 'Nuevo Producto';
        form.reset();
        document.getElementById('producto-id').value = '';
    }
    
    modal.style.display = 'block';
}

function editarProducto(id) {
    fetch(`api_admin.php?accion=listar_productos`)
        .then(response => response.json())
        .then(data => {
            const producto = data.find(p => p.id_Tenis == id);
            if (producto) {
                mostrarFormularioProducto(producto);
            }
        })
        .catch(error => console.error('Error:', error));
}

function eliminarProducto(id) {
    if (confirm('¿Estás seguro de eliminar este producto?')) {
        fetch(`api_admin.php?accion=eliminar_producto&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Producto eliminado exitosamente');
                    cargarProductos();
                } else {
                    alert('Error al eliminar: ' + (data.error || 'Error desconocido'));
                }
            })
            .catch(error => console.error('Error:', error));
    }
}

document.getElementById('form-producto')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const data = {
        id_Tenis: document.getElementById('producto-id').value || null,
        nombre: document.getElementById('producto-nombre').value,
        marca: document.getElementById('producto-marca').value,
        modelo: document.getElementById('producto-modelo').value,
        combinacion_colores: document.getElementById('producto-colores').value,
        descripcion: document.getElementById('producto-descripcion').value,
        precio: document.getElementById('producto-precio').value,
        stock: document.getElementById('producto-stock').value,
        fecha_lanzamiento: document.getElementById('producto-fecha').value,
        imagen_principal: document.getElementById('producto-imagen').value
    };
    
    fetch('api_admin.php?accion=guardar_producto', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Producto guardado exitosamente');
            cerrarModal('modal-producto');
            cargarProductos();
            cargarEstadisticas();
        } else {
            alert('Error: ' + (result.error || 'Error desconocido'));
        }
    })
    .catch(error => console.error('Error:', error));
});

function cargarUsuarios(buscar = '') {
    fetch(`api_admin.php?accion=listar_usuarios&buscar=${buscar}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('tabla-usuarios');
            if (data.length === 0) {
                container.innerHTML = '<p style="text-align: center; padding: 40px;">No hay usuarios</p>';
                return;
            }
            
            let html = '<table><thead><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Fecha Registro</th></tr></thead><tbody>';
            data.forEach(usuario => {
                html += `
                    <tr>
                        <td>${usuario.id_Usuario}</td>
                        <td>${usuario.nombre_usuario}</td>
                        <td>${usuario.correo}</td>
                        <td><span class="badge ${usuario.rol === 'Administrador' ? 'badge-admin' : 'badge-cliente'}">${usuario.rol}</span></td>
                        <td>${usuario.fecha_registro}</td>
                    </tr>
                `;
            });
            html += '</tbody></table>';
            container.innerHTML = html;
        })
        .catch(error => console.error('Error:', error));
}
function cargarPedidos(estado = '') {
    fetch(`api_admin.php?accion=listar_pedidos&estado=${estado}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('tabla-pedidos');
            if (data.length === 0) {
                container.innerHTML = '<p style="text-align: center; padding: 40px;">No hay pedidos</p>';
                return;
            }
            
            let html = '<table><thead><tr><th>ID</th><th>Cliente</th><th>Fecha</th><th>Total</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>';
            data.forEach(pedido => {
                const estadoClass = {
                    'Pendiente': 'badge-warning',
                    'Procesando': 'badge-info',
                    'Enviado': 'badge-primary',
                    'Entregado': 'badge-success',
                    'Cancelado': 'badge-danger'
                }[pedido.estado] || 'badge-secondary';
                
                html += `
                    <tr>
                        <td>#${String(pedido.id_Pedido).padStart(6, '0')}</td>
                        <td>${pedido.nombre_usuario}</td>
                        <td>${new Date(pedido.fecha_pedido).toLocaleString('es-MX')}</td>
                        <td>$${Number(pedido.monto_total).toFixed(2)}</td>
                        <td><span class="badge ${estadoClass}">${pedido.estado}</span></td>
                        <td>
                            <button class="btn-icon" onclick="verDetallePedido(${pedido.id_Pedido})" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn-icon" onclick="cambiarEstadoPedido(${pedido.id_Pedido}, '${pedido.estado}')" title="Cambiar estado">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            html += '</tbody></table>';
            container.innerHTML = html;
        })
        .catch(error => console.error('Error:', error));
}

function verDetallePedido(id) {
    fetch(`api_admin.php?accion=detalle_pedido&id=${id}`)
        .then(response => response.json())
        .then(data => {
            let html = '<div style="padding: 20px;"><h3>Detalles del Pedido #' + String(id).padStart(6, '0') + '</h3><table><thead><tr><th>Imagen</th><th>Producto</th><th>Cantidad</th><th>Precio Unit.</th><th>Subtotal</th></tr></thead><tbody>';
            data.forEach(item => {
                html += `
                    <tr>
                        <td><img src="${item.imagen_principal}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"></td>
                        <td>${item.nombre}<br><small>${item.marca} - ${item.modelo}</small></td>
                        <td>${item.cantidad}</td>
                        <td>$${Number(item.precio_unitario).toFixed(2)}</td>
                        <td>$${Number(item.subtotal).toFixed(2)}</td>
                    </tr>
                `;
            });
            html += '</tbody></table></div>';
            
            alert(html); 
        })
        .catch(error => console.error('Error:', error));
}

function cambiarEstadoPedido(id, estadoActual) {
    const estados = ['Pendiente', 'Procesando', 'Enviado', 'Entregado', 'Cancelado'];
    const nuevoEstado = prompt(`Estado actual: ${estadoActual}\n\nSelecciona el nuevo estado:\n1. Pendiente\n2. Procesando\n3. Enviado\n4. Entregado\n5. Cancelado\n\nIngresa el número:`);
    
    if (nuevoEstado && nuevoEstado >= 1 && nuevoEstado <= 5) {
        const estado = estados[nuevoEstado - 1];
        
        fetch('api_admin.php?accion=actualizar_estado_pedido', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ id_pedido: id, estado: estado })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Estado actualizado exitosamente');
                cargarPedidos();
            } else {
                alert('Error al actualizar');
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
function cargarAuditoria() {
    const accion = document.getElementById('filtro-accion')?.value || '';
    const fecha = document.getElementById('filtro-fecha-auditoria')?.value || '';
    
    fetch(`api_admin.php?accion=listar_auditoria&accion=${accion}&fecha=${fecha}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('tabla-auditoria');
            if (data.length === 0) {
                container.innerHTML = '<p style="text-align: center; padding: 40px;">No hay registros de auditoría</p>';
                return;
            }
            
            let html = '<table><thead><tr><th>Fecha</th><th>Tabla</th><th>Acción</th><th>Detalles</th><th>Usuario</th></tr></thead><tbody>';
            data.forEach(registro => {
                const accionClass = {
                    'INSERT': 'badge-success',
                    'UPDATE': 'badge-info',
                    'DELETE': 'badge-danger'
                }[registro.accion] || 'badge-secondary';
                
                const fechaFormato = new Date(registro.fecha_accion).toLocaleString('es-MX');
                const detalles = registro.datos_nuevos || registro.datos_anteriores || '-';
                
                html += `
                    <tr>
                        <td>${fechaFormato}</td>
                        <td><strong>${registro.tabla_afectada}</strong></td>
                        <td><span class="badge ${accionClass}">${registro.accion}</span></td>
                        <td style="max-width: 400px; word-wrap: break-word;">${detalles}</td>
                        <td>${registro.usuario_responsable}</td>
                    </tr>
                `;
            });
            html += '</tbody></table>';
            container.innerHTML = html;
        })
        .catch(error => console.error('Error:', error));
}
function generarReporteVentas() {
    const fechaInicio = document.getElementById('fecha-inicio-ventas').value;
    const fechaFin = document.getElementById('fecha-fin-ventas').value;
    
    if (!fechaInicio || !fechaFin) {
        alert('Por favor selecciona ambas fechas');
        return;
    }
    
    fetch(`api_admin.php?accion=reporte_ventas&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('resultado-ventas');
            if (data.length === 0) {
                container.innerHTML = '<p style="text-align: center; padding: 20px;">No hay ventas en este período</p>';
                return;
            }
            
            let total = 0;
            let html = '<table><thead><tr><th>Fecha</th><th>Pedidos</th><th>Total Ventas</th></tr></thead><tbody>';
            data.forEach(venta => {
                total += Number(venta.total_ventas);
                html += `
                    <tr>
                        <td>${venta.fecha}</td>
                        <td>${venta.num_pedidos}</td>
                        <td>$${Number(venta.total_ventas).toFixed(2)}</td>
                    </tr>
                `;
            });
            html += `<tr style="font-weight: bold; background: #f5f5f5;"><td colspan="2">TOTAL</td><td>$${total.toFixed(2)}</td></tr></tbody></table>`;
            container.innerHTML = html;
        })
        .catch(error => console.error('Error:', error));
}

function generarReporteUsuarios() {
    fetch('api_admin.php?accion=reporte_usuarios')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('resultado-usuarios');
            if (data.length === 0) {
                container.innerHTML = '<p style="text-align: center; padding: 20px;">No hay datos</p>';
                return;
            }
            
            let html = '<table><thead><tr><th>Mes</th><th>Nuevos Usuarios</th></tr></thead><tbody>';
            data.forEach(mes => {
                html += `
                    <tr>
                        <td>${mes.mes}</td>
                        <td>${mes.nuevos_usuarios}</td>
                    </tr>
                `;
            });
            html += '</tbody></table>';
            container.innerHTML = html;
        })
        .catch(error => console.error('Error:', error));
}

function generarReporteInventario() {
    fetch('api_admin.php?accion=reporte_inventario')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('resultado-inventario');
            if (data.length === 0) {
                container.innerHTML = '<p style="text-align: center; padding: 20px;">No hay productos</p>';
                return;
            }
            
            let valorTotal = 0;
            let html = '<table><thead><tr><th>Producto</th><th>Marca</th><th>Modelo</th><th>Stock</th><th>Precio</th><th>Valor</th></tr></thead><tbody>';
            data.forEach(item => {
                valorTotal += Number(item.valor_inventario);
                html += `
                    <tr>
                        <td>${item.nombre}</td>
                        <td>${item.marca}</td>
                        <td>${item.modelo}</td>
                        <td>${item.stock}</td>
                        <td>$${Number(item.precio).toFixed(2)}</td>
                        <td>$${Number(item.valor_inventario).toFixed(2)}</td>
                    </tr>
                `;
            });
            html += `<tr style="font-weight: bold; background: #f5f5f5;"><td colspan="5">VALOR TOTAL INVENTARIO</td><td>$${valorTotal.toFixed(2)}</td></tr></tbody></table>`;
            container.innerHTML = html;
        })
        .catch(error => console.error('Error:', error));
}
function cerrarModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}