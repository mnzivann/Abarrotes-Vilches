-- Crear la base de datos
CREATE DATABASE AbarrotesVilchesDB;
GO

USE AbarrotesVilchesDB;
GO

-- ==========================================
-- CREACIÓN DE TABLAS
-- ==========================================

CREATE TABLE Categorias (
    id_categoria VARCHAR(20) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    estatus VARCHAR(20) DEFAULT 'Activo'
);

CREATE TABLE Productos (
    id_producto VARCHAR(20) PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    id_categoria VARCHAR(20),
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    estatus VARCHAR(20) DEFAULT 'Activo',
    FOREIGN KEY (id_categoria) REFERENCES Categorias(id_categoria)
);

CREATE TABLE Empleados (
    id_empleado VARCHAR(20) PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol VARCHAR(50) NOT NULL,
    estatus VARCHAR(20) DEFAULT 'Activo'
);

CREATE TABLE Ventas (
    ticket VARCHAR(20) PRIMARY KEY,
    fecha DATETIME DEFAULT GETDATE(),
    id_empleado VARCHAR(20),
    total DECIMAL(10,2) NOT NULL,
    estado VARCHAR(20) DEFAULT 'Completada',
    FOREIGN KEY (id_empleado) REFERENCES Empleados(id_empleado)
);

CREATE TABLE DetalleVenta (
    id_detalle INT IDENTITY(1,1) PRIMARY KEY,
    ticket VARCHAR(20),
    id_producto VARCHAR(20),
    cantidad INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (ticket) REFERENCES Ventas(ticket),
    FOREIGN KEY (id_producto) REFERENCES Productos(id_producto)
);

CREATE TABLE Mermas (
    id_merma VARCHAR(20) PRIMARY KEY,
    id_producto VARCHAR(20),
    cantidad INT NOT NULL,
    motivo VARCHAR(100) NOT NULL,
    fecha DATE DEFAULT GETDATE(),
    estatus VARCHAR(20) DEFAULT 'Activo',
    FOREIGN KEY (id_producto) REFERENCES Productos(id_producto)
);

CREATE TABLE MovimientosInventario (
    id_movimiento INT IDENTITY(1,1) PRIMARY KEY,
    id_producto VARCHAR(20),
    tipo VARCHAR(20) NOT NULL, -- 'Entrada' o 'Salida'
    cantidad INT NOT NULL,
    fecha DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (id_producto) REFERENCES Productos(id_producto)
);
GO

-- ==========================================
-- POBLACIÓN DE DATOS INICIALES (SIMULACIÓN)
-- ==========================================

INSERT INTO Categorias (id_categoria, nombre, descripcion, estatus) VALUES
('CAT-01', 'Abarrotes', 'Productos de despensa básica', 'Activo'),
('CAT-02', 'Limpieza', 'Detergentes y jabones', 'Activo');

INSERT INTO Productos (id_producto, nombre, id_categoria, precio, stock, estatus) VALUES
('PRD-001', 'Aceite Nutrioli 946 ml', 'CAT-01', 45.00, 24, 'Activo'),
('PRD-002', 'Frijol La Sierra Bayos 560g', 'CAT-01', 18.50, 3, 'Activo'),
('PRD-003', 'Detergente Foca 1Kg', 'CAT-02', 32.00, 0, 'Inactivo');

-- Contraseñas configuradas para pruebas rápidas
INSERT INTO Empleados (id_empleado, nombre, usuario, password, rol, estatus) VALUES
('EMP-01', 'Jorge Ivan Muñiz Samano', 'admin', '1234', 'Administrador', 'Activo'),
('EMP-02', 'Hazziel Enrique Ramirez', 'empleado', '1234', 'Empleado', 'Activo'),
('EMP-03', 'Luis Angel Jacobo Vite', 'empleado', '1234', 'Empleado', 'Inactivo');

INSERT INTO Ventas (ticket, fecha, id_empleado, total, estado) VALUES
('T-1001', '2026-05-29 10:00:00', 'EMP-02', 108.50, 'Completada'),
('T-1002', '2026-05-29 11:30:00', 'EMP-02', 32.00, 'Completada');

INSERT INTO DetalleVenta (ticket, id_producto, cantidad, subtotal) VALUES
('T-1001', 'PRD-001', 2, 90.00),
('T-1001', 'PRD-002', 1, 18.50),
('T-1002', 'PRD-003', 1, 32.00);

INSERT INTO Mermas (id_merma, id_producto, cantidad, motivo, fecha, estatus) VALUES
('MER-001', 'PRD-002', 2, 'Empaque roto', '2026-05-28', 'Activo'),
('MER-002', 'PRD-001', 5, 'Caducidad', '2026-05-25', 'Inactivo');
GO


select * from inventario;