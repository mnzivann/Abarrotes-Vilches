<?php
session_start();
require_once 'models/Venta.php';

$ventaModel = new Venta();
$mensaje = "";

// ============================================================================
// PROCESAMIENTO DE VENTA (POST)
// ============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    /**
     * [RF_08] VENTA_REGISTRAR
     */
    if ($accion === 'procesar_venta') {
        $total_venta = floatval($_POST['total_venta']);
        $lista_articulos = json_decode($_POST['lista_articulos'], true);
        $id_empleado = $_SESSION['id_empleado'] ?? 'EMP-01'; 

        try {
            $ticketGenerado = $ventaModel->procesarVenta($total_venta, $lista_articulos, $id_empleado);
            
            $mensaje = "<div class='alert alert-success' style='padding:15px; background:#dcfce7; color:#166534; border-radius:6px; margin-bottom:20px;'>
                          <b>¡Venta procesada con éxito!</b> El ticket <b>$ticketGenerado</b> ha sido registrado por $" . number_format($total_venta, 2) . ".
                    </div>";
        } catch (Exception $e) {
            $mensaje = "<div class='alert alert-danger' style='padding:15px; background:#fee2e2; color:#991b1b; border-radius:6px; margin-bottom:20px;'>
                         " . $e->getMessage() . "
                    </div>";
        }
    }
}

// ============================================================================
// PREPARACIÓN DE DATOS Y RENDERIZADO DE VISTA
// ============================================================================
$productosBD = $ventaModel->obtenerProductosActivos();

require_once 'views/nueva_venta_view.php';
?>