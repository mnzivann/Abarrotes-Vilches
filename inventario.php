<?php
session_start();
require_once 'models/Inventario.php';

$inventarioModel = new Inventario();
$mensaje = "";

// ============================================================================
// PROCESAMIENTO DE MOVIMIENTOS (POST)
// ============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id_producto = $_POST['id_producto'];
    $cantidad = intval($_POST['cantidad']);

    // Consultamos el stock actual usando el Modelo
    $stock_actual = $inventarioModel->obtenerStock($id_producto);

    /**
     * [RF_05] INVENTARIO_ENTRADA
     */
    if ($accion === 'entrada') {
        if ($cantidad > 0) {
            try {
                $inventarioModel->registrarEntrada($id_producto, $cantidad);
                $mensaje = "<div class='alert alert-success'>Entrada registrada exitosamente: Se agregaron $cantidad unidades al inventario.</div>";
            } catch(PDOException $e) {
                $mensaje = "<div class='alert alert-danger'>Error al registrar entrada: " . $e->getMessage() . "</div>";
            }
        } else {
            $mensaje = "<div class='alert alert-danger'>Error de Validación [RF_05]: La cantidad de entrada debe ser mayor a 0.</div>";
        }
    }

    /**
     * [RF_06] INVENTARIO_SALIDA
     */
    if ($accion === 'salida') {
        if ($cantidad > 0 && $stock_actual >= $cantidad) {
            try {
                $inventarioModel->registrarSalida($id_producto, $cantidad);
                $mensaje = "<div class='alert alert-warning'>Salida registrada exitosamente: Se retiraron $cantidad unidades.</div>";
            } catch(PDOException $e) {
                $mensaje = "<div class='alert alert-danger'>Error al registrar salida: " . $e->getMessage() . "</div>";
            }
        } 
        else if ($cantidad > $stock_actual) {
            $mensaje = "<div class='alert alert-danger'>Error de Validación [RF_06]: No hay suficiente stock para la salida. (Stock actual disponible: $stock_actual).</div>";
        } 
        else {
            $mensaje = "<div class='alert alert-danger'>Error: La cantidad de salida debe ser mayor a 0.</div>";
        }
    }
}

// ============================================================================
// PREPARACIÓN DE DATOS Y RENDERIZADO DE VISTA
// ============================================================================
$productosBD = $inventarioModel->obtenerTodos();

require_once 'views/inventario_view.php';
?>