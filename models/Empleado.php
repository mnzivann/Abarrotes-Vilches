<?php
require_once 'conexion.php';

class Empleado {
    private $conn;

    public function __construct() {
        $this->conn = Conexion::conectar();
    }

    /**
     * [RF_12] EMPLEADO_CONSULTAR
     */
    public function obtenerTodos($busqueda = '') {
        if (!empty($busqueda)) {
            $sql = "SELECT * FROM Empleados WHERE nombre LIKE ? OR usuario LIKE ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(["%$busqueda%", "%$busqueda%"]); 
        } else {
            $sql = "SELECT * FROM Empleados";
            $stmt = $this->conn->query($sql);
        }
        
        $empleadosBD = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Adaptamos el 'id_empleado' a 'id' para la vista
        foreach($empleadosBD as &$emp) {
            $emp['id'] = $emp['id_empleado'];
        }
        unset($emp);

        return $empleadosBD;
    }

    /**
     * [RF_11] EMPLEADO_AGREGAR
     */
    public function agregar($nombre, $usuario, $password, $rol) {
        $id_empleado = "EMP-" . rand(1000, 9999); 
        $sql = "INSERT INTO Empleados (id_empleado, nombre, usuario, password, rol, estatus) VALUES (?, ?, ?, ?, ?, 'Activo')";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id_empleado, $nombre, $usuario, $password, $rol]);
    }

    /**
     * [RF_13] EMPLEADO_ACTUALIZAR
     */
    public function actualizar($id, $nombre, $rol, $password) {
        if (!empty($password)) {
            $sql = "UPDATE Empleados SET nombre = ?, rol = ?, password = ? WHERE id_empleado = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$nombre, $rol, $password, $id]);
        } else {
            $sql = "UPDATE Empleados SET nombre = ?, rol = ? WHERE id_empleado = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$nombre, $rol, $id]);
        }
    }

    /**
     * [RF_14] EMPLEADO_ELIMINAR
     */
    public function cambiarEstatus($id, $nuevo_estatus) {
        $sql = "UPDATE Empleados SET estatus = ? WHERE id_empleado = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$nuevo_estatus, $id]);
    }
}
?>