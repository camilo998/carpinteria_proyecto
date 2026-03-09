const mysql = require('mysql2/promise');
const fs = require('fs');
const path = require('path');

async function setupDatabase() {
  let connection;
  try {
    // Crear conexión inicial sin especificar base de datos
    connection = await mysql.createConnection({
      host: '127.0.0.1',
      user: 'root',
      port: 3306,
      multipleStatements: true
    });

    console.log('✓ Conectado a MySQL');

    // Leer el archivo SQL
    const sqlPath = path.join(__dirname, 'database.sql');
    const sqlScript = fs.readFileSync(sqlPath, 'utf8');

    // Ejecutar el script
    await connection.query(sqlScript);

    console.log('✓ Base de datos creada exitosamente');
    console.log('✓ Tablas creadas');
    console.log('✓ Datos de ejemplo insertados');

    await connection.end();
    console.log('\n✅ Setup completado. ¡Los productos deberían verse ahora!');
    process.exit(0);
  } catch (error) {
    console.error('❌ Error al configurar la base de datos:', error.message);
    if (connection) await connection.end();
    process.exit(1);
  }
}

setupDatabase();
