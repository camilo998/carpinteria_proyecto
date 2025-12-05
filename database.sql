-- ============================================
-- BASE DE DATOS: CARPINTERÍA DON GUSTO
-- Versión mejorada con normalización y buenas prácticas
-- ============================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS carpintin_don_gusto 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE carpintin_don_gusto;

-- ============================================
-- TABLAS PRINCIPALES
-- ============================================

-- Tabla de categorías (normalización)
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    icono VARCHAR(255),
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de productos (mejorada)
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    precio_descuento DECIMAL(10, 2) DEFAULT NULL,
    imagen VARCHAR(255),
    categoria_id INT NOT NULL,
    stock INT DEFAULT 0,
    material VARCHAR(100),
    dimensiones VARCHAR(100), -- ej: "120x80x75 cm"
    peso DECIMAL(8, 2), -- en kg
    activo BOOLEAN DEFAULT TRUE,
    destacado BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT,
    INDEX idx_categoria (categoria_id),
    INDEX idx_precio (precio),
    INDEX idx_activo (activo),
    INDEX idx_destacado (destacado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de imágenes adicionales de productos
CREATE TABLE productos_imagenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    imagen VARCHAR(255) NOT NULL,
    orden INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_producto (producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de clientes (normalización)
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    email VARCHAR(255),
    direccion TEXT,
    ciudad VARCHAR(100),
    departamento VARCHAR(100),
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_telefono (telefono),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de estados de pedidos
CREATE TABLE estados_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    color VARCHAR(7) DEFAULT '#000000', -- Color hex para UI
    orden INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de pedidos (mejorada)
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    descuento DECIMAL(10, 2) DEFAULT 0.00,
    total DECIMAL(10, 2) NOT NULL,
    metodo_pago ENUM('efectivo', 'transferencia', 'tarjeta', 'nequi', 'daviplata') NOT NULL,
    estado_id INT DEFAULT 1,
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_entrega DATE,
    direccion_entrega TEXT,
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT,
    FOREIGN KEY (estado_id) REFERENCES estados_pedido(id) ON DELETE RESTRICT,
    INDEX idx_cliente (cliente_id),
    INDEX idx_estado (estado_id),
    INDEX idx_fecha_pedido (fecha_pedido),
    INDEX idx_fecha_entrega (fecha_entrega)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de detalle de pedidos (relación muchos a muchos)
CREATE TABLE pedidos_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    personalizacion TEXT, -- Detalles de personalización
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT,
    INDEX idx_pedido (pedido_id),
    INDEX idx_producto (producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de historial de estados de pedidos
CREATE TABLE pedidos_historial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    estado_id INT NOT NULL,
    comentario TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (estado_id) REFERENCES estados_pedido(id) ON DELETE RESTRICT,
    INDEX idx_pedido (pedido_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSERTAR DATOS INICIALES
-- ============================================

-- Insertar categorías
INSERT INTO categorias (nombre, descripcion, icono) VALUES
('Mesa', 'Mesas artesanales para comedor y sala', 'fas fa-table'),
('Clóset', 'Armarios y clósets personalizados', 'fas fa-door-open'),
('Escritorio', 'Escritorios funcionales para oficina y hogar', 'fas fa-laptop-house'),
('Cama', 'Camas y bases de madera', 'fas fa-bed'),
('Estantería', 'Estanterías y bibliotecas', 'fas fa-book'),
('Silla', 'Sillas y bancos artesanales', 'fas fa-chair');

-- Insertar estados de pedido
INSERT INTO estados_pedido (nombre, descripcion, color, orden) VALUES
('Pendiente', 'Pedido recibido, pendiente de confirmación', '#FFA500', 1),
('Confirmado', 'Pedido confirmado, en proceso de fabricación', '#2196F3', 2),
('En Producción', 'Producto en proceso de fabricación', '#9C27B0', 3),
('Listo', 'Producto terminado, listo para entrega', '#4CAF50', 4),
('En Camino', 'Producto en camino al cliente', '#00BCD4', 5),
('Entregado', 'Pedido entregado al cliente', '#8BC34A', 6),
('Cancelado', 'Pedido cancelado', '#F44336', 7);

-- Insertar productos mejorados
INSERT INTO productos (nombre, descripcion, precio, precio_descuento, imagen, categoria_id, stock, material, dimensiones, peso, destacado) VALUES
-- Mesas
('Mesa Artesanal Rústica', 'Mesa artesanal de madera maciza con acabado rústico, perfecta para comedores elegantes. Diseño único que combina tradición y modernidad.', 400000, 380000, 'img/Comedor-Rustico.jpg', 1, 5, 'Madera de Roble', '180x90x75 cm', 45.5, TRUE),
('Mesa Resistente Colonial', 'Mesa resistente con acabados finos y detalles coloniales, ideal para cualquier hogar. Construcción robusta que garantiza durabilidad.', 400000, NULL, 'img/mesa2.jpg', 1, 3, 'Cedro', '160x85x75 cm', 40.0, FALSE),
('Mesa Elegante Moderna', 'Mesa elegante y funcional con diseño contemporáneo, ideal para añadir calidez y estilo a cualquier espacio de comedor.', 400000, 370000, 'img/mesa3.png', 1, 8, 'Nogal', '200x100x75 cm', 50.0, TRUE),

-- Clósets
('Clóset Elegante Premium', 'Armario elegante y versátil de dos puertas, diseñado para aportar estilo y sofisticación a tus espacios. Incluye compartimentos interiores.', 400000, NULL, 'img/mueble.jpg', 2, 2, 'Pino', '120x60x200 cm', 65.0, FALSE),
('Clóset con Acabados Finos', 'Armario con acabados finos y herrajes de calidad, diseñado para maximizar el espacio. Incluye barra para colgar y cajones.', 400000, 380000, 'img/mueble2.jpg', 2, 4, 'Roble', '150x70x210 cm', 75.0, TRUE),
('Clóset Funcional con Espejo', 'Clóset elegante y funcional con amplio espacio de almacenamiento y espejo de cuerpo completo integrado.', 400000, NULL, 'img/espejo.jpg', 2, 3, 'MDF Enchapado', '140x65x200 cm', 60.0, FALSE),

-- Escritorios
('Escritorio Moderno Ejecutivo', 'Escritorio moderno y funcional con múltiples cajones, ideal para oficinas y estudios. Diseño ergonómico y espacioso.', 300000, 280000, 'img/escritorio.webp', 3, 10, 'Madera MDF', '140x70x75 cm', 35.0, TRUE),
('Escritorio Compacto Minimalista', 'Escritorio compacto con diseño minimalista y líneas limpias, perfecto para espacios pequeños y home office.', 250000, NULL, 'img/escritoriodos..avif', 3, 15, 'Pino', '100x50x75 cm', 20.0, FALSE),
('Escritorio Elegante L-Shape', 'Escritorio en forma de L con amplio espacio de trabajo, ideal para profesionales que necesitan múltiples monitores.', 350000, 330000, 'img/escritorio3..jpg', 3, 6, 'Nogal', '160x140x75 cm', 45.0, TRUE);

-- Insertar imágenes adicionales de productos (ejemplo)
INSERT INTO productos_imagenes (producto_id, imagen, orden) VALUES
(1, 'img/mesa1-detalle1.jpg', 1),
(1, 'img/mesa1-detalle2.jpg', 2),
(2, 'img/mesa2-detalle1.jpg', 1);

-- ============================================
-- VISTAS ÚTILES
-- ============================================

-- Vista de productos con información completa
CREATE VIEW v_productos_completo AS
SELECT 
    p.id,
    p.nombre,
    p.descripcion,
    p.precio,
    p.precio_descuento,
    p.imagen,
    c.nombre AS categoria,
    c.id AS categoria_id,
    p.stock,
    p.material,
    p.dimensiones,
    p.peso,
    p.activo,
    p.destacado,
    CASE 
        WHEN p.precio_descuento IS NOT NULL 
        THEN ROUND(((p.precio - p.precio_descuento) / p.precio) * 100, 0)
        ELSE 0 
    END AS porcentaje_descuento
FROM productos p
INNER JOIN categorias c ON p.categoria_id = c.id;

-- Vista de pedidos con información completa
CREATE VIEW v_pedidos_completo AS
SELECT 
    p.id AS pedido_id,
    p.total,
    p.metodo_pago,
    p.fecha_pedido,
    p.fecha_entrega,
    ep.nombre AS estado,
    ep.color AS estado_color,
    c.nombre AS cliente_nombre,
    c.telefono AS cliente_telefono,
    c.email AS cliente_email,
    c.direccion AS cliente_direccion
FROM pedidos p
INNER JOIN clientes c ON p.cliente_id = c.id
INNER JOIN estados_pedido ep ON p.estado_id = ep.id;

-- ============================================
-- PROCEDIMIENTOS ALMACENADOS
-- ============================================

DELIMITER //

-- Procedimiento para crear un pedido completo
CREATE PROCEDURE sp_crear_pedido(
    IN p_cliente_nombre VARCHAR(255),
    IN p_telefono VARCHAR(20),
    IN p_email VARCHAR(255),
    IN p_direccion TEXT,
    IN p_metodo_pago VARCHAR(50),
    IN p_fecha_entrega DATE,
    IN p_notas TEXT
)
BEGIN
    DECLARE v_cliente_id INT;
    
    -- Verificar si el cliente existe, si no, crearlo
    SELECT id INTO v_cliente_id FROM clientes WHERE telefono = p_telefono LIMIT 1;
    
    IF v_cliente_id IS NULL THEN
        INSERT INTO clientes (nombre, telefono, email, direccion)
        VALUES (p_cliente_nombre, p_telefono, p_email, p_direccion);
        SET v_cliente_id = LAST_INSERT_ID();
    END IF;
    
    -- Retornar el ID del cliente para usar en el pedido
    SELECT v_cliente_id AS cliente_id;
END //

-- Procedimiento para actualizar estado de pedido
CREATE PROCEDURE sp_actualizar_estado_pedido(
    IN p_pedido_id INT,
    IN p_estado_id INT,
    IN p_comentario TEXT
)
BEGIN
    -- Actualizar el pedido
    UPDATE pedidos SET estado_id = p_estado_id WHERE id = p_pedido_id;
    
    -- Registrar en el historial
    INSERT INTO pedidos_historial (pedido_id, estado_id, comentario)
    VALUES (p_pedido_id, p_estado_id, p_comentario);
END //

DELIMITER ;

-- ============================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- ============================================

-- Índice de texto completo para búsqueda de productos
ALTER TABLE productos ADD FULLTEXT INDEX idx_fulltext_nombre_desc (nombre, descripcion);

-- ============================================
-- DATOS DE EJEMPLO ADICIONALES
-- ============================================

-- Cliente de ejemplo
INSERT INTO clientes (nombre, telefono, email, direccion, ciudad, departamento) VALUES
('Juan Pérez', '3001234567', 'juan.perez@email.com', 'Calle 123 #45-67', 'Bogotá', 'Cundinamarca'),
('María González', '3109876543', 'maria.gonzalez@email.com', 'Carrera 50 #30-20', 'Medellín', 'Antioquia');

-- Pedido de ejemplo
INSERT INTO pedidos (cliente_id, subtotal, total, metodo_pago, fecha_entrega, direccion_entrega, notas) VALUES
(1, 400000, 400000, 'transferencia', DATE_ADD(CURDATE(), INTERVAL 15 DAY), 'Calle 123 #45-67', 'Entregar en la mañana');

-- Detalle del pedido de ejemplo
INSERT INTO pedidos_detalle (pedido_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(1, 1, 1, 400000, 400000);

-- Historial del pedido
INSERT INTO pedidos_historial (pedido_id, estado_id, comentario) VALUES
(1, 1, 'Pedido creado y registrado en el sistema');