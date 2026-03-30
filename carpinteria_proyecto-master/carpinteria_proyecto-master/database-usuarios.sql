-- ============================================
-- TABLA DE USUARIOS - Carpintería Don Gusto
-- ============================================

USE carpintin_don_gusto;

-- Crear tabla de usuarios (actualizada)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100),
    telefono VARCHAR(20),
    rol ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario',
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_rol (rol),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar usuarios de prueba (contraseñas sin encriptar para prueba)
INSERT INTO usuarios (email, password, nombre, apellido, telefono, rol) VALUES
('admin@carpinteria.com', 'admin123', 'Administrador', 'Principal', '3001234567', 'admin'),
('admin2@carpinteria.com', 'admin456', 'Segundo', 'Admin', '3002345678', 'admin'),
('usuario@correo.com', 'usuario123', 'Usuario', 'Demo', '3003456789', 'usuario'),
('juan@correo.com', 'juan123', 'Juan', 'Pérez', '3004567890', 'usuario'),
('maria@correo.com', 'maria123', 'María', 'González', '3005678901', 'usuario');

