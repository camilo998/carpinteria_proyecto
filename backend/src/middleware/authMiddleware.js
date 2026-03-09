const jwt = require('jsonwebtoken')

const SECRET_KEY = 'tu_clave_secreta_super_segura'

// Middleware para verificar token
const verificarToken = (request, response, next) => {
    const token = request.headers.authorization?.split(' ')[1]

    if (!token) {
        return response.status(401).json({
            success: false,
            mensaje: 'Acceso no autorizado'
        })
    }

    try {
        const decoded = jwt.verify(token, SECRET_KEY)
        request.usuario = decoded
        next()
    } catch (error) {
        response.status(401).json({
            success: false,
            mensaje: 'Token inválido o expirado'
        })
    }
}

// Middleware para verificar si es admin
const esAdmin = (request, response, next) => {
    if (request.usuario.rol !== 'admin') {
        return response.status(403).json({
            success: false,
            mensaje: 'Acceso denegado. Se requieren permisos de administrador'
        })
    }
    next()
}

module.exports = { verificarToken, esAdmin }
