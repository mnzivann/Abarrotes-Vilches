<?php
session_start();
// Importamos el Modelo
require_once 'models/Categoria.php';

// Instanciamos el modelo
$categoriaModel = new Categoria();
$mensaje = "";

// ---------------------------------------------------------
// PROCESAMIENTO DE ACCIONES (POST)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'agregar') {
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        
        if (!empty($nombre)) {
            try {
                $categoriaModel->agregar($nombre, $descripcion);
                $mensaje = "<div class='alert alert-success'>Categoría '$nombre' registrada con estatus Activo en la BD.</div>";
            } catch(PDOException $e) {
                $mensaje = "<div class='alert alert-danger'>Error al registrar: " . $e->getMessage() . "</div>";
            }
        } else {
            $mensaje = "<div class='alert alert-danger'>Error de Validación [RF_19]: El nombre de la categoría es obligatorio.</div>";
        }
    }

    if ($accion === 'editar') {
        $id = $_POST['id_categoria'];
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        
        if (!empty($nombre) && !empty($id)) {
            try {
                $categoriaModel->actualizar($id, $nombre, $descripcion);
                $mensaje = "<div class='alert alert-success'>Categoría actualizada correctamente.</div>";
            } catch(PDOException $e) {
                $mensaje = "<div class='alert alert-danger'>Error al actualizar: " . $e->getMessage() . "</div>";
            }
        } else {
            $mensaje = "<div class='alert alert-danger'>Error de Validación [RF_21]: El nombre no puede estar vacío.</div>";
        }
    }

    if ($accion === 'cambiar_estatus') {
        $id = $_POST['id_categoria'];
        $nuevo_estatus = $_POST['nuevo_estatus'];
        
        try {
            $categoriaModel->cambiarEstatus($id, $nuevo_estatus);
            $mensaje = "<div class='alert alert-warning'>El estatus de la categoría ha cambiado a $nuevo_estatus.</div>";
        } catch(PDOException $e) {
            $mensaje = "<div class='alert alert-danger'>Error al cambiar estatus: " . $e->getMessage() . "</div>";
        }
    }
}

// ---------------------------------------------------------
// PREPARACIÓN DE DATOS Y RENDERIZADO DE VISTA
// ---------------------------------------------------------
// Le pedimos al modelo todas las categorías
$categoriasBD = $categoriaModel->obtenerTodas();

// Incluimos la vista (que usará las variables $mensaje y $categoriasBD)
require_once 'views/categorias_view.php';
?>