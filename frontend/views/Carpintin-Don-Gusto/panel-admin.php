
<?php
// Verificar autenticación de admin
$token = $_GET['token'] ?? $_COOKIE['token'] ?? null;

if (!$token) {
    header('Location: login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="../../css/producto_estile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - Carpintería Don Gusto</title>
    <style>
        .admin-section {
            background-color: #FFF8DC;
            border: 2px solid #D2B48C;
            border-radius: 10px;
            padding: 30px;
            margin: 20px auto;
            max-width: 1400px;
        }
        
        .admin-section h2 {
            color: #8B4513;
            font-family: 'Arial', sans-serif;
            border-bottom: 2px solid #D2B48C;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .form-group label {
            color: #8B4513;
            font-weight: bold;
        }
        
        .form-control {
            border: 1px solid #D2B48C;
            background-color: #FAF0E6;
        }
        
        .form-control:focus {
            border-color: #8B4513;
            box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.25);
        }
        
        .btn-add {
            background-color: #F4A460;
            color: #8B4513;
            border: 1px solid #8B4513;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        .btn-add:hover {
            background-color: #CD853F;
            color: white;
        }
        
        .btn-edit {
            background-color: #F4A460;
            color: #8B4513;
            border: 1px solid #8B4513;
            padding: 8px 15px;
            font-size: 14px;
            border-radius: 5px;
            margin-right: 5px;
        }
        
        .btn-edit:hover {
            background-color: #CD853F;
            color: white;
        }
        
        .btn-delete {
            background-color: #c0392b;
            color: white;
            border: none;
            padding: 8px 15px;
            font-size: 14px;
            border-radius: 5px;
        }
        
        .btn-delete:hover {
            background-color: #a93226;
        }
        
        /* Estilo de tabla */
        .table-container {
            overflow-x: auto;
            background-color: white;
            border-radius: 8px;
            border: 1px solid #D2B48C;
        }
        
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        
        .product-table thead {
            background-color: #5a3e2b;
            color: white;
        }
        
        .product-table th {
            padding: 15px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #D2B48C;
        }
        
        .product-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #D2B48C;
            vertical-align: middle;
        }
        
        .product-table tbody tr:hover {
            background-color: #FAF0E6;
        }
        
        .product-table img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #D2B48C;
        }
        
        .product-table .nombre {
            font-weight: bold;
            color: #8B4513;
            max-width: 200px;
        }
        
        .product-table .descripcion {
            color: #666;
            font-size: 13px;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .product-table .precio {
            font-weight: bold;
            color: #8B4513;
            font-size: 16px;
        }
        
        .product-table .categoria {
            background-color: #d4a373;
            color: white;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 12px;
        }
        
        .product-table .actions {
            white-space: nowrap;
        }
        
        .error {
            color: #c0392b;
            background: #fadbd8;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            display: none;
            border-left: 4px solid #c0392b;
        }
        
        .success {
            color: #1e8449;
            background: #d5f5e3;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            display: none;
            border-left: 4px solid #1e8449;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.html">
                    <img class="foto" src="img/logo.jpg" alt="Logotipo" style="height: 50px;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mynavbar">
                    <ul class="navbar-nav me-auto mb-2 mb-sm-0">
                        <li class="nav-item">
                            <a class="nav-link" href="productos.html">Productos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="sobre-nosotros.html">Sobre Nosotros</a>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-light btn-sm" onclick="logout()">Cerrar Sesión</button>
                    </div>
            </div>
        </nav>
    </header>

    <br><br>
    
    <div class="container">
        <!-- Sección de agregar producto -->
        <div class="admin-section">
            <h2>➕ Agregar Nuevo Producto</h2>
            <div class="error" id="errorMsg"></div>
            <div class="success" id="successMsg"></div>
            
            <form id="agregarProductoForm">
                <input type="hidden" id="productoId" name="productoId">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombre" class="form-label">Nombre del Producto</label>
                        <input type="text" class="form-control" id="nombre" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="precio" class="form-label">Precio</label>
                        <input type="number" class="form-control" id="precio" step="0.01" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" rows="3"></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="imagen" class="form-label">URL de Imagen</label>
                        <input type="text" class="form-control" id="imagen" placeholder="img/nombre-imagen.jpg">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-add" id="submitBtn">Agregar Producto</button>
                <button type="button" class="btn btn-secondary" onclick="cancelarEdicion()" id="cancelBtn" style="display: none;">Cancelar</button>
            </form>
        </div>
        
        <!-- Sección de productos en tabla -->
        <div class="admin-section">
            <h2>📦 Productos del Sistema</h2>
            <div class="table-container">
                <table class="product-table" id="productosTable">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Categoría</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="productosContainer">
                        <tr>
                            <td colspan="6" class="empty-state">Cargando productos...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        let editando = false;
        
        // Obtener token
        function getToken() {
            return localStorage.getItem('token');
        }
        
        // Cerrar sesión
        function logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('usuario');
            window.location.href = 'login.html';
        }
        
        // Cancelar edición
        function cancelarEdicion() {
            editando = false;
            document.getElementById('agregarProductoForm').reset();
            document.getElementById('submitBtn').textContent = 'Agregar Producto';
            document.getElementById('cancelBtn').style.display = 'none';
        }
        
        // Cargar productos
        async function cargarProductos() {
            try {
                const response = await fetch('/api/productos');
                const data = await response.json();
                
                if (data.success) {
                    mostrarProductos(data.datos);
                }
            } catch (error) {
                console.error('Error al cargar productos:', error);
            }
        }
        
        // Mostrar productos en tabla
        function mostrarProductos(productos) {
            const container = document.getElementById('productosContainer');
            
            if (productos.length === 0) {
                container.innerHTML = '<tr><td colspan="6" class="empty-state">No hay productos aún</td></tr>';
                return;
            }
            
            container.innerHTML = productos.map(prod => `
                <tr>
                    <td>
                        <img src="${prod.imagen || 'img/logo.jpg'}" alt="${prod.nombre}" onerror="this.src='img/logo.jpg'">
                    </td>
                    <td class="nombre">${prod.nombre}</td>
                    <td class="descripcion" title="${prod.descripcion || ''}">${prod.descripcion || 'Sin descripción'}</td>
                    <td class="precio">$${parseFloat(prod.precio).toFixed(2)}</td>
                    <td><span class="categoria">${prod.categoria || 'General'}</span></td>
                    <td class="actions">
                        <button class="btn btn-edit" onclick="editarProducto(${prod.id})">Editar</button>
                        <button class="btn btn-delete" onclick="eliminarProducto(${prod.id})">Eliminar</button>
                    </td>
                </tr>
            `).join('');
        }
        
        // Editar producto
        async function editarProducto(id) {
            try {
                const response = await fetch(`/api/productos/${id}`, {
                    headers: {
                        'Authorization': `Bearer ${getToken()}`
                    }
                });
                const data = await response.json();
                
                if (data.success && data.datos) {
                    // Manejar el caso cuando datos es un array (mysql2)
                    const producto = Array.isArray(data.datos) ? data.datos[0] : data.datos;
                    document.getElementById('productoId').value = producto.id;
                    document.getElementById('nombre').value = producto.nombre;
                    document.getElementById('precio').value = producto.precio;
                    document.getElementById('descripcion').value = producto.descripcion || '';
                    document.getElementById('imagen').value = producto.imagen || '';
                    
                    editando = true;
                    document.getElementById('submitBtn').textContent = 'Actualizar Producto';
                    document.getElementById('cancelBtn').style.display = 'inline-block';
                    
                    // Scroll al formulario
                    document.getElementById('agregarProductoForm').scrollIntoView({ behavior: 'smooth' });
                } else {
                    alert('Error al cargar producto');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al cargar producto');
            }
        }
        
        // Agregar/Actualizar producto
        document.getElementById('agregarProductoForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const productoId = document.getElementById('productoId').value;
            const nombre = document.getElementById('nombre').value;
            const precio = document.getElementById('precio').value;
            const descripcion = document.getElementById('descripcion').value;
            const imagen = document.getElementById('imagen').value;
            
            const errorDiv = document.getElementById('errorMsg');
            const successDiv = document.getElementById('successMsg');
            errorDiv.style.display = 'none';
            successDiv.style.display = 'none';
            
            try {
                const method = editando ? 'PUT' : 'POST';
                const url = editando ? `/api/productos/${productoId}` : '/api/productos';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${getToken()}`
                    },
                    body: JSON.stringify({ nombre, precio, descripcion, imagen })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    successDiv.textContent = data.mensaje;
                    successDiv.style.display = 'block';
                    document.getElementById('agregarProductoForm').reset();
                    if (editando) {
                        cancelarEdicion();
                    }
                    cargarProductos();
                } else {
                    throw new Error(data.mensaje);
                }
            } catch (error) {
                errorDiv.textContent = error.message;
                errorDiv.style.display = 'block';
            }
        });
        
        // Eliminar producto
        async function eliminarProducto(id) {
            if (!confirm('¿Estás seguro de que deseas eliminar este producto?')) return;
            
            try {
                const response = await fetch(`/api/productos/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${getToken()}`
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    cargarProductos();
                } else {
                    alert(data.mensaje);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
        
        // Cargar productos al abrir la página
        cargarProductos();
    </script>
</body>
</html>

