<?php
class Conexion {
    public static function conectar() {
        // Parámetros de tu contenedor Docker
        $host = "db"; 
        $db_name = "AbarrotesVilchesDB";
        $username = "SA";
        $password = "AbarrotesVilches2026!";

        try {
            $conn = new PDO("sqlsrv:Server=$host;Database=$db_name;TrustServerCertificate=1", $username, $password);
            // Configurar PDO para que lance excepciones (errores) si algo falla en SQL
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch(PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }
}
?>