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
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body>
      <header>
          <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
            <div class="container-fluid">
                <a  href="index.html">
                    <img src="./img/logo.jpg" alt="Logo Carpintería Don Gusto" style="height: 50px;">
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
            </div>
        </nav>
    </header>

    <div class="text-container" style="margin-left: auto; margin-right: auto; display: flex; flex-direction: column; justify-content: center; align-items: center;">
        <h2>¡Explora nuestro catálogo único! 🌟</h2>
        <p>En nuestra tienda, encontrarás <strong>productos diseñados con dedicación</strong> para transformar cada rincón de tu hogar en un espacio lleno de estilo y funcionalidad.</p>
        <p>🌿 Desde <strong>mesas artesanales</strong> que combinan durabilidad y elegancia, hasta <strong>clósets</strong> y <strong>escritorios</strong> pensados para reflejar tu buen gusto. ¡Déjate inspirar y encuentra lo que estás buscando!</p>
    </div>
    <style>
/* CSS Variables */
:root {
  --primary-color: #8B4513;
  --primary-dark: #654321;
  --accent-color: #D2B48C;
  --bg-light: #FFF8DC;
  --bg-lighter: #FAF0E6;
  --text-dark: #666;
  --shadow-light: 0 4px 12px rgba(139,69,19,0.2);
  --shadow-hover: 0 6px 20px rgba(139,69,19,0.4);
  --border-radius: 10px;
  --transition: all 0.3s ease;
}

* {
  box-sizing: border-box;
}

.body {
 text-align: center;
  line-height: 1.6;
  
  margin: 0;
  padding: 0;
  
}
#productos-container {
  display: grid !important;
  /* Fuerza 3 columnas de igual tamaño */
  grid-template-columns: repeat(3, 1fr) !important; 
  gap: 25px;
  padding: 20px;
  max-width: 1400px;
  margin: 0 auto;
}

/* Evita que un solo producto ocupe todo el ancho */
.producto-item {
  width: 100%;
}

h1, h2, h3, h4, h5, h6 {
  color: var(--primary-color);
  font-weight: bold;
}

/* Header & Navigation */
header {
  background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
  position: sticky;
  top: 0;
  z-index: 1000;
  box-shadow: var(--shadow-light);
  
}

.navbar-brand img {
  height: 50px;
  border-radius: 50%;
}


.nav-link {
  color: #fff8dc !important;
    
}

.nav-link:hover {
  color: #D2691E !important;
  font-weight: bold;

  
}
.text-container {
  background: linear-gradient(rgba(255,248,220,0.95), rgba(250,240,230,0.9));
  text-align: center;
  padding: 40px 20px;
  margin: 20px auto;
  max-width: 900px;
  border: 3px solid var(--accent-color);
  border-radius: var(--border-radius);
  box-shadow: var(--shadow-light);
  backdrop-filter: blur(2px);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.text-container h2 {
  font-size: clamp(1.8em, 4vw, 2.5em);
  margin-bottom: 15px;
  text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
}

.text-container p {
  font-size: 1.1em;
  max-width: 800px;
  margin: 0 auto 10px;
}

/* Filters */
.filter-row {
  background: var(--bg-lighter);
  padding: 20px;
  border-radius: var(--border-radius);
  margin: 20px 0;
  border: 2px solid var(--accent-color);
}

/* Products Grid */
#productos-container {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 25px;
  padding: 20px;
  max-width: 1400px;
  margin: 0 auto;
}

.product-card {
  background: var(--bg-light);
  border: 2px solid var(--accent-color);
  border-radius: var(--border-radius);
  padding: 20px;
  text-align: center;
  height: 100%;
  transition: var(--transition);
  box-shadow: var(--shadow-light);
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-hover);
}


.product-card img {
  width: 100%;
  height: 220px;
  object-fit: cover;
  border-radius: 8px;
  border: 2px solid var(--accent-color);
  margin-bottom: 15px;
}

.product-card h5 {
  font-size: 1.3em;
  margin-bottom: 10px;
}

.price {
  font-size: 1.5em;
  font-weight: bold;
  color: var(--primary-color);
  margin: 15px 0;
}

