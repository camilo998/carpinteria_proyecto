const { response } = require('express')
const Model = require('../models/recetaModel')

class ProductoController{
    static async obtenerProductos(request, response){
        try {
            const productos = await Model.obtenerProductos()
            response.json({
                success: true,
                datos: productos
            })
        } catch(err) {
            console.error('Error al obtener productos:', err.message || err)
            response.status(500).json({ success: false, error: err.message })
        }
    }
    static async obtenerPorId(request,response){
        try{
            const {id} = request.params
            const producto = await Model.obtenerPorId(id)
            if(!producto){
              return response.status(404).json({
                    success: false,
                    mensaje: 'El producto no existe'
                })
            }else{
                response.json({
                    success: true,
                    datos: producto
                })
            }
        }catch(error){
            console.error('Error al obtener producto por id:', error.message || error)
            response.status(500).json({ success:false, error: error.message })
        }
    }

    static async crearPedido(request, response){
        try{
            const datos = request.body
            const pedidoId = await Model.crearPedido(datos)
            response.status(201).json({
                success: true,
                mensaje: 'Pedido creado',
                id: pedidoId
            })
        }catch(error){
            response.status(500).json({
                success: false,
                error: error.message
            })
        }
    }

    // MÉTODOS DE ADMINISTRADOR
    static async crearProducto(request, response){
        try{
            const { nombre, descripcion, precio, imagen } = request.body
            
            if (!nombre || !precio) {
                return response.status(400).json({
                    success: false,
                    mensaje: 'Nombre y precio son requeridos'
                })
            }

            const productoId = await Model.crearProducto({ nombre, descripcion, precio, imagen })
            response.status(201).json({
                success: true,
                mensaje: 'Producto creado exitosamente',
                id: productoId
            })
        }catch(error){
            console.error('Error al crear producto:', error.message)
            response.status(500).json({
                success: false,
                error: error.message
            })
        }
    }

    static async actualizarProducto(request, response){
        try{
            const { id } = request.params
            const { nombre, descripcion, precio, imagen } = request.body

            const actualizado = await Model.actualizarProducto(id, { nombre, descripcion, precio, imagen })
            
            if (!actualizado) {
                return response.status(404).json({
                    success: false,
                    mensaje: 'Producto no encontrado'
                })
            }

            response.json({
                success: true,
                mensaje: 'Producto actualizado exitosamente'
            })
        }catch(error){
            console.error('Error al actualizar producto:', error.message)
            response.status(500).json({
                success: false,
                error: error.message
            })
        }
    }

    static async eliminarProducto(request, response){
        try{
            const { id } = request.params

            const eliminado = await Model.eliminarProducto(id)
            
            if (!eliminado) {
                return response.status(404).json({
                    success: false,
                    mensaje: 'Producto no encontrado'
                })
            }

            response.json({
                success: true,
                mensaje: 'Producto eliminado exitosamente'
            })
        }catch(error){
            console.error('Error al eliminar producto:', error.message)
            response.status(500).json({
                success: false,
                error: error.message
            })
        }
    }

}

module.exports = ProductoController
