<?php
session_start();
// ============================================================================
// MÓDULO DE CATEGORÍAS - LÓGICA DE BACKEND
// ============================================================================

/**
 * [RF_20] CATEGORÍA_CONSULTAR
 * Descripción: Consultar categorías registradas.
 * Validación: Mostrar datos correctamente (registros existentes).
 */
$categoriasBD = [
    ["id" => "CAT-01", "nombre" => "Abarrotes", "descripcion" => "Productos de despensa básica", "estatus" => "Activo"],
    ["id" => "CAT-02", "nombre" => "Limpieza", "descripcion" => "Detergentes y jabones", "estatus" => "Activo"]
];

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    /**
     * [RF_19] CATEGORÍA_AGREGAR
     * Descripción: Registrar una nueva categoría.
     * Validación: Campos obligatorios no vacíos (nombre único y estatus activo).
     */
    if ($accion === 'agregar') {
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);

        // Validación estricta del Excel: Campo 'nombre' no vacío. 
        // (Nota: Validación de "nombre único" pendiente de integración con DB real)
        if (!empty($nombre)) {
            // TODO: Integrar INSERT INTO Categorias (nombre, descripcion, estatus) VALUES (...)
            $mensaje = "<div class='alert alert-success'>Categoría '$nombre' registrada con estatus Activo.</div>";
        } else {
            $mensaje = "<div class='alert alert-danger'>Error de Validación [RF_19]: El nombre de la categoría es obligatorio.</div>";
        }
    }

    /**
     * [RF_21] CATEGORÍA_ACTUALIZAR
     * Descripción: Modificar datos de una categoría.
     * Validación: Validar cambios (nombre no vacío).
     */
    if ($accion === 'editar') {
        // En una implementación real llegaría el ID de la categoría a actualizar
        $nombre = trim($_POST['nombre']);
        
        // Validación estricta del Excel
        if (!empty($nombre)) {
            // TODO: Integrar UPDATE Categorias SET nombre = ?, descripcion = ? WHERE id = ?
            $mensaje = "<div class='alert alert-success'>Categoría actualizada correctamente.</div>";
        } else {
            $mensaje = "<div class='alert alert-danger'>Error de Validación [RF_21]: El nombre no puede estar vacío.</div>";
        }
    }

    /**
     * [RF_22] CATEGORÍA_ELIMINAR
     * Descripción: Cambiar estatus de una categoría en lugar de eliminarla (Baja Lógica).
     * Validación: Confirmación antes de cambiar estatus (Manejada en el Frontend).
     */
    if ($accion === 'cambiar_estatus') {
        $nuevo_estatus = $_POST['nuevo_estatus'];
        // TODO: Integrar UPDATE Categorias SET estatus = ? WHERE id = ?
        $mensaje = "<div class='alert alert-warning'>El estatus de la categoría ha cambiado a $nuevo_estatus.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías - Abarrotes Vilches</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; }
        :root { --primary-color: #2563eb; --sidebar-bg: #1e293b; --bg-color: #f1f5f9; --white: #ffffff; --danger: #ef4444; --success: #22c55e; --warning: #eab308;}
        body { display: flex; height: 100vh; background-color: var(--bg-color); }
        .sidebar { width: 260px; background-color: var(--sidebar-bg); color: var(--white); display: flex; flex-direction: column; }
        .brand { padding: 24px; text-align: center; border-bottom: 1px solid #334155; }
        .menu { list-style: none; padding: 20px 0; flex: 1; }
        .menu li a { display: block; padding: 15px 24px; color: #cbd5e1; text-decoration: none; }
        .menu li.active a { background-color: #334155; border-left: 4px solid var(--primary-color); color: white;}
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .topbar { background-color: var(--white); padding: 20px 40px; display: flex; justify-content: space-between; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .page-content { padding: 40px; overflow-y: auto; flex: 1; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; color: white; }
        .btn-primary { background-color: var(--primary-color); }
        .btn-warning { background-color: var(--warning); color: black; }
        .btn-danger { background-color: var(--danger); }
        .btn-success { background-color: var(--success); }
        .card { background-color: var(--white); border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); padding: 20px; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 6px; }
        .alert-success { background-color: #dcfce7; color: #166534; }
        .alert-danger { background-color: #fee2e2; color: #991b1b; }
        .alert-warning { background-color: #fef08a; color: #854d0e; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 100; }
        .modal-content { background: white; padding: 30px; border-radius: 8px; width: 400px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="brand"><h2>Abarrotes Vilches</h2></div>
        <ul class="menu">
            <li><a href="index.php">Productos</a></li>
            <li><a href="categorias.php">Categorías</a></li>
            <li><a href="inventario.php">Inventario</a></li>
            <li><a href="ventas.php">Ventas</a></li>
            <li><a href="mermas.php">Mermas</a></li>
            
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
                <li><a href="empleados.php">Empleados</a></li>
                <li><a href="reportes.php">Reportes</a></li>
            <?php endif; ?>
        </ul>
    </aside>

    <main class="main-content">
        <header class="topbar"><div>Catálogos Adicionales</div><div>Fecha: <?php echo date('d/m/Y'); ?></div></header>
        <div class="page-content">
            <!-- Renderizado de validaciones del servidor -->
            <?php echo $mensaje; ?>
            
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h1>Gestión de Categorías</h1>
                <!-- Disparador UI para RF_19 -->
                <button class="btn btn-primary" onclick="abrirModal('modalAgregar')">+ Nueva Categoría</button>
            </div>

            <div class="card">
                <table>
                    <thead><tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Estatus</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <!-- Iteración para cumplir con RF_20 -->
                        <?php foreach($categoriasBD as $cat): ?>
                            <tr>
                                <td><?php echo $cat['id']; ?></td>
                                <td><?php echo $cat['nombre']; ?></td>
                                <td><?php echo $cat['descripcion']; ?></td>
                                <td style="font-weight:bold; color: <?php echo $cat['estatus'] == 'Activo' ? 'green' : 'red'; ?>"><?php echo $cat['estatus']; ?></td>
                                <td style="display: flex; gap: 5px;">
                                    <!-- Disparador UI para RF_21 -->
                                    <button class="btn btn-warning" style="padding: 5px 10px;" onclick="abrirModalEditar('<?php echo $cat['id']; ?>', '<?php echo $cat['nombre']; ?>', '<?php echo $cat['descripcion']; ?>')">Editar</button>
                                    
                                    <!-- Formulario para cumplir con RF_22 (Baja Lógica) -->
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="accion" value="cambiar_estatus">
                                        <?php if ($cat['estatus'] == 'Activo'): ?>
                                            <input type="hidden" name="nuevo_estatus" value="Inactivo">
                                            <!-- Validación estricta UI: Confirmación antes de cambiar estatus -->
                                            <button type="submit" class="btn btn-danger" style="padding: 5px 10px;" onclick="return confirm('¿Desactivar esta categoría?');">Desactivar</button>
                                        <?php else: ?>
                                            <input type="hidden" name="nuevo_estatus" value="Activo">
                                            <!-- Validación estricta UI: Confirmación antes de cambiar estatus -->
                                            <button type="submit" class="btn btn-success" style="padding: 5px 10px;" onclick="return confirm('¿Reactivar categoría?');">Activar</button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Formulario para RF_19: Registrar Categoría -->
    <div id="modalAgregar" class="modal">
        <div class="modal-content">
            <h3>Registrar Categoría</h3>
            <form method="POST" action="categorias.php">
                <input type="hidden" name="accion" value="agregar">
                
                <!-- Validación UI (HTML5): Campo obligatorio (required) -->
                <label>Nombre *</label><input type="text" name="nombre" class="form-control" required>
                
                <label>Descripción</label><textarea name="descripcion" class="form-control"></textarea>
                
                <div style="display:flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn btn-danger" onclick="document.getElementById('modalAgregar').style.display='none'">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Formulario para RF_21: Actualizar Categoría -->
    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <h3>Actualizar Categoría</h3>
            <form method="POST" action="categorias.php">
                <input type="hidden" name="accion" value="editar">
                
                <!-- Validación UI (HTML5): Campo obligatorio (required) -->
                <label>Nombre *</label><input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                
                <label>Descripción</label><textarea name="descripcion" id="edit_desc" class="form-control"></textarea>
                
                <div style="display:flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn btn-danger" onclick="document.getElementById('modalEditar').style.display='none'">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModal(id) { document.getElementById(id).style.display = 'flex'; }
        function abrirModalEditar(id, nombre, desc) {
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_desc').value = desc;
            abrirModal('modalEditar');
        }
    </script>
</body>
</html>