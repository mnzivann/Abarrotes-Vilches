<?php
require_once 'conexion.php';

class Merma {
    private $conn;

    public function __construct() {
        $this->conn = Conexion::conectar();
    }

    /**
     * [RF_16] MERMA_CONSULTAR
     */
    public function obtenerTodas() {
        $sql = "SELECT m.id_merma AS id, p.nombre AS producto, m.cantidad, m.motivo, m.fecha, m.estatus 
                FROM Mermas m 
                INNER JOIN Productos p ON m.id_producto = p.id_producto 
                ORDER BY m.fecha DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Consulta auxiliar para llenar el select del formulario
     */
    public function obtenerProductosActivos() {
        $stmt = $this->conn->query("SELECT id_producto, nombre, stock FROM Productos WHERE estatus = 'Activo'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * [RF_15] MERMA_REGISTRAR
     */
    public function registrar($id_producto, $cantidad, $motivo) {
        // 1. Verificamos el stock antes de hacer nada
        $stmtStock = $this->conn->prepare("SELECT stock FROM Productos WHERE id_producto = ?");
        $stmtStock->execute([$id_producto]);
        $prodInfo = $stmtStock->fetch(PDO::FETCH_ASSOC);
        
        if (!$prodInfo || $prodInfo['stock'] < $cantidad) {
            $stockActual = $prodInfo ? $prodInfo['stock'] : 0;
            throw new Exception("Error: No puedes mermar $cantidad unidades. Solo hay $stockActual en stock.");
        }

        try {
            $this->conn->beginTransaction(); 
            
            $id_merma = "MER-" . rand(1000, 9999);
            
            // Insertar Merma
            $sql_merma = "INSERT INTO Mermas (id_merma, id_producto, cantidad, motivo, estatus) VALUES (?, ?, ?, ?, 'Activo')";
            $stmt_merma = $this->conn->prepare($sql_merma);
            $stmt_merma->execute([$id_merma, $id_producto, $cantidad, $motivo]);
            
            // Restar del inventario
            $sql_update = "UPDATE Productos SET stock = stock - ? WHERE id_producto = ?";
            $stmt_update = $this->conn->prepare($sql_update);
            $stmt_update->execute([$cantidad, $id_producto]);

            // Guardar bitácora
            $sql_mov = "INSERT INTO MovimientosInventario (id_producto, tipo, cantidad) VALUES (?, 'Registro Merma', ?)";
            $stmt_mov = $this->conn->prepare($sql_mov);
            $stmt_mov->execute([$id_producto, $cantidad]);

            $this->conn->commit(); 
            return true;
        } catch(PDOException $e) {
            $this->conn->rollBack(); 
            throw clone $e;
        }
    }

    /**
     * [RF_17] MERMA_ELIMINAR Y RESTAURAR
     */
    public function cambiarEstatus($id_merma, $nuevo_estatus) {
        $stmtInfo = $this->conn->prepare("SELECT id_producto, cantidad, estatus FROM Mermas WHERE id_merma = ?");
        $stmtInfo->execute([$id_merma]);
        $mermaActual = $stmtInfo->fetch(PDO::FETCH_ASSOC);

        if (!$mermaActual || $mermaActual['estatus'] === $nuevo_estatus) {
            throw new Exception("La merma no existe o ya se encuentra en ese estatus.");
        }

        $id_producto = $mermaActual['id_producto'];
        $cantidad = $mermaActual['cantidad'];

        try {
            $this->conn->beginTransaction();

            $stmtUpdate = $this->conn->prepare("UPDATE Mermas SET estatus = ? WHERE id_merma = ?");
            $stmtUpdate->execute([$nuevo_estatus, $id_merma]);

            if ($nuevo_estatus === 'Inactivo') {
                $this->conn->exec("UPDATE Productos SET stock = stock + $cantidad WHERE id_producto = '$id_producto'");
                $this->conn->exec("INSERT INTO MovimientosInventario (id_producto, tipo, cantidad) VALUES ('$id_producto', 'Merma Anulada', $cantidad)");
            } else {
                $this->conn->exec("UPDATE Productos SET stock = stock - $cantidad WHERE id_producto = '$id_producto'");
                $this->conn->exec("INSERT INTO MovimientosInventario (id_producto, tipo, cantidad) VALUES ('$id_producto', 'Merma Restaurada', $cantidad)");
            }

            $this->conn->commit();
            return $cantidad; // Retornamos la cantidad para que el controlador pueda armar el mensaje
        } catch(PDOException $e) {
            $this->conn->rollBack();
            throw clone $e;
        }
    }
}
?>