/* Buttons */
.btn-comprar {
  background: linear-gradient(45deg, #F4A460, #DAA520);
  color: var(--primary-color);
  border: 2px solid var(--primary-color);
  padding: 12px 24px;
  font-size: 1em;
  font-weight: bold;
  border-radius: 8px;
  cursor: pointer;
  transition: var(--transition);
  display: inline-flex;
  align-items: center;
  gap: 8px;
  box-shadow: var(--shadow-light);
  width: auto;
}

.btn-comprar:hover {
  background: linear-gradient(45deg, #CD853F, #B8860B);
  color: white;
  transform: translateY(-2px);
  box-shadow: var(--shadow-hover);
}

.btn-carrito {
  background: linear-gradient(45deg, #28a745, #20c997) !important;
  border-color: #28a745 !important;
  color: white !important;
}

/* Cart Toggle Button */
#cart-toggle {
  position: fixed;
  bottom: 25px;
  right: 25px;
  width: 70px;
  height: 70px;
  border-radius: 50%;
  font-size: 1.5em;
  box-shadow: var(--shadow-hover);
  z-index: 1050;
  border: 4px solid var(--accent-color);
}

#cart-count {
  position: absolute;
  top: -5px;
  right: -5px;
  font-size: 0.8em;
  min-width: 22px;
  height: 22px;
}

/* Cart Dropdown/Sidebar */
.cart-sidebar {
  position: fixed;
  top: 0;
  right: -450px;
  width: 450px;
  height: 100vh;
  background: linear-gradient(135deg, var(--bg-light), var(--bg-lighter));
  box-shadow: -10px 0 40px rgba(0,0,0,0.3);
  z-index: 1070;
  transition: right 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  overflow-y: auto;
}

.cart-sidebar.show {
  right: 0;
}

.cart-header {
  padding: 25px 20px 15px;
  border-bottom: 3px solid var(--accent-color);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.cart-item {
  display: flex;
  align-items: center;
  padding: 15px 20px;
  border-bottom: 1px solid var(--accent-color);
  gap: 15px;
  transition: var(--transition);
}

.cart-item:hover {
  background: var(--bg-lighter);
}

.cart-item-img {
  width: 65px;
  height: 65px;
  object-fit: cover;
  border-radius: 8px;
  border: 2px solid var(--accent-color);
}

.cart-qty-container {
  display: flex;
  align-items: center;
  gap: 10px;
  background: var(--bg-lighter);
  padding: 8px 12px;
  border-radius: 25px;
  border: 2px solid var(--accent-color);
}

.qty-btn {
  width: 35px;
  height: 35px;
  background: linear-gradient(45deg, #F4A460, #DAA520);
  color: var(--primary-color);
  border: none;
  border-radius: 50%;
  font-weight: bold;
  cursor: pointer;
  transition: var(--transition);
}

.qty-btn:hover {
  background: linear-gradient(45deg, #CD853F, #B8860B);
  color: white;
  transform: scale(1.05);
}

.cart-total-row {
  padding: 25px 20px;
  background: linear-gradient(135deg, var(--bg-lighter), var(--bg-light));
  border-top: 3px solid var(--accent-color);
  text-align: center;
  margin-top: auto;
}

.cart-total-amount {
  font-size: 1.8em;
  font-weight: bold;
  color: var(--primary-color);
}

/* Modal/Form */
.form-modal, .overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1000;
}

.form-modal {
  background: var(--bg-light);
  margin: 50px auto;
  max-width: 550px;
  padding: 40px;
  border: 3px solid var(--accent-color);
  border-radius: var(--border-radius);
  box-shadow: var(--shadow-hover);
  max-height: 90vh;
  overflow-y: auto;
  display: none;
  backdrop-filter: blur(5px);
}

.overlay {
  background: rgba(0,0,0,0.6);
  display: none;
}

.form-control {
  border: 2px solid var(--accent-color);
  background: var(--bg-lighter);
  transition: var(--transition);
}

.form-control:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 0.25rem rgba(139,69,19,0.2);
}

.error {
  color: #fff;
  background: #f8d7da;
  padding: 12px;
  border-radius: 6px;
  margin-bottom: 20px;
}

.success {
  color: #155724;
  background: #d4edda;
  padding: 12px;
  border-radius: 6px;
  margin-bottom: 20px;
}

#productos-container {
  display: grid !important;
  /* Fuerza 3 columnas de igual tamaño */
  grid-template-columns: repeat(3, 1fr) !important; 
  gap: 25px;
  padding: 20px;
  max-width: 1400px;
  margin: 0 auto;
}

/* Evita que un solo producto ocupe todo el ancho */
.producto-item {
  width: 100%;
}

  .text-container {
    margin: 10px;
    padding: 25px 15px;
  }

  #productos-container {
    grid-template-columns: 1fr;

  .product-card img {
    height: 180px;
  }

  .cart-sidebar {
    width: 100%;
    right: -100%;
  }

  .btn-comprar {
    width: 100%;
    margin: 5px 0;
  }

  .form-modal {
    margin: 20px;
    padding: 25px 20px;
    width: calc(100% - 40px);
  }
}

