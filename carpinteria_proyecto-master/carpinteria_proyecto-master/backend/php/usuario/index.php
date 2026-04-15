<?php
require_once '../config/db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Obtener categorías
$stmt = $pdo->query("SELECT * FROM categorias WHERE activo = 1 ORDER BY nombre");
$categorias = $stmt->fetchAll();

// Solo productos con stock disponible (sin stock = no visibles en catálogo)
$stmt = $pdo->query("
    SELECT p.*, c.nombre as categoria 
    FROM productos p 
    LEFT JOIN categorias c ON p.categoria_id = c.id 
    WHERE p.activo = 1 AND COALESCE(p.stock, 0) > 0
    ORDER BY p.destacado DESC, p.id DESC
");
$productos = $stmt->fetchAll();
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

.product-stock-badge {
  display: inline-block;
  margin: 8px 0;
  padding: 6px 12px;
  font-size: 0.85rem;
  font-weight: 700;
  color: #856404;
  background: #fff3cd;
  border: 1px solid #ffc107;
  border-radius: 999px;
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

@media (max-width: 768px) {
  .text-container {
    margin: 10px;
    padding: 25px 15px;
  }

  #productos-container {
    grid-template-columns: 1fr;
  }

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
                    $ruta_base = '../../../frontend/views/Carpintin-Don-Gusto/';
                    
                    if (!empty($imagen)) {
                        if (str_starts_with($imagen, 'http')) {
                            $ruta_img = '';
                        } else {
                            $ruta_img = $ruta_base;
                        }
                        $imagen = $ruta_img . $imagen;
                    }
                    $stockDisp = (int)($prod['stock'] ?? 0);
                    $stockBajo = $stockDisp > 0 && $stockDisp <= 10;
                    ?>
                    <div class="producto-item" data-id="<?php echo $prod['id']; ?>" data-categoria="<?php echo $prod['categoria_id']; ?>" data-nombre="<?php echo strtolower($prod['nombre']); ?>">
                        <div class="product-card">
                            <img src="<?php echo $imagen ? htmlspecialchars($imagen) : '/frontend/views/Carpintin-Don-Gusto/img/logo.jpg'; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($prod['nombre']); ?>">
                            <h5><?php echo htmlspecialchars($prod['nombre']); ?></h5>
                            <p><?php echo htmlspecialchars($prod['descripcion'] ?? 'Producto de calidad artesanal'); ?></p>
                            <?php if ($stockBajo): ?>
                            <p class="product-stock-badge mb-0">¡Últimas unidades disponibles!</p>
                            <?php endif; ?>
                            <p class="price">$<?php echo number_format($prod['precio'], 0); ?></p>
                            <button type="button" class="btn btn-comprar me-2" onclick='comprarAhora(<?php echo (int)$prod['id']; ?>, <?php echo json_encode($prod['nombre'], JSON_UNESCAPED_UNICODE); ?>, <?php echo json_encode((float)$prod['precio']); ?>, <?php echo json_encode($imagen, JSON_UNESCAPED_UNICODE); ?>, <?php echo (int)$stockDisp; ?>)'>Comprar</button>
                            <button type="button" class="btn btn-success btn-carrito position-relative" onclick='addToCart(<?php echo (int)$prod['id']; ?>, <?php echo json_encode($prod['nombre'], JSON_UNESCAPED_UNICODE); ?>, <?php echo json_encode((float)$prod['precio']); ?>, <?php echo json_encode($imagen, JSON_UNESCAPED_UNICODE); ?>, <?php echo (int)$stockDisp; ?>)' title="Agregar al carrito">
                                <i class="bi bi-cart-plus"></i> Carro
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7em;">+</span>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
            <?php endif; ?>
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
          <input type="email" id="checkout-email" placeholder="Email *" value="<?php echo htmlspecialchars($_SESSION['usuario_email'] ?? ''); ?>" style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #D2B48C; border-radius: 5px;" required>
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

  function escapeHtml(str) {
    if (str == null) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  /** Para atributo src: solo escapar comillas, no & (rompe query strings en URLs). */
  function attrEscape(str) {
    if (str == null) return '';
    return String(str).replace(/"/g, '&quot;');
  }

  function showToast(message, icon) {
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2800,
        timerProgressBar: true,
        icon: icon || 'success',
        title: message
      });
    } else {
      alert(message);
    }
  }

  function alertSwal(opts) {
    if (typeof Swal !== 'undefined') {
      Swal.fire(opts);
    } else {
      var t = (opts.title || '') + (opts.text ? '\n' + opts.text : '');
      alert(t);
    }
  }

  function syncCheckoutTotals() {
    const totalAmount = cart.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
    const totalFormatted = '$' + totalAmount.toLocaleString();
    const chk = document.getElementById('cart-total-checkout');
    const tot = document.getElementById('cart-total');
    if (chk) chk.textContent = totalFormatted;
    if (tot) tot.textContent = totalFormatted;
  }

  function showCheckoutPanel() {
    if (cart.length === 0) return;
    syncCheckoutTotals();
    document.getElementById('checkout-section').style.display = 'block';
    document.getElementById('total-section').style.display = 'none';
  }

  function hideCheckoutPanel() {
    document.getElementById('checkout-section').style.display = 'none';
    document.getElementById('total-section').style.display = 'block';
  }

  /** Comprar: mismo checkout que el carrito (sidebar + formulario de entrega/pago). */
  function comprarAhora(id, nombre, precio, imagen, stock) {
    const s = Number(stock);
    if (!s || s <= 0) {
      alertSwal({ icon: 'warning', title: 'Sin stock', text: 'Este producto no está disponible.' });
      return;
    }
    cart = [{ id, nombre, precio, imagen, cantidad: 1, stock: s }];
    saveCart();
    updateCartUI();
    const sidebar = document.getElementById('cart-sidebar');
    if (!sidebar.classList.contains('show')) {
      toggleSidebar();
    }
    showCheckoutPanel();
  }

  function addToCart(id, nombre, precio, imagen, stock) {
    const s = Number(stock);
    if (!s || s <= 0) {
      alertSwal({ icon: 'warning', title: 'Sin stock', text: 'Este producto no está disponible.' });
      return;
    }
    const existing = cart.find(item => item.id == id);
    if (existing) {
      if (existing.cantidad >= s) {
        alertSwal({ icon: 'info', title: 'Límite alcanzado', text: 'Ya tienes el máximo disponible en el carrito.' });
        return;
      }
      existing.cantidad++;
      existing.stock = s;
    } else {
      cart.push({ id, nombre, precio, imagen, cantidad: 1, stock: s });
    }
    saveCart();
    updateCartUI();
    showToast('¡Producto agregado al carrito!');
  }

  function removeFromCart(index) {
    cart.splice(index, 1);
    saveCart();
    updateCartUI();
    if (cart.length === 0) {
      hideCheckoutPanel();
    } else {
      syncCheckoutTotals();
    }
  }

  function updateQuantity(index, delta) {
    const item = cart[index];
    const maxStock = item.stock != null ? Number(item.stock) : 999999;
    if (delta > 0 && item.cantidad >= maxStock) {
      showToast('No hay más unidades en inventario', 'info');
      return;
    }
    item.cantidad += delta;
    if (item.cantidad <= 0) {
      removeFromCart(index);
    } else {
      saveCart();
      updateCartUI();
      syncCheckoutTotals();
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
          <img src="${attrEscape(item.imagen)}" alt="${escapeHtml(item.nombre)}" style="width:60px; height:60px; object-fit:cover; border-radius:8px;" 
               onerror="this.src='../../../frontend/views/Carpintin-Don-Gusto/img/logo.jpg';this.onerror=null;">
          <div style="flex:1;">
            <div style="font-weight:600; color:#8B4513; margin-bottom:5px; font-size:15px;">${escapeHtml(item.nombre)}</div>
            <div style="color:#8B4513; font-weight:bold;">$${Number(item.precio).toLocaleString()}</div>
          </div>
          <div style="display:flex; align-items:center; gap:10px; background:#FAF0E6; padding:8px; border-radius:25px; border:1px solid #D2B48C;">
            <button type="button" onclick="updateQuantity(${index}, -1)" style="width:30px; height:30px; background:#F4A460; color:#8B4513; border:none; border-radius:50%; cursor:pointer; font-weight:bold;">-</button>
            <span style="min-width:25px; text-align:center; font-weight:bold; color:#8B4513;">${item.cantidad}</span>
            <button type="button" onclick="updateQuantity(${index}, 1)" style="width:30px; height:30px; background:#F4A460; color:#8B4513; border:none; border-radius:50%; cursor:pointer; font-weight:bold;">+</button>
          </div>
          <button type="button" onclick="removeFromCart(${index})" style="background:#dc3545; color:white; border:none; padding:8px 15px; border-radius:6px; cursor:pointer; font-size:12px;">Eliminar</button>
        </div>
      `).join('');
    }
    totalEl.textContent = '$' + cart.reduce((sum, item) => sum + (item.precio * item.cantidad), 0).toLocaleString();
    syncCheckoutTotals();
  }

  function toggleSidebar() {
    const sidebar = document.getElementById('cart-sidebar');
    const overlay = document.getElementById('cart-overlay');
    const isOpen = sidebar.classList.contains('show');
    if (isOpen) {
      sidebar.classList.remove('show');
      overlay.style.display = 'none';
    } else {
      sidebar.classList.add('show');
      overlay.style.display = 'block';
    }
  }

  function toggleCheckout() {
    const checkoutSection = document.getElementById('checkout-section');
    const cartItems = getCart();
    if (checkoutSection.style.display === 'none' || !checkoutSection.style.display) {
      if (cartItems.length === 0) {
        alertSwal({ icon: 'info', title: 'Carrito vacío', text: 'Agrega productos antes de pagar.' });
        return;
      }
      showCheckoutPanel();
    } else {
      hideCheckoutPanel();
    }
  }

  function procesarCarrito() {
    const cartItems = getCart();
    if (cartItems.length === 0) {
      alertSwal({ icon: 'info', title: 'Carrito vacío', text: 'No hay productos para procesar.' });
      return;
    }

    const nombre = document.getElementById('checkout-nombre').value.trim();
    const telefono = document.getElementById('checkout-telefono').value.trim();
    const email = document.getElementById('checkout-email').value.trim();
    const direccion = document.getElementById('checkout-direccion').value.trim();
    const fecha = document.getElementById('checkout-fecha').value;

    if (!nombre || !telefono || !email || !direccion || !fecha) {
      alertSwal({ icon: 'warning', title: 'Faltan datos', text: 'Completa todos los campos requeridos.' });
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
        alertSwal({
          icon: 'success',
          title: '¡Pedido registrado!',
          text: result.message,
          confirmButtonColor: '#8B4513'
        });
        localStorage.removeItem(getCartKey());
        cart = [];
        updateCartUI();
        hideCheckoutPanel();
        toggleSidebar();
      } else {
        alertSwal({ icon: 'error', title: 'No se pudo completar', text: result.error || 'Error desconocido' });
      }
    })
    .catch(error => {
      alertSwal({ icon: 'error', title: 'Error de conexión', text: error.message });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    cart = getCart();
    updateCartUI();

    const cartToggle = document.getElementById('cart-toggle');
    if (cartToggle) {
      cartToggle.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        toggleSidebar();
      });
    }

    const closeSidebar = document.getElementById('close-sidebar');
    if (closeSidebar) {
      closeSidebar.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleSidebar();
      });
    }

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
