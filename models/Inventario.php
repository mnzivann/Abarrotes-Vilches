<?php
require_once 'conexion.php';

class Inventario {
    private $conn;

    public function __construct() {
        $this->conn = Conexion::conectar();
    }

    /**
     * Obtener el stock actual de un producto para validaciones
     */
    public function obtenerStock($id_producto) {
        $stmt = $this->conn->prepare("SELECT stock FROM Productos WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? intval($row['stock']) : 0;
    }

    /**
     * [RF_05] INVENTARIO_ENTRADA
     */
    public function registrarEntrada($id_producto, $cantidad) {
        try {
            $this->conn->beginTransaction(); 
            
            $sql_update = "UPDATE Productos SET stock = stock + ? WHERE id_producto = ?";
            $stmt_update = $this->conn->prepare($sql_update);
            $stmt_update->execute([$cantidad, $id_producto]);
            
            $sql_mov = "INSERT INTO MovimientosInventario (id_producto, tipo, cantidad) VALUES (?, 'Entrada', ?)";
            $stmt_mov = $this->conn->prepare($sql_mov);
            $stmt_mov->execute([$id_producto, $cantidad]);
            
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    /**
     * [RF_06] INVENTARIO_SALIDA
     */
    public function registrarSalida($id_producto, $cantidad) {
        try {
            $this->conn->beginTransaction();
            
            $sql_update = "UPDATE Productos SET stock = stock - ? WHERE id_producto = ?";
            $stmt_update = $this->conn->prepare($sql_update);
            $stmt_update->execute([$cantidad, $id_producto]);
            
            $sql_mov = "INSERT INTO MovimientosInventario (id_producto, tipo, cantidad) VALUES (?, 'Salida', ?)";
            $stmt_mov = $this->conn->prepare($sql_mov);
            $stmt_mov->execute([$id_producto, $cantidad]);
            
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    /**
     * [RF_07] INVENTARIO_CONSULTAR
     */
    public function obtenerTodos() {
        $sql = "SELECT id_producto AS id, nombre, stock, estatus FROM Productos";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>