@media (max-width: 480px) {
  #cart-toggle {
    width: 60px;
    height: 60px;
    font-size: 1.2em;
    bottom: 20px;
    right: 20px;
  }

  .cart-item {
    flex-direction: column;
    text-align: center;
    gap: 12px;
  }
}

/* Utilities */
.text-center { text-align: center; }
.mb-4 { margin-bottom: 1.5rem; }
.shadow-lg { box-shadow: var(--shadow-hover) !important; }
.rounded-pill { border-radius: 50rem; }
</style>


    <div class="container-fluid">
        <div id="productos-container">
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
                            <button class="btn btn-comprar me-2" onclick="abrirFormulario(<?php echo $prod['id']; ?>, '<?php echo htmlspecialchars($prod['nombre'], ENT_QUOTES); ?>', <?php echo $prod['precio']; ?>)">Comprar</button>
                            <button class="btn btn-success btn-carrito position-relative" onclick="addToCart(<?php echo $prod['id']; ?>, '<?php echo htmlspecialchars($prod['nombre'], ENT_QUOTES); ?>', <?php echo $prod['precio']; ?>, '<?php echo addslashes($imagen); ?>')" title="Agregar al carrito">
                                <i class="bi bi-cart-plus"></i> Carro
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7em;">+</span>
                            </button>
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
                <input type="date" class="form-control" id="fecha-entrega" name="fecha_entrega" required value="<?php echo htmlspecialchars($_POST['fecha_entrega'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico:</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? $_SESSION['usuario_email'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="metodo-pago" class="form-label">Método de Pago:</label>
                <select class="form-control" id="metodo-pago" name="metodo_pago">
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta">Tarjeta</option>
                    <option value="transferencia">Transferencia Bancaria</option>
                </select>
            </div>
            <button type="submit" class="btn btn-comprar">Enviar</button>
        </form>
    </div>

    <!-- Fixed Cart Toggle Button -->
    <div class="position-fixed bottom-0 end-0 p-3" style="bottom: 25px; right: 25px; z-index: 1050;">
<button id="cart-toggle" class="btn btn-comprar shadow-lg rounded-circle" title="Ver carrito" style="width: 70px; height: 70px; font-size: 1.5em;">
            🛒
            <span id="cart-count" class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">0</span>
        </button>
    </div>

<!-- Overlay para cerrar carrito -->
<div id="cart-overlay" class="cart-overlay-backdrop" onclick="toggleSidebar()" style="display: none;"></div>

