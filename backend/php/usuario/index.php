<?php
require_once '../config/db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// Procesar formulario de compra
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_id = intval($_POST['producto_id'] ?? 0);
    $nombre_cliente = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $nota = trim($_POST['nota'] ?? '');
    $fecha_entrega = $_POST['fecha_entrega'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $metodo_pago = $_POST['metodo_pago'] ?? 'efectivo';

    if (empty($producto_id) || empty($nombre_cliente) || empty($telefono) || empty($direccion) || empty($email)) {
        $error = 'Por favor complete todos los campos requeridos';
    } else {
        try {
            // Obtener precio del producto
            $stmt = $pdo->prepare("SELECT nombre, precio FROM productos WHERE id = ? AND activo = 1");
            $stmt->execute([$producto_id]);
            $producto = $stmt->fetch();

            if (!$producto) {
                $error = 'Producto no encontrado';
            } else {
                // Buscar o crear cliente
                $stmt = $pdo->prepare("SELECT id FROM clientes WHERE telefono = ?");
                $stmt->execute([$telefono]);
                $cliente = $stmt->fetch();

                if ($cliente) {
                    $cliente_id = $cliente['id'];
                } else {
                    $stmt = $pdo->prepare("INSERT INTO clientes (nombre, telefono, email, direccion) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$nombre_cliente, $telefono, $email, $direccion]);
                    $cliente_id = $pdo->lastInsertId();
                }

                // Crear pedido
                $precio = $producto['precio'];
                $stmt = $pdo->prepare("INSERT INTO pedidos (cliente_id, subtotal, total, metodo_pago, fecha_entrega, direccion_entrega, notas, estado_id) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
                $stmt->execute([$cliente_id, $precio, $precio, $metodo_pago, $fecha_entrega, $direccion, $nota]);
                $pedido_id = $pdo->lastInsertId();

                // Agregar detalle del pedido
                $stmt = $pdo->prepare("INSERT INTO pedidos_detalle (pedido_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, 1, ?, ?)");
                $stmt->execute([$pedido_id, $producto_id, $precio, $precio]);

                // Agregar historial
                $stmt = $pdo->prepare("INSERT INTO pedidos_historial (pedido_id, estado_id, comentario) VALUES (?, 1, ?)");
                $stmt->execute([$pedido_id, 'Pedido creado desde PHP']);

                $success = '¡Pedido realizado exitosamente! Nos contactaremos contigo pronto.';
            }
        } catch (PDOException $e) {
            $error = 'Error al procesar el pedido: ' . $e->getMessage();
        }
    }
}

// Obtener categorías
$stmt = $pdo->query("SELECT * FROM categorias WHERE activo = 1 ORDER BY nombre");
$categorias = $stmt->fetchAll();

// Obtener productos
$stmt = $pdo->query("
    SELECT p.*, c.nombre as categoria 
    FROM productos p 
    LEFT JOIN categorias c ON p.categoria_id = c.id 
    WHERE p.activo = 1 
    ORDER BY p.destacado DESC, p.id DESC
");
$productos = $stmt->fetchAll();

// Producto seleccionado para compra
$producto_seleccionado = null;
if (isset($_GET['comprar']) && is_numeric($_GET['comprar'])) {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ? AND activo = 1");
    $stmt->execute([$_GET['comprar']]);
    $producto_seleccionado = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Carpintería Don Gusto</title>
    <link rel="icon" href="../../../frontend/views/Carpintin-Don-Gusto/img/logo.jpg" type="image/jpg">
    <link rel="stylesheet" href="../../../frontend/css/producto_estile.css">
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="../../../frontend/views/Carpintin-Don-Gusto/index.html">
                    <img class="foto" src="../../../frontend/views/Carpintin-Don-Gusto/img/logo.jpg" alt="Logotipo" style="height: 50px;">
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
                            <a class="nav-link" href="sobre-nosotros.php">Sobre Nosotros</a>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center">
                        <span class="text-white me-3">Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
                        <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
                    </div>
            </div>
        </nav>
    </header>

    <br>
    <div class="text-container">
        <h2>¡Explora nuestro catálogo único! 🌟</h2>
        <p>En nuestra tienda, encontrarás <strong>productos diseñados con dedicación</strong> para transformar cada rincón de tu hogar en un espacio lleno de estilo y funcionalidad.</p>
        <p>🌿 Desde <strong>mesas artesanales</strong> que combinan durabilidad y elegancia, hasta <strong>clósets</strong> y <strong>escritorios</strong> pensados para reflejar tu buen gusto. ¡Déjate inspirar y encuentra lo que estás buscando!</p>
    </div>

    <div class="container mb-4">
        <div class="row justify-content-center">
            <div class="col-md-4 mb-2">
                <label for="buscar-producto" class="form-label" style="color: #8B4513; font-weight: bold;">Buscar:</label>
                <input type="text" class="form-control" id="buscar-producto" placeholder="Buscar producto..." onkeyup="filtrarProductos()">
            </div>
            <div class="col-md-4">
                <label for="filtro-categoria" class="form-label" style="color: #8B4513; font-weight: bold;">Filtrar por Categoría:</label>
                <select class="form-control" id="filtro-categoria" onchange="filtrarProductos()">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
    </div>

    <br>
    <div class="container-fluid">
        <div class="row" id="productos-container">
            <?php if (count($productos) === 0): ?>
                <div class="col-12 text-center">
                    <p>No hay productos disponibles</p>
                </div>
            <?php else: ?>
                    <?php foreach ($productos as $prod): ?>
                    <?php 
                    $imagen = $prod['imagen'] ?? '';
                    // Usar la misma ruta relativa que las otras imágenes en la página
                    $ruta_base = '../../../frontend/views/Carpintin-Don-Gusto/';
                    
                    if (!empty($imagen)) {
                        if (str_starts_with($imagen, 'http')) {
                            // Ya es URL externa - usar directamente
                            $ruta_img = '';
                        } else {
                            // Agregar ruta relativa
                            $ruta_img = $ruta_base;
                        }
                        $imagen = $ruta_img . $imagen;
                    }
                    ?>
                    <div class="producto-item" data-id="<?php echo $prod['id']; ?>" data-categoria="<?php echo $prod['categoria_id']; ?>" data-nombre="<?php echo strtolower($prod['nombre']); ?>">
                        <div class="product-card">
                            <img src="<?php echo $imagen ? htmlspecialchars($imagen) : '/frontend/views/Carpintin-Don-Gusto/img/logo.jpg'; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($prod['nombre']); ?>">
                            <h5><?php echo htmlspecialchars($prod['nombre']); ?></h5>
                            <p><?php echo htmlspecialchars($prod['descripcion'] ?? 'Producto de calidad artesanal'); ?></p>
                            <p class="price">$<?php echo number_format($prod['precio'], 0); ?></p>
                            <button class="btn btn-comprar" onclick="abrirFormulario(<?php echo $prod['id']; ?>, '<?php echo htmlspecialchars($prod['nombre'], ENT_QUOTES); ?>', <?php echo $prod['precio']; ?>)">Comprar</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
            <?php endif; ?>
        </div>

    <div class="overlay" id="overlay"></div>

    <div class="form-modal" id="formulario-compra">
        <button type="button" class="btn-close" aria-label="Cerrar" style="position:absolute; top:10px; right:10px;" onclick="cerrarFormulario()"></button>
        <h2>Formulario de Compra</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="hidden" name="producto_id" id="producto_id" value="<?php echo $producto_seleccionado['id'] ?? ''; ?>">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre Completo:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Número de Teléfono:</label>
                <input type="text" class="form-control" id="telefono" name="telefono" required value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección:</label>
                <input type="text" class="form-control" id="direccion" name="direccion" required value="<?php echo htmlspecialchars($_POST['direccion'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="nota" class="form-label">Nota o Información Adicional:</label>
                <textarea class="form-control" id="nota" name="nota"><?php echo htmlspecialchars($_POST['nota'] ?? ''); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="fecha-entrega" class="form-label">¿Cuándo desea recibir el pedido?</label>
                <input type="date" class="form-control" id="fecha-entrega" name="fecha-entrega" required value="<?php echo htmlspecialchars($_POST['fecha_entrega'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico:</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? $_SESSION['usuario_email'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="metodo-pago" class="form-label">Método de Pago:</label>
                <select class="form-control" id="metodo-pago" name="metodo-pago">
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta">Tarjeta</option>
                    <option value="transferencia">Transferencia Bancaria</option>
                </select>
            </div>
            <button type="submit" class="btn btn-comprar">Enviar</button>
        </form>
    </div>

    <script>
        let productoSeleccionado = null;

        // Mostrar formulario si hay un producto seleccionado
        <?php if ($producto_seleccionado): ?>
            productoSeleccionado = {
                id: <?php echo $producto_seleccionado['id']; ?>,
                nombre: '<?php echo htmlspecialchars($producto_seleccionado['nombre']); ?>',
                precio: <?php echo $producto_seleccionado['precio']; ?>
            };
            document.getElementById('formulario-compra').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        <?php endif; ?>

        function abrirFormulario(id, nombre, precio) {
            productoSeleccionado = { id, nombre, precio };
            document.getElementById('producto_id').value = id;
            document.getElementById('formulario-compra').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function cerrarFormulario() {
            document.getElementById('formulario-compra').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
            // Limpiar URL
            if (window.location.search.includes('comprar')) {
                window.location.href = 'index.php';
            }
        }

        // Cerrar formulario al hacer clic en el overlay
        document.getElementById('overlay').addEventListener('click', function() {
            cerrarFormulario();
        });

        function filtrarProductos() {
            const busqueda = document.getElementById('buscar-producto').value.toLowerCase();
            const categoria = document.getElementById('filtro-categoria').value;
            const productos = document.querySelectorAll('.producto-item');

            productos.forEach(producto => {
                const nombre = producto.dataset.nombre;
                const prodCategoria = producto.dataset.categoria;

                const coincideBusqueda = nombre.includes(busqueda);
                const coincideCategoria = !categoria || prodCategoria === categoria;

                if (coincideBusqueda && coincideCategoria) {
                    producto.style.display = 'block';
                } else {
                    producto.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
