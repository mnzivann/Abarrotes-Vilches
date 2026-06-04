<?php
session_start();
// Importamos el Modelo
require_once 'models/Producto.php';

$productoModel = new Producto();
$mensaje = "";

// ============================================================================
// PROCESAMIENTO DE FORMULARIOS (POST)
// ============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    /**
     * [RF_01] PRODUCTO_AGREGAR
     */
    if ($accion === 'agregar') {
        $nombre = trim($_POST['nombre']);
        $id_categoria = $_POST['categoria']; 
        $precio = floatval($_POST['precio']);
        $stock = intval($_POST['stock']);

        if ($precio > 0 && $stock >= 0 && !empty($nombre) && !empty($id_categoria)) {
            try {
                $productoModel->agregar($nombre, $id_categoria, $precio, $stock);
                $mensaje = "<div class='alert alert-success'>Producto '$nombre' agregado correctamente a la BD.</div>";
            } catch(PDOException $e) {
                $mensaje = "<div class='alert alert-danger'>Error al guardar en BD: " . $e->getMessage() . "</div>";
            }
        } else {
            $mensaje = "<div class='alert alert-danger'>Error de Validación: Verifica que el precio sea mayor a 0, el stock no sea negativo y no haya campos vacíos.</div>";
        }
    }

    /**
     * [RF_03] PRODUCTO_MODIFICAR
     */
    if ($accion === 'editar') {
        $id = $_POST['id_producto'];
        $id_categoria = $_POST['categoria']; 
        $precio = floatval($_POST['precio']);
        $stock = intval($_POST['stock']);
        
        if ($precio > 0 && $stock >= 0 && !empty($id_categoria)) {
            try {
                $productoModel->actualizar($id, $id_categoria, $precio, $stock);
                $mensaje = "<div class='alert alert-success'>Producto actualizado correctamente en BD.</div>";
            } catch(PDOException $e) {
                $mensaje = "<div class='alert alert-danger'>Error al actualizar: " . $e->getMessage() . "</div>";
            }
        } else {
            $mensaje = "<div class='alert alert-danger'>Error: El precio y stock deben ser válidos y debe seleccionar una categoría.</div>";
        }
    }

    /**
     * [RF_04] PRODUCTO_ELIMINAR (Baja Lógica)
     */
    if ($accion === 'cambiar_estatus') {
        $id = $_POST['id_producto'];
        $nuevo_estatus = $_POST['nuevo_estatus'];
        
        try {
            $productoModel->cambiarEstatus($id, $nuevo_estatus);
            $mensaje = "<div class='alert alert-warning'>El estatus del producto ha cambiado a $nuevo_estatus.</div>";
        } catch(PDOException $e) {
            $mensaje = "<div class='alert alert-danger'>Error al cambiar estatus: " . $e->getMessage() . "</div>";
        }
    }
}

// ============================================================================
// PREPARACIÓN DE DATOS Y RENDERIZADO DE VISTA
// ============================================================================
$busqueda = $_GET['buscar'] ?? '';
$productosBD = $productoModel->obtenerTodos($busqueda);
$categoriasDisponibles = $productoModel->obtenerCategoriasActivas();

// Cargamos el HTML puro
require_once 'views/productos_view.php';
?>