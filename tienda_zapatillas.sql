-- *******************************************************************
-- SQL SCRIPT PARA POSTGRESQL (Render)
-- ESTE SCRIPT CREA LA ESTRUCTURA DE LA BASE DE DATOS `tienda_zapatillas`
-- *******************************************************************

-- --------------------------------------------------------
-- 1. Tabla: usuarios
-- --------------------------------------------------------

DROP TABLE IF EXISTS usuarios CASCADE;

CREATE TABLE usuarios (
    -- id: SERIAL es el equivalente a INT NOT NULL AUTO_INCREMENT en MySQL
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol VARCHAR(15) NOT NULL DEFAULT 'cliente'
);

-- --------------------------------------------------------
-- 2. Tabla: productos
-- --------------------------------------------------------

DROP TABLE IF EXISTS productos CASCADE;

CREATE TABLE productos (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    -- Tipo DECIMAL con precisión 10 y escala 2
    precio DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL,
    descripcion TEXT DEFAULT NULL,
    imagen VARCHAR(255) NOT NULL
);

-- --------------------------------------------------------
-- 3. Tabla: pedidos
-- --------------------------------------------------------

DROP TABLE IF EXISTS pedidos CASCADE;

CREATE TABLE pedidos (
    id SERIAL PRIMARY KEY,
    -- id_usuario es la clave foránea a la tabla usuarios
    id_usuario INT NOT NULL,
    -- TIMESTAMP NOT NULL DEFAULT NOW() reemplaza a DATETIME DEFAULT current_timestamp()
    fecha_pedido TIMESTAMP NOT NULL DEFAULT NOW(),
    total DECIMAL(10, 2) NOT NULL,
    estado_pedido VARCHAR(50) NOT NULL DEFAULT 'Pendiente'
);

-- --------------------------------------------------------
-- 4. Tabla: detalles_pedido
-- --------------------------------------------------------

DROP TABLE IF EXISTS detalles_pedido CASCADE;

CREATE TABLE detalles_pedido (
    -- NOTA: Mantengo el ID autoincremental para la clave primaria, aunque un índice compuesto también es viable.
    id SERIAL PRIMARY KEY,
    -- FK al pedido
    pedido_id INT NOT NULL,
    -- FK al producto
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unidad DECIMAL(10, 2) NOT NULL
);

-- *******************************************************************
-- DEFINICIÓN DE CLAVES FORÁNEAS (RESTICCIONES)
-- *******************************************************************

-- 1. Restricción para pedidos
ALTER TABLE pedidos
    ADD CONSTRAINT fk_usuario
    FOREIGN KEY (id_usuario)
    REFERENCES usuarios (id);

-- 2. Restricciones para detalles_pedido
ALTER TABLE detalles_pedido
    ADD CONSTRAINT fk_pedido
    FOREIGN KEY (pedido_id)
    REFERENCES pedidos (id),
    ADD CONSTRAINT fk_producto
    FOREIGN KEY (producto_id)
    REFERENCES productos (id);

-- *******************************************************************
-- VOLCADO DE DATOS (OPCIONAL: si quieres datos de prueba)
-- Se ajustan los INSERT para no incluir el campo ID, pues SERIAL lo maneja.
-- *******************************************************************

-- --------------------------------------------------------
-- Volcado de datos para la tabla usuarios
-- --------------------------------------------------------
INSERT INTO usuarios (nombre, email, password, rol) VALUES
('Admin Tienda', 'admin@tienda.com', '$2y$10$JCFmX1wO4s3gSyrvxbYj2Ovr2eNSSBF8Q3JDXfGHYJ99ppX4vkpa', 'admin'),
('Maria', 'cliente@tienda.com', '$2y$10$GUKHXCPTGvOyL346uSRtju6YzyvJOam9p93Cl5nhn1S/042slfxB.', 'cliente');

-- --------------------------------------------------------
-- Volcado de datos para la tabla productos
-- --------------------------------------------------------
INSERT INTO productos (nombre, precio, stock, descripcion, imagen) VALUES
('Air Max 97', 159.99, 100, 'Un clásico moderno que combina estilo futurista y rendimiento urbano.', 'airmax.jpg'),
('Yeezy Boost 350', 220.00, 100, 'combinan una estética vanguardista con materiales ultraligeros y una suela Boost que garantiza comodidad durante todo el día.', 'yeezy.jpg'),
('Converse Chuck Taylor', 65.00, 100, 'Zapatillas icónicas que combinan estilo clásico y comodidad.', 'converse.jpg');

-- --------------------------------------------------------
-- Volcado de datos para la tabla pedidos (NOTA: Asume que ID de usuario 1 y 2 existen)
-- --------------------------------------------------------
INSERT INTO pedidos (id_usuario, total, estado_pedido) VALUES
(2, 224.99, 'Pendiente'),
(2, 440.00, 'Pendiente'),
(2, 444.99, 'Pendiente'),
(2, 65.00, 'Pendiente'),
(2, 65.00, 'Pendiente'),
(2, 65.00, 'Pendiente'),
(1, 65.00, 'Pendiente');

-- --------------------------------------------------------
-- Volcado de datos para la tabla detalles_pedido (NOTA: Usa los IDs de pedidos y productos creados)
-- --------------------------------------------------------
INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unidad) VALUES
(1, 1, 1, 159.99),
(1, 3, 1, 65.00),
(2, 2, 2, 220.00),
(3, 3, 1, 65.00),
(3, 2, 1, 220.00),
(3, 1, 1, 159.99),
(4, 3, 1, 65.00),
(5, 3, 1, 65.00),
(6, 3, 1, 65.00),
(7, 3, 1, 65.00);
