<?php
// Parámetros de conexión a nuestro contenedor Docker
$host = "db"; 
$db_name = "master"; // Nos conectamos a 'master' para probar que el motor vive, sin importar si ya creaste tus tablas
$username = "SA";
$password = "AbarrotesVilches2026!";

try {
    // Intentamos la conexión PDO con TrustServerCertificate=1 para evitar bloqueos SSL locales
    $conn = new PDO("sqlsrv:Server=$host;Database=$db_name;TrustServerCertificate=1", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Si llegamos aquí, ¡todo funcionó!
    echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>";
    echo "<h1 style='color: #22c55e;'>¡Conexión Exitosa! 🚀</h1>";
    echo "<p style='font-size: 1.2rem; color: #334155;'>PHP se comunicó perfectamente con Microsoft SQL Server dentro de Docker.</p>";
    echo "<p style='color: #64748b;'>Los drivers (sqlsrv/pdo_sqlsrv) están instalados correctamente.</p>";
    echo "</div>";

} catch(PDOException $e) {
    // Si algo falla, atrapamos el error y lo mostramos
    echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>";
    echo "<h1 style='color: #ef4444;'>Error de Conexión ❌</h1>";
    echo "<p style='background: #fee2e2; padding: 15px; border-radius: 8px; display: inline-block;'><b>Detalle:</b> " . $e->getMessage() . "</p>";
    echo "</div>";
}
?>