<?php
session_start();
// ============================================================================
// LÓGICA DE EMPLEADOS (SIMULACIÓN DE BASE DE DATOS)
// ============================================================================

// [RF_12] Consultar empleados registrados
$empleadosBD = [
    ["id" => "EMP-01", "nombre" => "Jorge Ivan Muñiz Samano", "usuario" => "admin_jorge", "rol" => "Administrador", "estatus" => "Activo"],
    ["id" => "EMP-02", "nombre" => "Hazziel Enrique Ramirez", "usuario" => "cajero_hazziel", "rol" => "Cajero", "estatus" => "Activo"],
    ["id" => "EMP-03", "nombre" => "Luis Angel Jacobo Vite", "usuario" => "almacen_jacobo", "rol" => "Almacenista", "estatus" => "Inactivo"]
];

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // [RF_11] Registrar empleado
    if ($accion === 'agregar') {
        $nombre = $_POST['nombre'];
        $usuario = $_POST['usuario'];
        $password = $_POST['password'];
        $rol = $_POST['rol'];

        // Validación: Campos obligatorios y empleado único (simulado)
        $existe = false;
        foreach($empleadosBD as $emp) { if($emp['usuario'] === $usuario) { $existe = true; } }

        if (!$existe && !empty($nombre) && !empty($password)) {
            // Aquí Jacobo hará el: INSERT INTO Empleados (nombre, usuario, password, rol, estatus) VALUES (...)
            $mensaje = "<div class='alert alert-success'>Empleado '$nombre' registrado con éxito.</div>";
        } else {
            $mensaje = "<div class='alert alert-danger'>Error: El nombre de usuario ya existe o faltan datos.</div>";
        }
    }

    // [RF_13] Modificar datos de empleado
    if ($accion === 'editar') {
        $id = $_POST['id_empleado'];
        $nombre = $_POST['nombre'];
        $rol = $_POST['rol'];
        
        // Aquí Hazziel hará el: UPDATE Empleados SET nombre = ?, rol = ? WHERE id = ?
        // (Nota: La contraseña se actualizaría si no viene vacía en un flujo real)
        $mensaje = "<div class='alert alert-success'>Datos del empleado actualizados correctamente.</div>";
    }

    // [RF_14] Cambiar estatus de empleado (Baja lógica)
    if ($accion === 'cambiar_estatus') {
        $id = $_POST['id_empleado'];
        $nuevo_estatus = $_POST['nuevo_estatus'];
        // Aquí harán el: UPDATE Empleados SET estatus = ? WHERE id = ?
        $mensaje = "<div class='alert alert-warning'>El estatus del empleado ha cambiado a $nuevo_estatus.</div>";
    }
}

