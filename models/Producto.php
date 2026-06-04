<?php
require_once 'conexion.php';

class Producto {
    private $conn;

    public function __construct() {
        $this->conn = Conexion::conectar();
    }

    /**
     * [RF_02] PRODUCTO_CONSULTAR (BÚSQUEDA BAJO DEMANDA)
     */
    public function obtenerTodos($busqueda = '') {
        // Solo ejecutamos el SELECT si hay una búsqueda, tal como lo tenías
        if (!empty($busqueda)) {
            $sql = "SELECT p.id_producto AS id, p.id_categoria, p.nombre, c.nombre AS categoria, p.precio, p.stock, p.estatus 
                    FROM Productos p 
                    LEFT JOIN Categorias c ON p.id_categoria = c.id_categoria 
                    WHERE p.nombre LIKE ? OR c.nombre LIKE ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(["%$busqueda%", "%$busqueda%"]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return []; 
    }

    /**
     * Consulta auxiliar para los <select> de los modales
     */
    public function obtenerCategoriasActivas() {
        $stmt = $this->conn->query("SELECT id_categoria, nombre FROM Categorias WHERE estatus = 'Activo'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * [RF_01] PRODUCTO_AGREGAR
     */
    public function agregar($nombre, $id_categoria, $precio, $stock) {
        $id_producto = "PRD-" . rand(1000, 9999);
        $sql = "INSERT INTO Productos (id_producto, nombre, id_categoria, precio, stock, estatus) VALUES (?, ?, ?, ?, ?, 'Activo')";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id_producto, $nombre, $id_categoria, $precio, $stock]);
    }

    /**
     * [RF_03] PRODUCTO_MODIFICAR
     */
    public function actualizar($id, $id_categoria, $precio, $stock) {
        $sql = "UPDATE Productos SET id_categoria = ?, precio = ?, stock = ? WHERE id_producto = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id_categoria, $precio, $stock, $id]);
    }

    /**
     * [RF_04] PRODUCTO_ELIMINAR
     */
    public function cambiarEstatus($id, $nuevo_estatus) {
        $sql = "UPDATE Productos SET estatus = ? WHERE id_producto = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$nuevo_estatus, $id]);
    }
}
?>