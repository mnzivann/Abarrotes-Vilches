<?php
require_once 'conexion.php';

class HistorialVentas {
    private $conn;

    public function __construct() {
        $this->conn = Conexion::conectar();
    }

    /**
     * [RF_09] VENTA_CONSULTAR (SELECT PRINCIPAL)
     */
    public function obtenerVentas() {
        $sql = "SELECT v.ticket, v.fecha, e.nombre AS cajero, v.total, v.estado 
                FROM Ventas v 
                LEFT JOIN Empleados e ON v.id_empleado = e.id_empleado 
                ORDER BY v.fecha DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * CONSULTA DE DETALLES PARA EL MODAL DE JAVASCRIPT
     */
    public function obtenerDetalles() {
        $sql = "SELECT dv.ticket, p.nombre, dv.cantidad, dv.subtotal 
                FROM DetalleVenta dv 
                INNER JOIN Productos p ON dv.id_producto = p.id_producto";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * [RF_10] VENTA_CANCELAR
     */
    public function cancelarVenta($ticket) {
        try {
            $this->conn->beginTransaction(); 

            // 1. Verificar que la venta exista y no esté cancelada
            $stmtCheck = $this->conn->prepare("SELECT estado FROM Ventas WHERE ticket = ?");
            $stmtCheck->execute([$ticket]);
            $ventaActual = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$ventaActual || $ventaActual['estado'] === 'Cancelada') {
                throw new Exception("La venta ya estaba cancelada o no existe.");
            }

            // 2. Cambiar estado a Cancelada
            $stmtUpdateVenta = $this->conn->prepare("UPDATE Ventas SET estado = 'Cancelada' WHERE ticket = ?");
            $stmtUpdateVenta->execute([$ticket]);

            // 3. Obtener detalles para devolver stock
            $stmtDetalle = $this->conn->prepare("SELECT id_producto, cantidad FROM DetalleVenta WHERE ticket = ?");
            $stmtDetalle->execute([$ticket]);
            $articulos = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

            // 4. Devolver stock e insertar movimiento
            $stmtUpdateStock = $this->conn->prepare("UPDATE Productos SET stock = stock + ? WHERE id_producto = ?");
            $stmtMovimiento = $this->conn->prepare("INSERT INTO MovimientosInventario (id_producto, tipo, cantidad) VALUES (?, 'Cancelacion', ?)");

            foreach ($articulos as $art) {
                $stmtUpdateStock->execute([$art['cantidad'], $art['id_producto']]);
                $stmtMovimiento->execute([$art['id_producto'], $art['cantidad']]);
            }

            $this->conn->commit(); 
            return true;
        } catch(PDOException $e) {
            $this->conn->rollBack();
            throw new Exception("Error de base de datos: " . $e->getMessage());
        } catch(Exception $e) {
            $this->conn->rollBack();
            throw clone $e; 
        }
    }
}
?>