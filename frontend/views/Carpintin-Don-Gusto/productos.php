<?php
// Verificar autenticación
$token = $_GET['token'] ?? $_COOKIE['token'] ?? null;
$usuario = null;

if (!$token) {
    header('Location: login.html');
    exit;
}

// Decodificar token (simulado - en producción usar JWT library)
// Por ahora guardamos en sesión o localStorage en el frontend
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Carpintería Don Gusto</title>
    <link rel="stylesheet" href="../../css/producto_estile.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .navbar h1 {
            font-size: 24px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .logout-btn {
            background: rgba(255,255,255,0.3);
            color: white;
            border: 2px solid white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: 1px;
            background : rgba(255,255,255,0.3);
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.5);
        }
        
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        
        .section-title {
            color: #333;
            font-size: 28px;
            margin-bottom: 30px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .product-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
        }
        
        .product-info {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .product-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .product-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
            flex-grow: 1;
        }
        
        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .product-price {
            font-size: 20px;
            font-weight: bold;
            color: #667eea;
        }
        
        .btn-comprar {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: 1pxbackground 0.3s;
        }
        
        .btn-comprar:hover {
            background: #45a049;
        }
        
        .empty-state {
            background: white;
            padding: 40px;
            text-align: center;
            border-radius: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>🪵 Carpintería Don Gusto</h1>
        <div class="user-info">
            <span id="usuarioNombre"></span>
            <button class="logout-btn" onclick="logout()">Cerrar Sesión</button>
        </div>
    </div>
    
    <div class="container">
        <h2 class="section-title">📦 Nuestros Productos</h2>
        <div id="productosContainer" class="products-grid">
            <div class="empty-state">Cargando productos...</div>
        </div>
    </div>
    
    <script>
        // Verificar autenticación
        function verificarAutenticacion() {
            const token = localStorage.getItem('token')
            const usuarioStr = localStorage.getItem('usuario')
            
            if (!token || !usuarioStr) {
                window.location.href = 'login.html'
                return
            }
            
            const usuario = JSON.parse(usuarioStr)
            
            if (usuario.rol !== 'usuario') {
                window.location.href = 'login.html'
                return
            }
            
            // Mostrar nombre del usuario
            if (usuario.email) {
                document.getElementById('usuarioNombre').textContent = `Hola, ${usuario.email.split('@')[0]}`
            }
        }
        
        // Obtener token
        function getToken() {
            return localStorage.getItem('token')
        }
        
        // Cerrar sesión
        function logout() {
            localStorage.removeItem('token')
            localStorage.removeItem('usuario')
            window.location.href = 'login.html'
        }
        
        // Cargar productos
        async function cargarProductos() {
            try {
                const response = await fetch('/api/productos')
                const data = await response.json()
                
                if (data.success && data.datos) {
                    mostrarProductos(data.datos)
                }
            } catch (error) {
                console.error('Error al cargar productos:', error)
            }
        }
        
        // Mostrar productos
        function mostrarProductos(productos) {
            const container = document.getElementById('productosContainer')
            
            if (productos.length === 0) {
                container.innerHTML = '<div class="empty-state">No hay productos disponibles</div>'
                return
            }
            
            container.innerHTML = productos.map(prod => `
                <div class="product-card">
                    <div class="product-image">🪵</div>
                    <div class="product-info">
                        <div class="product-name">${prod.nombre}</div>
                        <div class="product-description">${prod.descripcion || 'Producto de calidad'}</div>
                        <div class="product-footer">
                            <div class="product-price">$${parseFloat(prod.precio).toFixed(2)}</div>
                            <button class="btn-comprar" onclick="agregarAlCarrito(${prod.id}, '${prod.nombre}', ${prod.precio})">
                                Agregar
                            </button>
                        </div>
                    </div>
                </div>
            `).join('')
        }
        
        // Agregar al carrito
        async function agregarAlCarrito(id, nombre, precio) {
            try {
                const response = await fetch('/api/productos/pedido', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${getToken()}`
                    },
                    body: JSON.stringify({ producto_id: id, cantidad: 1, detalles: nombre })
                })
                
                const data = await response.json()
                
                if (data.success) {
                    alert('✅ Producto agregado a tu pedido')
                } else {
                    alert('Error: ' + data.mensaje)
                }
            } catch (error) {
                console.error('Error:', error)
                alert('Error al agregar al carrito')
            }
        }
        
        // Cargar productos al abrir la página
        verificarAutenticacion()
        cargarProductos()
    </script>
</body>
</html>
