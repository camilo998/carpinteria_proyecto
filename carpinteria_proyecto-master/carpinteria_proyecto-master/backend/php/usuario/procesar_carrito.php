<?php
require_once '../config/db.php';

header('Content-Type: application/json');

// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'No autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$cart = $input['cart'] ?? [];
$nombre_cliente = trim($input['nombre'] ?? '');
$telefono = trim($input['telefono'] ?? '');
$direccion = trim($input['direccion'] ?? '');
$nota = trim($input['nota'] ?? '');
$fecha_entrega = $input['fecha_entrega'] ?? '';
$email = trim($input['email'] ?? '');
$metodo_pago = $input['metodo_pago'] ?? 'efectivo';

if (empty($cart) || empty($nombre_cliente) || empty($telefono) || empty($direccion) || empty($email)) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

try {
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

    // Verificar y reservar stock
    $stock_errors = [];
    foreach ($cart as $item) {
        $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ? AND activo = 1");
        $stmt->execute([$item['id']]);
        $current_stock = $stmt->fetchColumn();
        
        if ($current_stock === false || $current_stock < $item['cantidad']) {
            $stmt = $pdo->prepare("SELECT nombre FROM productos WHERE id = ?");
            $stmt->execute([$item['id']]);
            $nombre = $stmt->fetchColumn();
            $stock_errors[] = "Producto '$nombre': Stock insuficiente ($current_stock < {$item['cantidad']})";
        }
    }
    
    if (!empty($stock_errors)) {
        echo json_encode(['success' => false, 'error' => 'Stock insuficiente: ' . implode('; ', $stock_errors)]);
        exit;
    }
    
    // Reservar stock (subtract)
    foreach ($cart as $item) {
        $stmt = $pdo->prepare("UPDATE productos SET stock = GREATEST(0, stock - ?) WHERE id = ?");
        $stmt->execute([$item['cantidad'], $item['id']]);
    }

    // Calcular totales
    $subtotal = 0;
    foreach ($cart as $item) {
        $subtotal += $item['precio'] * $item['cantidad'];
    }
    $total = $subtotal; // Sin impuestos por ahora

    // Crear pedido
$stmt = $pdo->prepare("INSERT INTO pedidos (cliente_id, subtotal, total, metodo_pago, fecha_entrega, direccion_entrega, notas, estado_id) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
    $stmt->execute([$cliente_id, $subtotal, $total, $metodo_pago, $fecha_entrega, $direccion, $nota]);
    $pedido_id = $pdo->lastInsertId();

    // Detalles del pedido
    foreach ($cart as $item) {
        $stmt = $pdo->prepare("INSERT INTO pedidos_detalle (pedido_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$pedido_id, $item['id'], $item['cantidad'], $item['precio'], $item['precio'] * $item['cantidad']]);
    }

    // Historial
    $stmt = $pdo->prepare("INSERT INTO pedidos_historial (pedido_id, estado_id, comentario) VALUES (?, 1, ?)");
    $stmt->execute([$pedido_id, 'Pedido creado desde carrito']);

    echo json_encode(['success' => true, 'message' => '¡Pedido #' . $pedido_id . ' creado exitosamente! Te contactaremos pronto.']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Error DB: ' . $e->getMessage()]);
}
?>

