const { response } = require('express')
const Model = require('../models/recetaModel')

class ProductoController{
    static async obtenerProductos(request, response){
        const productos = await Model.obtenerProductos()
        response.json({
            success: true,
            daticos: productos
        })
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
            throw error
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
