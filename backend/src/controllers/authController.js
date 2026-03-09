const jwt = require('jsonwebtoken')

const SECRET_KEY = 'tu_clave_secreta_super_segura'

// Base de datos de usuarios simulada (cambiar a BD real)
const usuarios = [
    { id: 1, email: 'admin@carpinteria.com', password: 'admin123', rol: 'admin' },
    { id: 2, email: 'usuario@correo.com', password: 'usuario123', rol: 'usuario' }
]

class AuthController {
    static login(request, response) {
        try {
            const { email, password } = request.body

            if (!email || !password) {
                return response.status(400).json({
                    success: false,
                    mensaje: 'Email y contraseña son requeridos'
                })
            }

            // Buscar usuario
            const usuario = usuarios.find(u => u.email === email && u.password === password)

            if (!usuario) {
                return response.status(401).json({
                    success: false,
                    mensaje: 'Email o contraseña incorrectos'
                })
            }

            // Generar token JWT
            const token = jwt.sign(
                { id: usuario.id, email: usuario.email, rol: usuario.rol },
                SECRET_KEY,
                { expiresIn: '24h' }
            )

            response.json({
                success: true,
                mensaje: 'Sesión iniciada',
                token: token,
                usuario: {
                    id: usuario.id,
                    email: usuario.email,
                    rol: usuario.rol
                }
            })
        } catch (error) {
            console.error('Error al iniciar sesión:', error)
            response.status(500).json({
                success: false,
                error: error.message
            })
        }
    }

    static verificarToken(request, response) {
        try {
            const token = request.headers.authorization?.split(' ')[1]

            if (!token) {
                return response.status(401).json({
                    success: false,
                    mensaje: 'Token no proporcionado'
                })
            }

            const decoded = jwt.verify(token, SECRET_KEY)
            response.json({
                success: true,
                usuario: decoded
            })
        } catch (error) {
            response.status(401).json({
                success: false,
                mensaje: 'Token inválido o expirado'
            })
        }
    }
}

module.exports = AuthController
