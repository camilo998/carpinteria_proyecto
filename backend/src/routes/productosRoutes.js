const express  = require('express')
const router = express.Router()
const ProductoController = require('../controllers/productoController')
const { verificarToken, esAdmin } = require('../middleware/authMiddleware')

// Rutas públicas
router.get('/', ProductoController.obtenerProductos)

// Rutas protegidas para usuarios - DEBE ir antes de /:id
router.post('/pedido', verificarToken, ProductoController.crearPedido)

// Rutas protegidas para administrador
router.post('/', verificarToken, esAdmin, ProductoController.crearProducto)
router.put('/:id', verificarToken, esAdmin, ProductoController.actualizarProducto)
router.delete('/:id', verificarToken, esAdmin, ProductoController.eliminarProducto)

// Ruta pública por ID - DEBE ir al final
router.get('/:id', ProductoController.obtenerPorId)

module.exports = router

