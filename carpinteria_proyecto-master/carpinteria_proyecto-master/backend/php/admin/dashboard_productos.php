<?php
require_once '../config/db.php';

// Conexión a la base de datos y control de acceso
// Solo los administradores pueden ver esta página.
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Consultas estadísticas para dashboard de productos
$total_productos = $pdo->query("SELECT COUNT(*) FROM productos WHERE activo = 1")->fetchColumn();
$total_categorias = $pdo->query("SELECT COUNT(*) FROM categorias WHERE activo = 1")->fetchColumn();
$precio_promedio = $pdo->query("SELECT ROUND(AVG(precio), 0) FROM productos WHERE activo = 1")->fetchColumn() ?: 0;
$valor_stock = $pdo->query("SELECT ROUND(SUM(precio * COALESCE(stock, 0)), 0) FROM productos")->fetchColumn() ?: 0;
$productos_sin_stock = $pdo->query("SELECT COUNT(*) FROM productos WHERE activo = 1 AND (stock IS NULL OR stock = 0)")->fetchColumn();

// Manejar actualización de stock
$stock_success = '';
$stock_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $producto_id = intval($_POST['producto_id']);
    $nuevo_stock = intval($_POST['nuevo_stock']);
    
    if ($producto_id > 0 && $nuevo_stock >= 0) {
        try {
            $stmt = $pdo->prepare("UPDATE productos SET stock = ? WHERE id = ? AND activo = 1");
            $result = $stmt->execute([$nuevo_stock, $producto_id]);
            
            if ($result) {
                $stock_success = "¡Stock actualizado exitosamente!";
            } else {
                $stock_error = "No se pudo actualizar el stock.";
            }
        } catch (PDOException $e) {
            $stock_error = "Error: " . $e->getMessage();
        }
    } else {
        $stock_error = "Stock inválido (debe ser >= 0).";
    }
    
    // Refresh data
    $total_productos = $pdo->query("SELECT COUNT(*) FROM productos WHERE activo = 1")->fetchColumn();
    $total_categorias = $pdo->query("SELECT COUNT(*) FROM categorias WHERE activo = 1")->fetchColumn();
    $precio_promedio = $pdo->query("SELECT ROUND(AVG(precio), 0) FROM productos WHERE activo = 1")->fetchColumn() ?: 0;
    $valor_stock = $pdo->query("SELECT ROUND(SUM(precio * COALESCE(stock, 0)), 0) FROM productos")->fetchColumn() ?: 0;
    $productos_sin_stock = $pdo->query("SELECT COUNT(*) FROM productos WHERE activo = 1 AND (stock IS NULL OR stock = 0)")->fetchColumn();
}

