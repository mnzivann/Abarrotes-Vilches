<?php
session_start();
require_once 'models/Reporte.php';

// Bloqueo de seguridad: Solo el administrador puede entrar a este módulo
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
    header("Location: index.php");
    exit();
}

$reporteModel = new Reporte();
$mensaje = "";

// ============================================================================
// PROCESAMIENTO DE RESPALDOS (POST)
// ============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'respaldo') {
    try {
        $ruta = $reporteModel->generarRespaldo();
        $mensaje = "<div class='alert alert-success'>✅ <b>Respaldo generado con éxito.</b><br>El archivo se guardó de forma segura en el servidor de base de datos como: <code>$ruta</code></div>";
    } catch(PDOException $e) {
        $mensaje = "<div class='alert alert-danger'>❌ <b>Error al generar el respaldo:</b> " . $e->getMessage() . "</div>";
    }
}

// ============================================================================
// PREPARACIÓN DE DATOS PARA EL REPORTE
// ============================================================================
try {
    $ventasBD = $reporteModel->obtenerReporteVentas();
} catch(PDOException $e) {
    $ventasBD = [];
    $mensaje = "<div class='alert alert-danger'>Error al cargar el reporte: " . $e->getMessage() . "</div>";
}

// Cálculo matemático de los ingresos totales y formateo de fechas
$ingresosTotales = 0;
foreach($ventasBD as &$v) { 
    $ingresosTotales += $v['total']; 
    $v['fecha'] = date('d/m/Y H:i', strtotime($v['fecha']));
}
unset($v);

// Cargamos la interfaz
require_once 'views/reportes_view.php';
?>

