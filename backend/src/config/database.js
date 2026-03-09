const mysql = require('mysql2')
const path = require('path')
require('dotenv').config({ path: path.join(__dirname, '../../.env') })

const poolConfig = {
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    port: process.env.DB_PORT || 3306,
    database: process.env.DB_NAME,
    connectionLimit: 10,
}

// solo agregar password si existe y no está vacío
if (process.env.DB_PASSWORD && process.env.DB_PASSWORD.trim() !== '') {
    poolConfig.password = process.env.DB_PASSWORD
}

const pool = mysql.createPool(poolConfig)

// crear versión promise y también comprobar la conexión al arrancar
const conectionPromise = pool.promise()

// prueba simple para verificar que la base de datos responde
conectionPromise.query('SELECT 1')
    .then(() => {
        console.log('MySQL conectado correctamente a', process.env.DB_NAME)
    })
    .catch(err => {
        console.error('Error de conexión MySQL:', err.message || err)
    })

module.exports = conectionPromise