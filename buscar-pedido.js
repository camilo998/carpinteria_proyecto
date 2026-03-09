const mysql = require('mysql2/promise');

async function buscarPedido() {
  let connection;
  try {
    connection = await mysql.createConnection({
      host: '127.0.0.1',
      user: 'root',
      database: 'carpintin_don_gusto'
    });

    console.log('✓ Conectado a la base de datos\n');

    // Buscar por ID 2
    console.log('═══ BÚSQUEDA POR ID 2 ═══\n');
    const [pedidos] = await connection.query(`
      SELECT p.id, c.nombre, c.email, c.telefono, c.direccion, p.total, p.metodo_pago, p.fecha_entrega, p.notas, pd.producto_id, pr.nombre as producto
      FROM pedidos p 
      JOIN clientes c ON p.cliente_id = c.id
      JOIN pedidos_detalle pd ON p.id = pd.pedido_id
      JOIN productos pr ON pd.producto_id = pr.id
      WHERE p.id = 2
    `);

    if (pedidos.length > 0) {
      pedidos.forEach(pedido => {
        console.log('📦 PEDIDO ENCONTRADO');
        console.log('─────────────────');
        console.log(`ID Pedido: ${pedido.id}`);
        console.log(`Cliente: ${pedido.nombre}`);
        console.log(`Email: ${pedido.email}`);
        console.log(`Teléfono: ${pedido.telefono}`);
        console.log(`Dirección: ${pedido.direccion}`);
        console.log(`Producto: ${pedido.producto}`);
        console.log(`Total: $${pedido.total}`);
        console.log(`Método de pago: ${pedido.metodo_pago}`);
        console.log(`Fecha entrega: ${pedido.fecha_entrega}`);
        console.log(`Notas/Información adicional: ${pedido.notas || 'Sin notas'}`);
      });
    } else {
      console.log('❌ No se encontró pedido con ID 2\n');
    }

    // Buscar por nombre David y email con camilonunez
    console.log('\n═══ BÚSQUEDA POR NOMBRE Y EMAIL ═══\n');
    const [pedidos2] = await connection.query(`
      SELECT p.id, c.nombre, c.email, c.telefono, c.direccion, p.total, p.metodo_pago, p.fecha_entrega, p.notas, pd.producto_id, pr.nombre as producto
      FROM pedidos p 
      JOIN clientes c ON p.cliente_id = c.id
      JOIN pedidos_detalle pd ON p.id = pd.pedido_id
      JOIN productos pr ON pd.producto_id = pr.id
      WHERE (c.nombre LIKE '%David%' OR c.nombre LIKE '%Camilo%') 
      AND c.email LIKE '%camilonunez%'
    `);

    if (pedidos2.length > 0) {
      pedidos2.forEach(pedido => {
        console.log('📦 PEDIDO ENCONTRADO');
        console.log('─────────────────');
        console.log(`ID Pedido: ${pedido.id}`);
        console.log(`Cliente: ${pedido.nombre}`);
        console.log(`Email: ${pedido.email}`);
        console.log(`Teléfono: ${pedido.telefono}`);
        console.log(`Dirección: ${pedido.direccion}`);
        console.log(`Producto: ${pedido.producto}`);
        console.log(`Total: $${pedido.total}`);
        console.log(`Método de pago: ${pedido.metodo_pago}`);
        console.log(`Fecha entrega: ${pedido.fecha_entrega}`);
        console.log(`Notas/Información adicional: ${pedido.notas || 'Sin notas'}`);
      });
    } else {
      console.log('❌ No se encontró pedido con esos criterios');
    }

    // Mostrar todos los pedidos
    console.log('\n═══ TODOS LOS PEDIDOS ═══\n');
    const [todosPedidos] = await connection.query(`
      SELECT p.id, c.nombre, c.email, p.total, p.metodo_pago, p.fecha_pedido
      FROM pedidos p 
      JOIN clientes c ON p.cliente_id = c.id
      ORDER BY p.id DESC
    `);

    if (todosPedidos.length > 0) {
      todosPedidos.forEach(pedido => {
        console.log(`ID: ${pedido.id} | Cliente: ${pedido.nombre} | Email: ${pedido.email} | Total: $${pedido.total} | Pago: ${pedido.metodo_pago} | Fecha: ${pedido.fecha_pedido}`);
      });
    } else {
      console.log('No hay pedidos en la base de datos');
    }

    await connection.end();
    console.log('\n✓ Búsqueda completada');
    process.exit(0);
  } catch (error) {
    console.error('❌ Error:', error.message);
    if (connection) await connection.end();
    process.exit(1);
  }
}

buscarPedido();
