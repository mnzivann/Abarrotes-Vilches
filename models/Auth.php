<?php
require_once 'conexion.php';

class Auth {
    private $conn;

    public function __construct() {
        $this->conn = Conexion::conectar();
    }

    /**
     * Busca un usuario en la BD limpiando espacios en blanco
     */
    public function buscarUsuario($usuario) {
        $sql = "SELECT id_empleado, nombre, rol, password, estatus FROM Empleados WHERE LTRIM(RTRIM(usuario)) = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$usuario]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>