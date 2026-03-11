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
                    mensaje: 'el producto no existe perro!!!'
                })
            }else{
                response.json(producto)

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

}

module.exports = ProductoController