<!-- Sidebar carrito -->
<div id="cart-sidebar" class="cart-sidebar position-fixed top 10 z-2060px"
  style="transition: right 0.4s ease; width: 450px; height: 100vh; background: #FFF8DC; box-shadow: -5px 0 20px rgba(0,0,0,0.3);">
  <div class="p-4" style="height: 100%; display: flex; flex-direction: column;">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom" style="border-color: #D2B48C;">
      <h4 style="color: #8B4513; margin: 0;">🛒 Mi Carrito</h4>
      <button id="close-sidebar" type="button" aria-label="Cerrar"
        style="background: none; border: none; font-size: 28px; color: #8B4513; cursor: pointer; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-weight: bold;">✕</button>
    </div>
      <div id="cart-items-list" style="flex: 1; overflow-y: auto;"></div>
      
      <div id="checkout-section" style="display: none; margin-top: auto; padding-top: 20px; border-top: 2px solid #D2B48C;">
        <div style="font-size: 16px; color: #666; margin-bottom: 10px;">Total: <span id="cart-total-checkout" style="font-size: 24px; font-weight: bold; color: #8B4513;">$0</span></div>
        
        <form id="checkout-form">
          <div style="font-size: 14px; color: #666; margin-bottom: 10px;">Datos de entrega:</div>
          <input type="text" id="checkout-nombre" placeholder="Nombre completo *" style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #D2B48C; border-radius: 5px;" required>
          <input type="tel" id="checkout-telefono" placeholder="Teléfono *" style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #D2B48C; border-radius: 5px;" required>
          <input type="email" id="checkout-email" placeholder="Email *" style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #D2B48C; border-radius: 5px;" required>
          <input type="text" id="checkout-direccion" placeholder="Dirección *" style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #D2B48C; border-radius: 5px;" required>
          <input type="date" id="checkout-fecha" style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #D2B48C; border-radius: 5px;" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
          <select id="checkout-pago" style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #D2B48C; border-radius: 5px;">
            <option value="efectivo">Efectivo</option>
            <option value="tarjeta">Tarjeta</option>
            <option value="transferencia">Transferencia Bancaria</option>
          </select>
          <textarea id="checkout-nota" placeholder="Notas (opcional)" style="width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid #D2B48C; border-radius: 5px; resize: vertical; height: 60px;"></textarea>
          <button class="btn btn-comprar w-100 mb-2" onclick="procesarCarrito()" type="button" style="background: #F4A460; color: #8B4513; border: 1px solid #8B4513;">Confirmar Pedido</button>
          <button type="button" onclick="toggleCheckout()" style="width: 100%; background: #ddd; color: #666; border: 1px solid #ccc; padding: 8px; border-radius: 5px;">Cancelar</button>
        </form>
      </div>
      
      <div id="total-section" style="margin-top: auto; padding-top: 20px; border-top: 2px solid #D2B48C; text-align: center; background: #FAF0E6; padding: 20px; border-radius: 10px;">
        <div style="font-size: 16px; color: #666; margin-bottom: 10px;">Total: <span id="cart-total">$0</span></div>
        <button class="btn btn-comprar w-100" onclick="toggleCheckout()" style="background: #F4A460; color: #8B4513; border: 1px solid #8B4513;">Ir a Pagar</button>
      </div>
    </div>
  </div>
</div>

<style>
  /* Sidebar oculto por defecto */
  .cart-sidebar {
    right: -450px !important;
    z-index: 1071 !important;
  }

  /* Sidebar visible */
  .cart-sidebar.show {
    right: 0 !important;
  }

  /* Overlay backdrop */
  .cart-overlay-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1070 !important;
    cursor: pointer;
  }
</style>

