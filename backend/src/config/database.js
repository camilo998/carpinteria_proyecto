const mysql = require('mysql2')
require('dotenv').config()

const pool = mysql.createPool(
    {
        host: process.env.DB_HOST,
        user: process.env.DB_USER,
        password: process.env.DB_PASSWORD,
        port: process.env.DB_PORT,
        database: process.env.DB_NAME,
        connectionLimit: 10,
    }
)
const conectionPromise = pool.promise()
module.exports = conectionPromise