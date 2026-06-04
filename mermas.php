<?php
session_start();
require_once 'models/Merma.php';

$mermaModel = new Merma();
$mensaje = "";

// ============================================================================
// PROCESAMIENTO DE FORMULARIOS (POST)
// ============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    /**
     * [RF_15] MERMA_REGISTRAR
     */
    if ($accion === 'registrar') {
        $id_producto = $_POST['producto']; 
        $cantidad = intval($_POST['cantidad']);
        $motivo = trim($_POST['motivo']);

        if ($cantidad > 0 && !empty($id_producto) && !empty($motivo)) {
            try {
                $mermaModel->registrar($id_producto, $cantidad, $motivo);
                $mensaje = "<div class='alert alert-success'> Merma registrada. Se descontaron $cantidad unidades del inventario.</div>";
            } catch (Exception $e) {
                $mensaje = "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
            }
        } else {
            $mensaje = "<div class='alert alert-danger'>Error de Validación: La cantidad debe ser mayor a 0 y todos los campos son obligatorios.</div>";
        }
    }

    /**
     * [RF_17] MERMA_ELIMINAR Y RESTAURAR
     */
    if ($accion === 'cambiar_estatus') {
        $id_merma = $_POST['id_merma'];
        $nuevo_estatus = $_POST['nuevo_estatus'];
        
        try {
            $cantidad_afectada = $mermaModel->cambiarEstatus($id_merma, $nuevo_estatus);
            
            if ($nuevo_estatus === 'Inactivo') {
                $mensaje = "<div class='alert alert-warning'> Merma Anulada. Se devolvieron $cantidad_afectada unidades al inventario.</div>";
            } else {
                $mensaje = "<div class='alert alert-danger'> Merma Restaurada. Se volvieron a descontar $cantidad_afectada unidades del inventario.</div>";
            }
        } catch (Exception $e) {
            $mensaje = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
    }
}

// ============================================================================
// PREPARACIÓN DE DATOS Y RENDERIZADO DE VISTA
// ============================================================================
$mermasBD = $mermaModel->obtenerTodas();
$productosDisponibles = $mermaModel->obtenerProductosActivos();

require_once 'views/mermas_view.php';
?>