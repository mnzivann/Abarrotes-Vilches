<?php
require_once 'conexion.php';

class Reporte {
    private $conn;

    public function __construct() {
        $this->conn = Conexion::conectar();
    }

    /**
     * [RNF_04] RESPALDO DE BASE DE DATOS
     * Ejecuta el comando nativo de Microsoft SQL Server para backups
     */
    public function generarRespaldo() {
        $ruta_respaldo = '/var/opt/mssql/data/AbarrotesVilches_Respaldo_' . date('Ymd_His') . '.bak';
        $sqlBackup = "BACKUP DATABASE AbarrotesVilchesDB TO DISK = '$ruta_respaldo' WITH FORMAT, INIT";
        
        $this->conn->exec($sqlBackup);
        
        return $ruta_respaldo; // Retornamos la ruta para que la vista la muestre
    }

    /**
     * [RF_18] REPORTE_GENERAR
     * Consulta con JOINS y STRING_AGG para consolidar los tickets
     */
    public function obtenerReporteVentas() {
        $sqlReporte = "
            SELECT 
                v.ticket, 
                v.fecha, 
                v.total,
                STRING_AGG(p.nombre + ' (' + CAST(dv.cantidad AS VARCHAR) + ')', ', ') AS productos
            FROM Ventas v
            INNER JOIN DetalleVenta dv ON v.ticket = dv.ticket
            INNER JOIN Productos p ON dv.id_producto = p.id_producto
            WHERE v.estado = 'Completada'
            GROUP BY v.ticket, v.fecha, v.total
            ORDER BY v.fecha DESC
        ";
        
        $stmt = $this->conn->query($sqlReporte);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>