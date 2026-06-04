<?php
session_start();
// Importamos el Modelo
require_once 'models/Empleado.php';

$empleadoModel = new Empleado();
$mensaje = "";

// ============================================================================
// PROCESAMIENTO DE FORMULARIOS (POST)
// ============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    /**
     * [RF_11] EMPLEADO_AGREGAR
     */
    if ($accion === 'agregar') {
        $nombre = $_POST['nombre'];
        $usuario = $_POST['usuario'];
        $password = $_POST['password'];
        $rol = $_POST['rol'];

        if (!empty($nombre) && !empty($password)) {
            try {
                $empleadoModel->agregar($nombre, $usuario, $password, $rol);
                $mensaje = "<div class='alert alert-success'>Empleado '$nombre' registrado con éxito.</div>";
            } catch(PDOException $e) {
                // El error 2627 es "Violation of UNIQUE KEY", es decir, el usuario ya existe
                $mensaje = "<div class='alert alert-danger'>Error de Validación [RF_11]: El nombre de usuario '$usuario' ya está en uso.</div>";
            }
        } else {
            $mensaje = "<div class='alert alert-danger'>Error: Faltan datos obligatorios.</div>";
        }
    }

    /**
     * [RF_13] EMPLEADO_ACTUALIZAR
     */
    if ($accion === 'editar') {
        $id = $_POST['id_empleado'];
        $nombre = $_POST['nombre'];
        $rol = $_POST['rol'];
        $password = $_POST['password'];
        
        try {
            $empleadoModel->actualizar($id, $nombre, $rol, $password);
            $mensaje = "<div class='alert alert-success'>Datos del empleado actualizados correctamente.</div>";
        } catch(PDOException $e) {
            $mensaje = "<div class='alert alert-danger'>Error al actualizar: " . $e->getMessage() . "</div>";
        }
    }

    /**
     * [RF_14] EMPLEADO_ELIMINAR (Baja Lógica)
     */
    if ($accion === 'cambiar_estatus') {
        $id = $_POST['id_empleado'];
        $nuevo_estatus = $_POST['nuevo_estatus'];
        
        try {
            $empleadoModel->cambiarEstatus($id, $nuevo_estatus);
            $mensaje = "<div class='alert alert-warning'>El estatus del empleado ha cambiado a $nuevo_estatus.</div>";
        } catch(PDOException $e) {
            $mensaje = "<div class='alert alert-danger'>Error al cambiar estatus: " . $e->getMessage() . "</div>";
        }
    }
}

// ============================================================================
// PREPARACIÓN DE DATOS Y RENDERIZADO DE VISTA
// ============================================================================
$busqueda = $_GET['buscar'] ?? '';
$empleadosBD = $empleadoModel->obtenerTodos($busqueda);

// Cargamos el HTML puro
require_once 'views/empleados_view.php';
?>