// Búsqueda (Filtro GET para RF_12)
$busqueda = $_GET['buscar'] ?? '';
if (!empty($busqueda)) {
    $empleadosBD = array_filter($empleadosBD, function($e) use ($busqueda) {
        return stripos($e['nombre'], $busqueda) !== false || stripos($e['usuario'], $busqueda) !== false;
    });
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados - Abarrotes Vilches</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; }
        :root { --primary-color: #2563eb; --sidebar-bg: #1e293b; --bg-color: #f1f5f9; --text-dark: #0f172a; --white: #ffffff; --danger: #ef4444; --success: #22c55e; --warning: #eab308; }
        body { display: flex; height: 100vh; background-color: var(--bg-color); color: var(--text-dark); }
        .sidebar { width: 260px; background-color: var(--sidebar-bg); color: var(--white); display: flex; flex-direction: column; }
        .brand { padding: 24px; text-align: center; border-bottom: 1px solid #334155; }
        .menu { list-style: none; padding: 20px 0; flex: 1; }
        .menu li a { display: block; padding: 15px 24px; color: #cbd5e1; text-decoration: none; }
        .menu li.active a { background-color: #334155; border-left: 4px solid var(--primary-color); color: white;}
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .topbar { background-color: var(--white); padding: 20px 40px; display: flex; justify-content: space-between; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .page-content { padding: 40px; overflow-y: auto; flex: 1; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; color: white; }
        .btn-primary { background-color: var(--primary-color); }
        .btn-warning { background-color: var(--warning); color: black; }
        .btn-danger { background-color: var(--danger); }
        .btn-success { background-color: var(--success); }
        .card { background-color: var(--white); border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 6px; }
        .alert-success { background-color: #dcfce7; color: #166534; }
        .alert-danger { background-color: #fee2e2; color: #991b1b; }
        .alert-warning { background-color: #fef08a; color: #854d0e; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 100; }
        .modal-content { background: white; padding: 30px; border-radius: 8px; width: 400px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px; margin-bottom: 15px; }
        .search-bar { display: flex; gap: 10px; margin-bottom: 20px; }
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
        <header class="topbar"><div>Recursos Humanos</div><div>Fecha: <?php echo date('d/m/Y'); ?></div></header>
        <div class="page-content">
            <?php echo $mensaje; ?>

            <div class="page-header">
                <h1>Directorio de Empleados</h1>
                <button class="btn btn-primary" onclick="abrirModal('modalAgregar')">+ Registrar Empleado</button>
            </div>

            <div class="card">
                <form method="GET" action="empleados.php" class="search-bar">
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre o usuario..." style="margin:0;">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </form>

                <table>
                    <thead>
                        <tr><th>ID</th><th>Nombre</th><th>Usuario</th><th>Rol</th><th>Estatus</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($empleadosBD as $emp): ?>
                            <tr>
                                <td><?php echo $emp['id']; ?></td>
                                <td><?php echo $emp['nombre']; ?></td>
                                <td><?php echo $emp['usuario']; ?></td>
                                <td><?php echo $emp['rol']; ?></td>
                                <td><span style="font-weight:bold; color: <?php echo $emp['estatus'] == 'Activo' ? 'green' : 'red'; ?>"><?php echo $emp['estatus']; ?></span></td>
                                <td style="display: flex; gap: 5px;">
                                    <button class="btn btn-warning" style="padding: 5px 10px;" onclick="abrirModalEditar('<?php echo $emp['id']; ?>', '<?php echo $emp['nombre']; ?>', '<?php echo $emp['rol']; ?>')">Editar</button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="accion" value="cambiar_estatus">
                                        <input type="hidden" name="id_empleado" value="<?php echo $emp['id']; ?>">
                                        <?php if ($emp['estatus'] == 'Activo'): ?>
                                            <input type="hidden" name="nuevo_estatus" value="Inactivo">
                                            <button type="submit" class="btn btn-danger" style="padding: 5px 10px;" onclick="return confirm('¿Desactivar acceso a este empleado?');">Desactivar</button>
                                        <?php else: ?>
                                            <input type="hidden" name="nuevo_estatus" value="Activo">
                                            <button type="submit" class="btn btn-success" style="padding: 5px 10px;" onclick="return confirm('¿Restaurar acceso?');">Activar</button>
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

    <div id="modalAgregar" class="modal">
        <div class="modal-content">
            <h3>Registrar Nuevo Empleado</h3>
            <form method="POST" action="empleados.php">
                <input type="hidden" name="accion" value="agregar">
                <label>Nombre Completo *</label><input type="text" name="nombre" class="form-control" required>
                <label>Usuario *</label><input type="text" name="usuario" class="form-control" required>
                <label>Contraseña *</label><input type="password" name="password" class="form-control" required>
                <label>Rol *</label>
                <select name="rol" class="form-control" required>
                    <option value="Administrador">Administrador</option>
                    <option value="Cajero">Cajero</option>
                    <option value="Almacenista">Almacenista</option>
                </select>
                <div style="display:flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn btn-danger" onclick="document.getElementById('modalAgregar').style.display='none'">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <h3>Modificar Empleado</h3>
            <form method="POST" action="empleados.php">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id_empleado" id="edit_id">
                <label>Nombre Completo *</label><input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                <label>Nueva Contraseña (Opcional)</label><input type="password" name="password" class="form-control" placeholder="Dejar en blanco para conservar actual">
                <label>Rol *</label>
                <select name="rol" id="edit_rol" class="form-control" required>
                    <option value="Administrador">Administrador</option>
                    <option value="Cajero">Cajero</option>
                    <option value="Almacenista">Almacenista</option>
                </select>
                <div style="display:flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn btn-danger" onclick="document.getElementById('modalEditar').style.display='none'">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModal(id) { document.getElementById(id).style.display = 'flex'; }
        function abrirModalEditar(id, nombre, rol) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_rol').value = rol;
            abrirModal('modalEditar');
        }
    </script>
</body>
</html>