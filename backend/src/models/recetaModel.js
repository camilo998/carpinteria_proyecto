const DB = require('../config/database')

class ProductoModel {
    static async obtenerProductos(){
        const [rows] = await DB.query(`
            SELECT p.*, c.nombre as categoria
            FROM productos p
            INNER JOIN categorias c ON p.categoria_id = c.id
            WHERE p.activo = 1
            ORDER BY p.destacado DESC, p.id DESC
        `)
        return rows
    }
    static async obtenerPorId(id){
        const [row] = await DB.query(`
            SELECT p.*, c.nombre as categoria
            FROM productos p
            INNER JOIN categorias c ON p.categoria_id = c.id
            WHERE p.id = ?
        `, [id])
        return row
    }
    static async crearPedido(datos){
        const { producto_id, telefono, direccion, nota, fecha_entrega, email, metodo_pago, nombre_cliente } = datos

        let [cliente] = await DB.query('SELECT id FROM clientes WHERE telefono = ?', [telefono])

        let cliente_id
        if (cliente.length === 0) {
            const [result] = await DB.query('INSERT INTO clientes (nombre, telefono, email, direccion) VALUES (?, ?, ?, ?)', [nombre_cliente || 'Cliente', telefono, email, direccion])
            cliente_id = result.insertId
        } else {
            cliente_id = cliente[0].id
        }

        const [producto] = await DB.query('SELECT precio FROM productos WHERE id = ?', [producto_id])
        const precio = producto[0].precio

        const [pedidoResult] = await DB.query('INSERT INTO pedidos (cliente_id, subtotal, total, metodo_pago, fecha_entrega, direccion_entrega, notas) VALUES (?, ?, ?, ?, ?, ?, ?)', [cliente_id, precio, precio, metodo_pago, fecha_entrega, direccion, nota])
        const pedido_id = pedidoResult.insertId

        await DB.query('INSERT INTO pedidos_detalle (pedido_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, 1, ?, ?)', [pedido_id, producto_id, precio, precio])

        await DB.query('INSERT INTO pedidos_historial (pedido_id, estado_id, comentario) VALUES (?, 1, ?)', [pedido_id, 'Pedido creado desde formulario web'])

        return pedido_id
    }

    // MÉTODOS DE ADMINISTRADOR
    static async crearProducto(datos) {
        const { nombre, descripcion, precio, imagen, categoria_id } = datos
        
        // Usar categoría por defecto (1) si no se especifica
        const catId = categoria_id || 1;
        
        try {
            const [result] = await DB.query(
                'INSERT INTO productos (nombre, descripcion, precio, imagen, activo, categoria_id) VALUES (?, ?, ?, ?, 1, ?)',
                [nombre, descripcion, precio, imagen, catId]
            )
            return result.insertId
        } catch (error) {
            throw new Error('Error al crear producto: ' + error.message)
        }
    }

    static async actualizarProducto(id, datos) {
        const { nombre, descripcion, precio, imagen } = datos
        
        try {
            const [result] = await DB.query(
                'UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, imagen = ?, activo = 1 WHERE id = ?',
                [nombre || null, descripcion || null, precio || null, imagen || null, id]
            )
            return result.affectedRows > 0
        } catch (error) {
            throw new Error('Error al actualizar producto: ' + error.message)
        }
    }

    static async eliminarProducto(id) {
        try {
            const [result] = await DB.query(
                'UPDATE productos SET activo = 0 WHERE id = ?',
                [id]
            )
            return result.affectedRows > 0
        } catch (error) {
            throw new Error('Error al eliminar producto: ' + error.message)
        }
    }

}

module.exports = ProductoModel