// Productos recientes
$stmt = $pdo->query("
    SELECT p.*, c.nombre as categoria 
    FROM productos p 
    LEFT JOIN categorias c ON p.categoria_id = c.id 
    WHERE p.activo = 1 
    ORDER BY p.created_at DESC, p.id DESC 
    LIMIT 10
");
$productos_recientes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Productos - Carpintería Don Gusto</title>
    <link rel="icon" href="../../../frontend/views/Carpintin-Don-Gusto/img/logo.jpg" type="image/jpg">
    <link rel="stylesheet" href="../../../frontend/css/stile.css">
    <link rel="stylesheet" href="../../../frontend/css/producto_estile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .admin-section { 
            background-color: #FFF8DC; 
            border: 2px solid #D2B48C; 
            border-radius: 10px; 
            padding: 30px; 
            margin: 20px auto; 
            max-width: 1400px; 
        }
        .stats-card { 
            background: linear-gradient(135deg, #FFF8DC, #FAF0E6); 
            border: 2px solid #D2B48C; 
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stats-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 25px rgba(0,0,0,0.15); 
        }
        .stats-number { 
            font-size: 2.5rem; 
            font-weight: bold; 
            color: #8B4513; 
        }
        .table-productos th { 
            background-color: #5a3e2b; 
            color: white; 
        }
        .table-productos tbody tr:hover { 
            background-color: #FAF0E6; 
        }
        .table-productos img { 
            width: 60px; 
            height: 45px; 
            object-fit: cover; 
            border-radius: 5px; 
        }
        .categoria-badge { 
            background-color: #d4a373; 
            color: white; 
            padding: 4px 10px; 
            border-radius: 15px; 
            font-size: 0.85rem; 
        }
        .stock-bajo { color: #dc3545; font-weight: bold; }
        .stock-ok { color: #28a745; }
        .no-stock { color: #ffc107; }
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

    <div class="container-fluid mt-4">
        <div class="admin-section">
            <?php if ($stock_success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $stock_success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($stock_error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $stock_error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <h1 class="h1 mb-4 text-center">📦 Dashboard de Productos</h1>
            <p class="text-center text-muted mb-4"><small><strong>💡 Tip:</strong> Haz doble clic en cualquier stock para editarlo directamente.</small></p>
            
            <!-- Stats Cards -->
            <div class="row mb-5 g-4">
                <div class="col-md-3">
                    <div class="card stats-card text-center p-4 h-100">
                        <div class="stats-number"><?php echo $total_productos; ?></div>
                        <h5>Total Productos</h5>
                        <small class="text-muted">Activos en catálogo</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card text-center p-4 h-100 text-success">
                        <div class="stats-number">$<?php echo number_format($precio_promedio); ?></div>
                        <h5>Precio Promedio</h5>
                        <small class="text-muted">Por producto</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card text-center p-4 h-100 text-info">
                        <div class="stats-number"><?php echo $total_categorias; ?></div>
                        <h5>Categorías</h5>
                        <small class="text-muted">Disponibles</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card text-center p-4 h-100 text-warning">
                        <div class="stats-number">$<?php echo number_format($valor_stock); ?></div>
                        <h5>Valor en Stock</h5>
                        <small class="text-muted">Inventario total</small>
                    </div>
                </div>
            </div>

            <!-- Filtros y tabla reciente -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <input type="text" class="form-control" id="searchProducto" placeholder="Buscar producto por nombre...">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filterCategoria">
                        <option value="">Todas las categorías</option>
                        <?php 
                        $cats = $pdo->query("SELECT DISTINCT c.nombre FROM categorias c JOIN productos p ON c.id = p.categoria_id WHERE p.activo=1 ORDER BY c.nombre")->fetchAll();
                        foreach ($cats as $cat): ?>
                            <option value="<?php echo strtolower($cat['nombre']); ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" id="filterStock" placeholder="Stock min">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100" onclick="filtrarTabla()">Filtrar</button>
                </div>
            </div>

            <div class="table-responsive">
                <h3 class="mb-3">🔥 Productos Recientes <span id="resultCount" class="badge bg-light text-dark ms-2">(<?php echo count($productos_recientes); ?>)</span></h3>
                <?php if (empty($productos_recientes)): ?>
                    <div class="text-center py-5">
                        <h4>No hay productos registrados</h4>
                        <p><a href="index.php" class="btn btn-primary">Agregar primer producto</a></p>
                    </div>
                <?php else: ?>
                <table class="table table-hover table-productos" id="tablaProductos">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos_recientes as $prod): ?>
                        <?php 
                        $ruta_img = !empty($prod['imagen']) ? '../../../frontend/views/Carpintin-Don-Gusto/img/' . $prod['imagen'] : '../../../frontend/views/Carpintin-Don-Gusto/img/logo.jpg';
                        $stock_class = ($prod['stock'] ?? 0) == 0 ? 'no-stock' : (($prod['stock'] ?? 0) < 5 ? 'stock-bajo' : 'stock-ok');
                        ?>
                        <tr data-nombre="<?php echo strtolower($prod['nombre']); ?>" 
                            data-categoria="<?php echo strtolower($prod['categoria'] ?? ''); ?>" 
                            data-stock="<?php echo $prod['stock'] ?? 0; ?>">
                            <td>
                                <img src="<?php echo htmlspecialchars($ruta_img); ?>" alt="<?php echo htmlspecialchars($prod['nombre']); ?>" onerror="this.src='../../../frontend/views/Carpintin-Don-Gusto/img/logo.jpg'">
                            </td>
                            <td><strong><?php echo htmlspecialchars($prod['nombre']); ?></strong>
                                <?php if ($prod['descripcion']): ?><br><small><?php echo substr($prod['descripcion'], 0, 80); ?>...</small><?php endif; ?>
                            </td>
                            <td><span class="categoria-badge"><?php echo htmlspecialchars($prod['categoria'] ?? 'Sin categoría'); ?></span></td>
                            <td><strong>$<?php echo number_format($prod['precio']); ?></strong></td>
                            <td class="stock-cell" data-producto-id="<?php echo $prod['id']; ?>" data-stock-actual="<?php echo $prod['stock'] ?? 0; ?>">
                                <span class="stock-display <?php echo $stock_class; ?>" ondblclick="editStock(this)"><?php echo $prod['stock'] ?? 0; ?></span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($prod['created_at'] ?? 'now')); ?></td>
                            <td>
                                <a href="index.php?editar=<?php echo $prod['id']; ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function filtrarTabla() {
            const search = document.getElementById('searchProducto').value.toLowerCase();
            const categoria = document.getElementById('filterCategoria').value.toLowerCase();
            const stockMin = parseInt(document.getElementById('filterStock').value) || 0;
            
            const rows = document.querySelectorAll('#tablaProductos tbody tr');
            let visible = 0;
            
            rows.forEach(row => {
                const nombre = row.dataset.nombre || '';
                const cat = row.dataset.categoria || '';
                const stock = parseInt(row.dataset.stock) || 0;
                
                const matchSearch = nombre.includes(search);
                const matchCat = !categoria || cat.includes(categoria);
                const matchStock = stock >= stockMin;
                
                if (matchSearch && matchCat && matchStock) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            document.getElementById('resultCount').textContent = '(' + visible + ')';
        }

        // Live filtering
        document.getElementById('searchProducto').addEventListener('keyup', filtrarTabla);
        document.getElementById('filterCategoria').addEventListener('change', filtrarTabla);
        document.getElementById('filterStock').addEventListener('keyup', filtrarTabla);

        // Inline stock editing
        function editStock(displayElement) {
            const cell = displayElement.closest('.stock-cell');
            const productId = cell.dataset.productoId;
            const currentStock = parseInt(cell.dataset.stockActual);
            
            // Create input
            const input = document.createElement('input');
            input.type = 'number';
            input.min = 0;
            input.value = currentStock;
            input.className = 'form-control form-control-sm mx-1';
            input.style.width = '80px';
            
            const saveBtn = document.createElement('button');
            saveBtn.className = 'btn btn-sm btn-success me-1';
            saveBtn.innerHTML = '<i class="bi bi-check"></i>';
            
            const cancelBtn = document.createElement('button');
            cancelBtn.className = 'btn btn-sm btn-secondary';
            cancelBtn.innerHTML = '<i class="bi bi-x"></i>';
            
            // Replace display with editor
            displayElement.style.display = 'none';
            cell.appendChild(input);
            cell.appendChild(saveBtn);
            cell.appendChild(cancelBtn);
            input.focus();
            input.select();
            
            // Save
            saveBtn.onclick = function() {
                const newStock = parseInt(input.value);
                if (newStock >= 0 && newStock !== currentStock) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="update_stock" value="1">
                        <input type="hidden" name="producto_id" value="${productId}">
                        <input type="hidden" name="nuevo_stock" value="${newStock}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                } else {
                    cancelEdit(cell, displayElement);
                }
            };
            
            // Cancel
            cancelBtn.onclick = function() { cancelEdit(cell, displayElement); };
            
            // Enter/Escape
            input.onkeypress = function(e) {
                if (e.key === 'Enter') saveBtn.click();
            };
            input.onblur = function() {
                setTimeout(() => cancelBtn.click(), 200);
            };
        }
        
        function cancelEdit(cell, displayElement) {
            const input = cell.querySelector('input[type="number"]');
            const saveBtn = cell.querySelector('.btn-success');
            const cancelBtn = cell.querySelector('.btn-secondary');
            if (input) input.remove();
            if (saveBtn) saveBtn.remove();
            if (cancelBtn) cancelBtn.remove();
            displayElement.style.display = '';
        }
    </script>
</body>
</html>
