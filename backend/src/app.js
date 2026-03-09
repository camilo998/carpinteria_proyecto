const express = require('express')
const path = require('path')
require('dotenv').config({ path: path.join(__dirname, '.env') })
const cors = require('cors')
const ProductosRouter = require('./routes/productosRoutes')
const AuthRoutes = require('./routes/authRoutes')
const { verificarToken } = require('./middleware/authMiddleware')
const APP = express()
const PORT =  process.env.PORT || 3000

// Debug: mostrar valores cargados
console.log('PORT:', PORT)
console.log('DB_NAME:', process.env.DB_NAME)
APP.use(cors())
APP.use(express.json())

// Servir archivos estáticos desde frontend
const frontendPath = path.join(__dirname, '../../frontend');
console.log('Sirviendo archivos estáticos desde:', frontendPath);
console.log('Directorio existe:', require('fs').existsSync(frontendPath));

// ====================
// API RUTAS - PRIMERO
// ====================

// Rutas de autenticación (públicas)
APP.use('/api/auth', AuthRoutes)

// Rutas de productos - DEBEN IR ANTES DE ARCHIVOS ESTÁTICOS
APP.use('/api/productos', ProductosRouter)

// ====================
// ARCHIVOS ESTÁTICOS - DESPUÉS
// ====================

// Archivos estáticos - maneja todas las páginas HTML automáticamente
APP.use(express.static(frontendPath))

// Rutas específicas para páginas HTML
APP.get('/login.html', (req, res) => {
    res.sendFile(path.join(frontendPath, 'views/Carpintin-Don-Gusto/login.html'));
});

APP.get('/index.html', (req, res) => {
    res.sendFile(path.join(frontendPath, 'views/Carpintin-Don-Gusto/index.html'));
});

APP.get('/sobre-nosotros.html', (req, res) => {
    res.sendFile(path.join(frontendPath, 'views/Carpintin-Don-Gusto/sobre-nosotros.html'));
});

APP.get('/productos.html', (req, res) => {
    res.sendFile(path.join(frontendPath, 'views/Carpintin-Don-Gusto/productos.html'));
});

APP.get('/panel-admin.html', (req, res) => {
    res.sendFile(path.join(frontendPath, 'views/Carpintin-Don-Gusto/panel-admin.html'));
});

// Ruta raíz - servir página de login HTML
APP.get('/', (request, response) => {
    const filePath = path.join(frontendPath, 'views/Carpintin-Don-Gusto/login.html');
    response.sendFile(filePath);
});

// Ruta para el favicon
APP.get('/favicon.ico', (req, res) => {
    res.sendFile(path.join(frontendPath, 'views/Carpintin-Don-Gusto/img/logo.jpg'));
});

