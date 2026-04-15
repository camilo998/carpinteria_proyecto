<?php
require_once '../config/db.php';

// Conexión a la base de datos y control de acceso
// Solo los administradores pueden ver esta página.
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Consultas estadísticas para mostrar métricas en el dashboard
$total_ventas = $pdo->query("SELECT COUNT(*) as count, SUM(total) as sum_total FROM pedidos")->fetch();
$total_clientes = $pdo->query("SELECT COUNT(DISTINCT cliente_id) FROM pedidos")->fetchColumn();
$pedidos_pendientes = $pdo->query("SELECT COUNT(*) FROM pedidos WHERE estado_id <= 2")->fetchColumn(); // Pedidos en estado pendiente o confirmado
$avg_venta = $pdo->query("SELECT AVG(total) FROM pedidos")->fetchColumn() ?: 0;

// Fetch pedidos with client and state info
$stmt = $pdo->query("
    SELECT 
        p.id,
        p.total,
        p.metodo_pago,
        p.fecha_pedido,
        p.fecha_entrega,
        p.estado_id,
        ep.nombre as estado,
        ep.color as estado_color,
        c.nombre as cliente_nombre,
        c.telefono as cliente_telefono,
        c.email as cliente_email
    FROM pedidos p
    JOIN clientes c ON p.cliente_id = c.id
    JOIN estados_pedido ep ON p.estado_id = ep.id
    ORDER BY p.fecha_pedido DESC
");
$pedidos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información de Ventas - Carpintería Don Gusto</title>
    <link rel="icon" href="../../../frontend/views/Carpintin-Don-Gusto/img/logo.jpg" type="image/jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../frontend/css/stile.css">
    <link rel="stylesheet" href="../../frontend/css/producto_estile.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Existing styles + sales table styles -->
    <style>
        /* Existing styles preserved... */
        
        .stats-card { background: linear-gradient(135deg, #FFF8DC, #FAF0E6); border: 2px solid #D2B48C; }
        .stats-number { font-size: 2.5rem; font-weight: bold; color: #8B4513; }
        .badge-status { font-size: 0.85rem; padding: 0.5em 0.75em; }
        .table-ventas th { background-color: #5a3e2b; color: white; }
        .table-ventas tbody tr:hover { background-color: #FAF0E6; }
        .admin-section { background-color: #FFF8DC; border: 2px solid #D2B48C; border-radius: 10px; padding: 30px; margin: 20px auto; max-width: 1400px; }
        .navbar {
            width: 100% !important;
            margin: 0 !important;
        }

        /* Navbar */
        .navbar {
        background-color: #5a3e2b;
    }
    </style>
</head>
<body>
    <header>
    <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
        <div class="container-fluid">
            <a href="index.html">
                <img src="../usuario/img/logo.jpg" alt="Logo Carpintería Don Gusto" style="height: 50px;">
            </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mynavbar">
                    <ul class="navbar-nav me-auto mb-2 mb-sm-0">
                            <li class="nav-item">
                                <a class="nav-link" href="index.php">Productos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="usuarios.php">Usuarios</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="data.php">Info de Venta</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="dashboard_productos.php">dashboard productos</a>
                            <li class="nav-item">
                                <a class="nav-link" href="sobre-nosotros.php">Sobre Nosotros</a>
                            </li>
                
                    </ul>
                    <div class="d-flex align-items-center">
                        <span class="text-white me-3">Admin: <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
                        <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <style>
    
    </style>
    <div class="container-fluid mt-4">
        <div class="admin-section">
            <h1 class="h12 mb-4 text-center">📊 info de Ventas - Clientes</h1>
            
            <!-- Tarjetas de resumen con métricas de ventas y clientes -->
            <div class="row mb-5 g-4">
                <div class="col-md-3">
                    <div class="card stats-card text-center p-4 h-100">
                        <h5>Total Ventas</h5>
                        <div class="stats-number">$<?php echo number_format($total_ventas['sum_total'] ?: 0, 0); ?></div>
                        <small class="text-muted"><?php echo $total_ventas['count']; ?> pedidos</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card text-center p-4 h-100 text-warning">
                        <h5>Pedidos Pendientes</h5>
                        <div class="stats-number"><?php echo $pedidos_pendientes; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card text-center p-4 h-100 text-info">
                        <h5>Total Clientes</h5>
                        <div class="stats-number"><?php echo $total_clientes; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card text-center p-4 h-100 text-success">
                        <h5>Promedio Venta</h5>
                        <div class="stats-number">$<?php echo number_format($avg_venta, 0); ?></div>
                    </div>
                </div>
            </div>

            <!-- Sección de filtros para buscar pedidos y tabla de resultados -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="searchCliente" placeholder="Buscar cliente o teléfono...">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filterEstado">
                            <option value="">Todos los estados</option>
                            <?php
                            $estados = $pdo->query("SELECT id, nombre FROM estados_pedido ORDER BY orden")->fetchAll();
                            foreach ($estados as $estado): ?>
                                <option value="<?php echo $estado['id']; ?>"><?php echo htmlspecialchars($estado['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="filterFecha">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" onclick="filtrarTabla()">Filtrar</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <h3 class="mb-3">📋 Ventas por Cliente <span id="resultCount" class="badge bg-light text-dark ms-2">(<?php echo count($pedidos); ?>)</span></h3>
                    <?php if (empty($pedidos)): ?>
                        <div class="text-center py-5">
                            <h4>No hay pedidos registrados aún</h4>
                            <p>Los pedidos se crean desde el carrito del cliente.</p>
                        </div>
                    <?php else: ?>
                    <table class="table table-hover table-ventas" id="tablaPedidos">
                        <thead>
                            <tr>
                                <th>ID Pedido</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Pago</th>
                                <th>Estado</th>
                                <th>Fecha Pedido</th>
                                <th>Entrega</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $pedido): ?>
                            <!-- Cada fila contiene los datos de un pedido y atributos usados por los filtros -->
                            <tr data-telefono="<?php echo strtolower($pedido['cliente_telefono']); ?>" data-nombre="<?php echo strtolower($pedido['cliente_nombre']); ?>" data-estado="<?php echo $pedido['estado_id']; ?>" data-fecha="<?php echo date('Y-m-d', strtotime($pedido['fecha_pedido'])); ?>">
                                <td><strong>#<?php echo $pedido['id']; ?></strong></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($pedido['cliente_nombre']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($pedido['cliente_telefono']); ?></small><br>
                                    <?php if ($pedido['cliente_email']): ?><small><?php echo htmlspecialchars($pedido['cliente_email']); ?></small><?php endif; ?>
                                </td>
                                <td><strong class="text-success">$<?php echo number_format($pedido['total'], 0); ?></strong></td>
                                <td>
<span class="badge bg-secondary"><?php echo mb_strtoupper($pedido['metodo_pago'], 'UTF-8'); ?></span>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: <?php echo $pedido['estado_color']; ?> !important;">
                                        <?php echo htmlspecialchars($pedido['estado']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></td>
                                <td><?php echo $pedido['fecha_entrega'] ? date('d/m/Y', strtotime($pedido['fecha_entrega'])) : 'Pendiente'; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="verDetalles(<?php echo $pedido['id']; ?>)">Ver</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
        </div>
    </div>

    <!-- Modal for details - placeholder for step 5 -->
    <div class="modal fade" id="detallesModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Detalles Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="modalContent">Cargando...</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function verDetalles(id) {
            // Prepara la URL para recargar la página con la información del pedido seleccionado
            document.getElementById('modalContent').innerHTML = 'Cargando detalles del pedido #' + id + '...';
            const url = new URL(window.location);
            url.searchParams.set('action', 'detalles');
            url.searchParams.set('id', id);
            window.location.href = url;
        }

        // Handle AJAX for details
        <?php if (isset($_GET['action']) && $_GET['action'] === 'detalles' && isset($_GET['id'])): 
            $pedido_id = (int)$_GET['id'];
            $stmt = $pdo->prepare("
                SELECT p.*, c.nombre cliente_nombre, c.telefono cliente_telefono, ep.nombre estado 
                FROM pedidos p JOIN clientes c ON p.cliente_id = c.id JOIN estados_pedido ep ON p.estado_id = ep.id 
                WHERE p.id = ?
            ");
            $stmt->execute([$pedido_id]);
            $pedido = $stmt->fetch();
            
            if ($pedido): 
                $detalles = $pdo->prepare("
                    SELECT pd.*, pr.nombre producto_nombre, pr.imagen 
                    FROM pedidos_detalle pd 
                    JOIN productos pr ON pd.producto_id = pr.id 
                    WHERE pedido_id = ?
                ");
                $detalles->execute([$pedido_id]);
                $items = $detalles->fetchAll();
                
                $historial = $pdo->prepare("
                    SELECT ph.*, ep.nombre estado_nombre 
                    FROM pedidos_historial ph 
                    JOIN estados_pedido ep ON ph.estado_id = ep.id 
                    WHERE pedido_id = ? ORDER BY ph.created_at
                ");
                $historial->execute([$pedido_id]);
                $hist = $historial->fetchAll();
            endif;
        ?>
        if (window.location.search.includes('action=detalles')) {
            // Rellenar el contenido del modal con los datos del pedido cargados desde PHP
            document.getElementById('modalContent').innerHTML = `<?php 
                if (isset($pedido)): ?>
                <div>
                    <h6>Pedido #<?php echo $pedido['id']; ?></h6>
                    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($pedido['cliente_nombre'] . ' (' . $pedido['cliente_telefono'] . ')'); ?></p>
                    <p><strong>Total:</strong> $<?php echo number_format($pedido['total'], 0); ?></p>
                    <p><strong>Estado:</strong> <span class="badge bg-info"><?php echo htmlspecialchars($pedido['estado']); ?></span></p>
                    
                    <h6>Productos:</h6>
                    <table class="table table-sm">
                        <thead><tr><th>Producto</th><th>Cant</th><th>Precio</th><th>Subtotal</th></tr></thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['producto_nombre']); ?></td>
                                <td><?php echo $item['cantidad']; ?></td>
                                <td>$<?php echo number_format($item['precio_unitario'], 0); ?></td>
                                <td>$<?php echo number_format($item['subtotal'], 0); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <h6>Historial:</h6>
                    <ul class="list-group">
                        <?php foreach ($hist as $h): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><?php echo date('d/m H:i', strtotime($h['created_at'])); ?> - <?php echo htmlspecialchars($h['estado_nombre']); ?></span>
                            <?php if ($h['comentario']): ?><small><?php echo htmlspecialchars($h['comentario']); ?></small><?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php else: ?>
                <p>No se encontró el pedido.</p>
                <?php endif; ?>`;

            const detallesModal = new bootstrap.Modal(document.getElementById('detallesModal'));
            detallesModal.show();
        }
        <?php endif; ?>

        function filtrarTabla() {
            // Filtrar la tabla de pedidos por cliente, estado y fecha
            const search = document.getElementById('searchCliente').value.toLowerCase();
            const estado = document.getElementById('filterEstado').value;
            const fecha = document.getElementById('filterFecha').value;
            
            const rows = document.querySelectorAll('#tablaPedidos tbody tr');
            let visible = 0;
            
            rows.forEach(row => {
                const telefono = row.dataset.telefono || '';
                const nombre = row.dataset.nombre || '';
                const rowEstado = row.dataset.estado;
                const rowFecha = row.dataset.fecha;
                
                const matchSearch = telefono.includes(search) || nombre.includes(search);
                const matchEstado = !estado || rowEstado === estado;
                const matchFecha = !fecha || rowFecha === fecha;
                
                if (matchSearch && matchEstado && matchFecha) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            document.getElementById('resultCount').textContent = '(' + visible + ')';
        }

        // Live search on keyup
        document.getElementById('searchCliente').addEventListener('keyup', filtrarTabla);
        document.getElementById('filterEstado').addEventListener('change', filtrarTabla);
        document.getElementById('filterFecha').addEventListener('change', filtrarTabla);
        // Al cambiar el filtro, se actualiza el contador de filas visibles
    </script>

</body>
</html>
