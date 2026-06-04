<?php
require_once 'conexion.php';

class Categoria {
    private $conn;

    public function __construct() {
        // Al instanciar la clase, abrimos la conexión automáticamente
        $this->conn = Conexion::conectar();
    }

    /**
     * [RF_20] CATEGORÍA_CONSULTAR
     */
    public function obtenerTodas() {
        $sql = "SELECT id_categoria AS id, nombre, descripcion, estatus FROM Categorias";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * [RF_19] CATEGORÍA_AGREGAR
     */
    public function agregar($nombre, $descripcion) {
        $id_categoria = "CAT-" . rand(100, 999);
        $sql = "INSERT INTO Categorias (id_categoria, nombre, descripcion, estatus) VALUES (?, ?, ?, 'Activo')";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id_categoria, $nombre, $descripcion]);
    }

    /**
     * [RF_21] CATEGORÍA_ACTUALIZAR
     */
    public function actualizar($id, $nombre, $descripcion) {
        $sql = "UPDATE Categorias SET nombre = ?, descripcion = ? WHERE id_categoria = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$nombre, $descripcion, $id]);
    }

    /**
     * [RF_22] CATEGORÍA_ELIMINAR
     */
    public function cambiarEstatus($id, $nuevo_estatus) {
        $sql = "UPDATE Categorias SET estatus = ? WHERE id_categoria = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$nuevo_estatus, $id]);
    }
}
?>