// Ruta para admin
APP.get('/admin', (request, response) => {
    // Verificar token
    const token = request.headers.authorization?.split(' ')[1] || request.query.token;
    if (!token) {
        return response.redirect('/');
    }
    
    try {
        const jwt = require('jsonwebtoken');
        const decoded = jwt.verify(token, 'tu_clave_secreta_super_segura');
        if (decoded.rol !== 'admin') {
            return response.redirect('/');
        }
        
        response.send(`
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Panel Administrativo - Carpintería Don Gusto</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: Arial, sans-serif; background: #f5f5f5; }
                .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center; }
                .container { max-width: 1200px; margin: 20px auto; padding: 0 20px; }
                .section { background: white; padding: 30px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .section h2 { color: #333; margin-bottom: 20px; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
                .form-group { margin-bottom: 15px; }
                .form-group label { display: block; margin-bottom: 5px; color: #333; font-weight: 600; }
                .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
                .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
                .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; transition: transform 0.2s; }
                .btn-add { background: #4CAF50; color: white; }
                .btn-edit { background: #ff9800; color: white; }
                .btn-delete { background: #f44336; color: white; padding: 8px 15px; font-size: 14px; }
                .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-top: 20px; }
                .product-card { background: #f9f9f9; border: 1px solid #ddd; border-radius: 8px; padding: 15px; text-align: center; }
                .product-card img { width: 100%; height: 150px; object-fit: cover; border-radius: 5px; margin-bottom: 10px; }
                .product-card h3 { color: #333; margin-bottom: 10px; }
                .product-card p { color: #666; font-size: 14px; margin-bottom: 8px; }
                .product-price { color: #667eea; font-size: 18px; font-weight: bold; margin-bottom: 15px; }
                .product-actions { display: flex; gap: 5px; }
                .error, .success { padding: 10px; border-radius: 5px; margin-bottom: 15px; display: none; }
                .error { color: #d32f2f; background: #ffebee; }
                .success { color: #388e3c; background: #e8f5e9; }
            </style>
        </head>
        <body>
            <div class="navbar">
                <h1>🔧 Panel Administrativo</h1>
                <button class="btn" onclick="logout()" style="background: rgba(255,255,255,0.3); color: white; border: 2px solid white;">Cerrar Sesión</button>
            </div>
            
            <div class="container">
                <div class="section">
                    <h2 id="formTitle">➕ Agregar Nuevo Producto</h2>
                    <div class="error" id="errorMsg"></div>
                    <div class="success" id="successMsg"></div>
                    
                    <form id="agregarProductoForm">
                        <input type="hidden" id="productoId" name="productoId">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nombre">Nombre del Producto</label>
                                <input type="text" id="nombre" name="nombre" required>
                            </div>
                            <div class="form-group">
                                <label for="precio">Precio</label>
                                <input type="number" id="precio" name="precio" step="0.01" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea id="descripcion" name="descripcion" rows="4"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="imagen">URL de Imagen</label>
                            <input type="text" id="imagen" name="imagen">
                        </div>
                        
                        <button type="submit" class="btn btn-add" id="submitBtn">Agregar Producto</button>
                        <button type="button" class="btn" onclick="cancelarEdicion()" id="cancelBtn" style="background: #666; color: white; display: none;">Cancelar</button>
                    </form>
                </div>
                
                <div class="section">
                    <h2>📦 Productos del Sistema</h2>
                    <div id="productosContainer" class="products-grid">
                        <p>Cargando productos...</p>
                    </div>
                </div>
            </div>
            
            <script>
                const token = '${token}';
                let editando = false;
                
                function logout() {
                    localStorage.clear();
                    window.location.href = '/login.html';
                }
                
                function cancelarEdicion() {
                    editando = false;
                    document.getElementById('agregarProductoForm').reset();
                    document.getElementById('formTitle').textContent = '➕ Agregar Nuevo Producto';
                    document.getElementById('submitBtn').textContent = 'Agregar Producto';
                    document.getElementById('cancelBtn').style.display = 'none';
                }
                
                async function editarProducto(id) {
                    try {
                        const response = await fetch(\`/api/productos/\${id}\`, {
                            headers: {
                                'Authorization': \`Bearer \${token}\`
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
                            document.getElementById('formTitle').textContent = '✏️ Editar Producto';
                            document.getElementById('submitBtn').textContent = 'Actualizar Producto';
                            document.getElementById('cancelBtn').style.display = 'inline-block';
                            
                            // Scroll to form
                            document.getElementById('agregarProductoForm').scrollIntoView({ behavior: 'smooth' });
                        } else {
                            alert('Error al cargar producto');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al cargar producto');
                    }
                }
                
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
                
                function mostrarProductos(productos) {
                    const container = document.getElementById('productosContainer');
                    
                    if (productos.length === 0) {
                        container.innerHTML = '<p>No hay productos aún</p>';
                        return;
                    }
                    
                    container.innerHTML = productos.map(prod => \`
                        <div class="product-card">
                            <img src="\${prod.imagen || 'img/logo.jpg'}" alt="\${prod.nombre}" onerror="this.src='img/logo.jpg'">
                            <h3>\${prod.nombre}</h3>
                            <p>\${prod.descripcion || 'Sin descripción'}</p>
                            <div class="product-price">$\${parseFloat(prod.precio).toFixed(2)}</div>
                            <div class="product-actions">
                                <button class="btn btn-edit" onclick="editarProducto(\${prod.id})">Editar</button>
                                <button class="btn btn-delete" onclick="eliminarProducto(\${prod.id})">Eliminar</button>
                            </div>
                        </div>
                    \`).join('');
                }
                
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
                        const url = editando ? \`/api/productos/\${productoId}\` : '/api/productos';
                        
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': \`Bearer \${token}\`
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
                
                async function eliminarProducto(id) {
                    if (!confirm('¿Estás seguro de que deseas eliminar este producto?')) return;
                    
                    try {
                        const response = await fetch(\`/api/productos/\${id}\`, {
                            method: 'DELETE',
                            headers: {
                                'Authorization': \`Bearer \${token}\`
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
                
                cargarProductos();
            </script>
        </body>
        </html>
        `);
    } catch (error) {
        response.redirect('/');
    }
});

