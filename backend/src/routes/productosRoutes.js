const express  = require('express')
const router = express.Router()
const ProductoController = require('../controllers/productoController')

router.get('/', ProductoController.obtenerProductos)
router.get('/:id', ProductoController.obtenerPorId)
router.post('/pedido', ProductoController.crearPedido)

module.exports = router