<script>
  let cart = [];

  function getCartKey() {
    return 'carrito_<?php echo $_SESSION["usuario_id"]; ?>';
  }

  function getCart() {
    return JSON.parse(localStorage.getItem(getCartKey()) || '[]');
  }

  function saveCart() {
    localStorage.setItem(getCartKey(), JSON.stringify(cart));
  }

  function addToCart(id, nombre, precio, imagen) {
    const existing = cart.find(item => item.id == id);
    if (existing) {
      existing.cantidad++;
    } else {
      cart.push({ id, nombre, precio, imagen, cantidad: 1 });
    }
    saveCart();
    updateCartUI();
    showToast('¡Producto agregado al carrito!');
  }

  function removeFromCart(index) {
    cart.splice(index, 1);
    saveCart();
    updateCartUI();
  }

  function updateQuantity(index, delta) {
    cart[index].cantidad += delta;
    if (cart[index].cantidad <= 0) {
      removeFromCart(index);
    } else {
      saveCart();
      updateCartUI();
    }
  }

  function updateCartUI() {
    document.getElementById('cart-count').textContent = cart.reduce((sum, item) => sum + item.cantidad, 0) || 0;
    const itemsList = document.getElementById('cart-items-list');
    const totalEl = document.getElementById('cart-total');

    if (cart.length === 0) {
      itemsList.innerHTML = '<div style="text-align:center; color:#8B4513; padding:40px 20px; font-style:italic;">Tu carrito está vacío 🛒</div>';
    } else {
      itemsList.innerHTML = cart.map((item, index) => `
        <div style="display:flex; align-items:center; padding:15px 0; border-bottom:1px solid #eee; gap:15px;">
          <img src="${item.imagen}" alt="${item.nombre}" style="width:60px; height:60px; object-fit:cover; border-radius:8px;" 
               onerror="this.src='../../../frontend/views/Carpintin-Don-Gusto/img/logo.jpg';this.onerror=null;">
          <div style="flex:1;">
            <div style="font-weight:600; color:#8B4513; margin-bottom:5px; font-size:15px;">${item.nombre}</div>
            <div style="color:#8B4513; font-weight:bold;">$${item.precio.toLocaleString()}</div>
          </div>
          <div style="display:flex; align-items:center; gap:10px; background:#FAF0E6; padding:8px; border-radius:25px; border:1px solid #D2B48C;">
            <button onclick="updateQuantity(${index}, -1)" style="width:30px; height:30px; background:#F4A460; color:#8B4513; border:none; border-radius:50%; cursor:pointer; font-weight:bold;">-</button>
            <span style="min-width:25px; text-align:center; font-weight:bold; color:#8B4513;">${item.cantidad}</span>
            <button onclick="updateQuantity(${index}, 1)" style="width:30px; height:30px; background:#F4A460; color:#8B4513; border:none; border-radius:50%; cursor:pointer; font-weight:bold;">+</button>
          </div>
          <button onclick="removeFromCart(${index})" style="background:#dc3545; color:white; border:none; padding:8px 15px; border-radius:6px; cursor:pointer; font-size:12px;">Eliminar</button>
        </div>
      `).join('');
    }
    totalEl.textContent = '$' + cart.reduce((sum, item) => sum + (item.precio * item.cantidad), 0).toLocaleString();
  }

  function toggleSidebar() {
    const sidebar = document.getElementById('cart-sidebar');
    const overlay = document.getElementById('cart-overlay');
    
    const isOpen = sidebar.classList.contains('show');
    
    if (isOpen) {
      // Cerrar
      sidebar.classList.remove('show');
      overlay.style.display = 'none';
    } else {
      // Abrir
      sidebar.classList.add('show');
      overlay.style.display = 'block';
    }
  }

  function toggleCheckout() {
    const checkoutSection = document.getElementById('checkout-section');
    const totalSection = document.getElementById('total-section');
    const cartItems = getCart();

    if (checkoutSection.style.display === 'none' || !checkoutSection.style.display) {
      if (cartItems.length === 0) {
        alert('Tu carrito está vacío');
        return;
      }
      // Calcular el total correctamente
      const totalAmount = cartItems.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
      const totalFormatted = '$' + totalAmount.toLocaleString();
      document.getElementById('cart-total-checkout').textContent = totalFormatted;
      document.getElementById('cart-total').textContent = totalFormatted;
      checkoutSection.style.display = 'block';
      totalSection.style.display = 'none';
    } else {
      checkoutSection.style.display = 'none';
      totalSection.style.display = 'block';
    }
  }

  function procesarCarrito() {
    const cartItems = getCart();
    if (cartItems.length === 0) {
      alert('Tu carrito está vacío');
      return;
    }

    const nombre = document.getElementById('checkout-nombre').value.trim();
    const telefono = document.getElementById('checkout-telefono').value.trim();
    const email = document.getElementById('checkout-email').value.trim();
    const direccion = document.getElementById('checkout-direccion').value.trim();
    const fecha = document.getElementById('checkout-fecha').value;

    if (!nombre || !telefono || !email || !direccion || !fecha) {
      alert('Por favor completa todos los campos requeridos');
      return;
    }

    const data = {
      cart: cartItems,
      nombre,
      telefono,
      email,
      direccion,
      fecha_entrega: fecha,
      metodo_pago: document.getElementById('checkout-pago').value,
      nota: document.getElementById('checkout-nota').value.trim()
    };

    fetch('procesar_carrito.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
      if (result.success) {
        alert(result.message);
        localStorage.removeItem(getCartKey());
        cart = [];
        updateCartUI();
        document.getElementById('checkout-section').style.display = 'none';
        document.getElementById('total-section').style.display = 'block';
        toggleSidebar();
      } else {
        alert('Error: ' + result.error);
      }
    })
    .catch(error => {
      alert('Error de conexión: ' + error.message);
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    cart = getCart();
    updateCartUI();

    // Event listener para botón del carrito
    const cartToggle = document.getElementById('cart-toggle');
    if (cartToggle) {
      cartToggle.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        toggleSidebar();
      });
    }

    // Event listener para botón X de cerrar
    const closeSidebar = document.getElementById('close-sidebar');
    if (closeSidebar) {
      closeSidebar.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleSidebar();
      });
    }

    // Event listener para overlay (clic fuera)
    const overlay = document.getElementById('cart-overlay');
    if (overlay) {
      overlay.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleSidebar();
      });
    }
  });
</script>
</body>
</html>