// Ruta para productos
APP.get('/productos', (request, response) => {
    // Verificar token
    const token = request.headers.authorization?.split(' ')[1] || request.query.token;
    if (!token) {
        return response.redirect('/');
    }
    
    try {
        const jwt = require('jsonwebtoken');
        const decoded = jwt.verify(token, 'tu_clave_secreta_super_segura');
        if (decoded.rol !== 'usuario') {
            return response.redirect('/');
        }
        
        response.send(`
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Productos - Carpintería Don Gusto</title>
            <style>
                body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
                .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center; }
                .container { max-width: 1200px; margin: 20px auto; }
                .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; }
                .product-card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 20px; }
                .product-name { font-size: 18px; font-weight: bold; color: #333; margin-bottom: 10px; }
                .product-price { font-size: 20px; font-weight: bold; color: #667eea; }
                .btn-comprar { background: #4CAF50; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
            </style>
        </head>
        <body>
            <div class="navbar">
                <h1>🪵 Carpintería Don Gusto</h1>
                <div>
                    <span>Hola, ${decoded.email.split('@')[0]}</span>
                    <button onclick="logout()" style="background: rgba(255,255,255,0.3); color: white; border: 2px solid white; padding: 10px 20px; margin-left: 20px;">Cerrar Sesión</button>
                </div>
            </div>
            
            <div class="container">
                <h2>📦 Nuestros Productos</h2>
                <div id="productosContainer" class="products-grid">
                    Cargando productos...
                </div>
            </div>
            
            <script>
                const token = '${token}';
                
                function logout() {
                    localStorage.clear();
                    window.location.href = '/login.html';
                }
                
                async function cargarProductos() {
                    try {
                        const response = await fetch('/api/productos');
                        const data = await response.json();
                        
                        if (data.success && data.datos) {
                            mostrarProductos(data.datos);
                        }
                    } catch (error) {
                        console.error('Error al cargar productos:', error);
                        document.getElementById('productosContainer').innerHTML = 'Error al cargar productos';
                    }
                }
                
                function mostrarProductos(productos) {
                    const container = document.getElementById('productosContainer');
                    
                    if (productos.length === 0) {
                        container.innerHTML = 'No hay productos disponibles';
                        return;
                    }
                    
                    container.innerHTML = productos.map(prod => 
                        '<div class="product-card">' +
                            '<div class="product-name">' + prod.nombre + '</div>' +
                            '<div class="product-price">$ ' + parseFloat(prod.precio).toFixed(2) + '</div>' +
                            '<button class="btn-comprar" onclick="comprarProducto(' + prod.id + ', \\'' + prod.nombre.replace(/'/g, '\\\\\\\'') + '\\', ' + prod.precio + ')">Comprar</button>' +
                        '</div>'
                    ).join('');
                }
                
                async function comprarProducto(id, nombre, precio) {
                    if (confirm('¿Comprar ' + nombre + ' por $' + precio + '?')) {
                        try {
                            const response = await fetch('/api/productos/pedido', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Authorization': 'Bearer ' + token
                                },
                                body: JSON.stringify({ 
                                    producto_id: id, 
                                    cantidad: 1, 
                                    detalles: nombre,
                                    nombre_cliente: '${decoded.email}',
                                    telefono: '123456789',
                                    direccion: 'Dirección de ejemplo',
                                    email: '${decoded.email}',
                                    metodo_pago: 'efectivo'
                                })
                            });
                            
                            const data = await response.json();
                            
                            if (data.success) {
                                alert('✅ ¡Producto agregado a tu pedido exitosamente!');
                            } else {
                                alert('Error: ' + data.mensaje);
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('Error al procesar la compra');
                        }
                    }
                }
                
                cargarProductos();
            </script>
        </body>
        </html>
        `);
    } catch (error) {
        console.error('Error en /productos:', error);
        response.redirect('/');
    }
});

APP.listen(PORT,() => {
    console.log(`servidor corriendo en puerto ${process.env.PORT}`)
})

