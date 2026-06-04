<?php
require_once 'conexion.php';

class Venta {
    private $conn;

    public function __construct() {
        $this->conn = Conexion::conectar();
    }

    /**
     * Consulta auxiliar para cargar el autocompletado en JavaScript
     */
    public function obtenerProductosActivos() {
        $sql = "SELECT id_producto AS id, nombre, precio, stock FROM Productos WHERE estatus = 'Activo' AND stock > 0";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * [RF_08] VENTA_REGISTRAR (Transacción Central)
     */
    public function procesarVenta($total_venta, $lista_articulos, $id_empleado) {
        if ($total_venta <= 0 || empty($lista_articulos)) {
            throw new Exception("Error de Validación: No se puede procesar un ticket vacío o en ceros.");
        }

        try {
            $this->conn->beginTransaction(); 

            // 1. Generar ticket único
            $ticket = "T-" . time(); 

            // 2. Insertar encabezado de Venta
            $sqlVenta = "INSERT INTO Ventas (ticket, id_empleado, total, estado) VALUES (?, ?, ?, 'Completada')";
            $stmtVenta = $this->conn->prepare($sqlVenta);
            $stmtVenta->execute([$ticket, $id_empleado, $total_venta]);

            // Preparamos las consultas repetitivas para el ciclo
            $sqlDetalle = "INSERT INTO DetalleVenta (ticket, id_producto, cantidad, subtotal) VALUES (?, ?, ?, ?)";
            $stmtDetalle = $this->conn->prepare($sqlDetalle);

            $sqlUpdateStock = "UPDATE Productos SET stock = stock - ? WHERE id_producto = ?";
            $stmtUpdateStock = $this->conn->prepare($sqlUpdateStock);

            $sqlMovimiento = "INSERT INTO MovimientosInventario (id_producto, tipo, cantidad) VALUES (?, 'Salida por Venta', ?)";
            $stmtMovimiento = $this->conn->prepare($sqlMovimiento);

            // 3. Iterar cada artículo del carrito (Detalle)
            foreach ($lista_articulos as $articulo) {
                // Verificar stock dinámicamente por si hubo ventas simultáneas
                $stmtCheck = $this->conn->prepare("SELECT stock FROM Productos WHERE id_producto = ?");
                $stmtCheck->execute([$articulo['id']]);
                $stockActual = $stmtCheck->fetchColumn();

                if ($stockActual < $articulo['cantidad']) {
                    throw new Exception("Stock insuficiente para el producto: " . $articulo['nombre']);
                }

                $stmtDetalle->execute([$ticket, $articulo['id'], $articulo['cantidad'], $articulo['subtotal']]);
                $stmtUpdateStock->execute([$articulo['cantidad'], $articulo['id']]);
                $stmtMovimiento->execute([$articulo['id'], $articulo['cantidad']]);
            }

            $this->conn->commit(); 
            return $ticket; // Retornamos el número de ticket generado
            
        } catch(PDOException $e) {
            $this->conn->rollBack(); 
            throw new Exception("Error de base de datos: " . $e->getMessage());
        } catch(Exception $e) {
            $this->conn->rollBack();
            throw $e; // Lanzamos excepciones de validación (ej. falta de stock)
        }
    }
}
?>