<?php
session_start();
require_once 'models/HistorialVentas.php';

$historialModel = new HistorialVentas();
$mensaje = "";

// ============================================================================
// PROCESAMIENTO DE FORMULARIOS (POST)
// ============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion === 'cancelar_venta') {
        $ticket = $_POST['ticket'];
        
        try {
            $historialModel->cancelarVenta($ticket);
            $mensaje = "<div style='padding: 15px; margin-bottom: 20px; border-radius: 6px; background-color: #fef08a; color: #854d0e; border: 1px solid #fde047;'>
                            ✅ La venta con ticket <b>$ticket</b> ha sido cancelada y los productos regresaron al inventario.
                        </div>";
        } catch (Exception $e) {
            $mensaje = "<div style='padding: 15px; margin-bottom: 20px; border-radius: 6px; background-color: #fee2e2; color: #991b1b;'>" . $e->getMessage() . "</div>";
        }
    }
}

// ============================================================================
// PREPARACIÓN DE DATOS Y RENDERIZADO DE VISTA
// ============================================================================
$ventasEnSQLServer = $historialModel->obtenerVentas();
$todosLosDetalles = $historialModel->obtenerDetalles();

// Formateo de datos estéticos para la vista
foreach ($ventasEnSQLServer as &$v) {
    $v['clase'] = ($v['estado'] === 'Completada') ? 'badge-success' : 'badge-danger';
    $v['permiso_cancelar'] = ($v['estado'] === 'Completada');
    
    $nombres = explode(' ', $v['cajero']);
    $v['cajero'] = $nombres[0] ?? 'Cajero'; // Mostramos solo el primer nombre
    $v['fecha'] = date('d/m/Y H:i', strtotime($v['fecha']));
}
unset($v);

// Cargamos el HTML puro
require_once 'views/ventas_view.php';
?>