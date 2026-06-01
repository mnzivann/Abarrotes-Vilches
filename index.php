<?php
session_start();
// ============================================================================
// MÓDULO DE PRODUCTOS - LÓGICA DE BACKEND (CONTROLADOR SIMULADO)
// ============================================================================

/**
 * [RF_07] INVENTARIO CONSULTAR (Base base para productos)
 * Simulación de la Base de Datos.
 * En la integración final, esto será reemplazado por un SELECT a SQL Server.
 */
$productosBD = [
    ["id" => "PRD-001", "nombre" => "Aceite Nutrioli 946 ml", "categoria" => "Abarrotes", "precio" => 45.00, "stock" => 24, "estatus" => "Activo"],
    ["id" => "PRD-002", "nombre" => "Frijol La Sierra Bayos 560g", "categoria" => "Abarrotes", "precio" => 18.50, "stock" => 3, "estatus" => "Activo"],
    ["id" => "PRD-003", "nombre" => "Detergente Foca 1Kg", "categoria" => "Limpieza", "precio" => 32.00, "stock" => 0, "estatus" => "Inactivo"]
];

$mensaje = ""; // Variable para renderizar alertas en la Interfaz de Usuario

// PROCESAMIENTO DE FORMULARIOS (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    /**
     * [RF_01] PRODUCTO_AGREGAR
     * Descripción: El usuario captura los datos y da clic en "Agregar".
     * Validaciones aplicadas: Campos no vacíos, precio > 0, stock >= 0.
     */
    if ($accion === 'agregar') {
        $nombre = $_POST['nombre'];
        $categoria = $_POST['categoria'];
        $precio = floatval($_POST['precio']);
        $stock = intval($_POST['stock']);

        // Cumplimiento estricto de la regla de negocio del Excel
        if ($precio > 0 && $stock >= 0 && !empty($nombre)) {
            // TODO: Integrar consulta INSERT INTO Productos (...) VALUES (...)
            $mensaje = "<div class='alert alert-success'>Producto '$nombre' agregado correctamente.</div>";
        } else {
            $mensaje = "<div class='alert alert-danger'>Error de Validación: Verifica que el precio sea mayor a 0 y el stock no sea negativo.</div>";
        }
    }

    /**
     * [RF_03] PRODUCTO_MODIFICAR
     * Descripción: El usuario selecciona un producto y modifica sus datos.
     * Validaciones aplicadas: precio > 0, stock >= 0 antes de guardar.
     */
    if ($accion === 'editar') {
        $id = $_POST['id_producto'];
        $precio = floatval($_POST['precio']);
        $stock = intval($_POST['stock']);
        
        if ($precio > 0 && $stock >= 0) {
            // TODO: Integrar consulta UPDATE Productos SET precio = ?, stock = ? WHERE id = ?
            $mensaje = "<div class='alert alert-success'>Producto actualizado correctamente.</div>";
        }
    }

    /**
     * [RF_04] PRODUCTO_ELIMINAR
     * Descripción: El usuario cambia el estatus de un producto en lugar de eliminarlo de la BD (Baja Lógica).
     * Validaciones aplicadas: Confirmación en el frontend antes de procesar el cambio.
     */
    if ($accion === 'cambiar_estatus') {
        $id = $_POST['id_producto'];
        $nuevo_estatus = $_POST['nuevo_estatus'];
        // TODO: Integrar consulta UPDATE Productos SET estatus = ? WHERE id = ?
        $mensaje = "<div class='alert alert-warning'>El estatus del producto ha cambiado a $nuevo_estatus.</div>";
    }
}

/**
 * [RF_02] PRODUCTO_CONSULTAR
 * Descripción: El usuario busca productos por nombre o categoría y el sistema muestra los resultados.
 * Validaciones aplicadas: Coincidencia de nombre o categoría (Búsqueda GET).
 */
$busqueda = $_GET['buscar'] ?? '';
if (!empty($busqueda)) {
    // Simulación del filtro LIKE. 
    // En SQL Server será: SELECT * FROM Productos WHERE nombre LIKE '%$busqueda%' OR categoria LIKE '%$busqueda%'
    $productosBD = array_filter($productosBD, function($p) use ($busqueda) {
        return stripos($p['nombre'], $busqueda) !== false || stripos($p['categoria'], $busqueda) !== false;
    });
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Abarrotes Vilches</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; }
        :root { --primary-color: #2563eb; --sidebar-bg: #1e293b; --bg-color: #f1f5f9; --text-dark: #0f172a; --white: #ffffff; --danger: #ef4444; --success: #22c55e; --warning: #eab308; }
        body { display: flex; height: 100vh; background-color: var(--bg-color); color: var(--text-dark); }
        
        /* Sidebar y Topbar */
        .sidebar { width: 260px; background-color: var(--sidebar-bg); color: var(--white); display: flex; flex-direction: column; }
        .brand { padding: 24px; text-align: center; border-bottom: 1px solid #334155; }
        .menu { list-style: none; padding: 20px 0; flex: 1; }
        .menu li a { display: block; padding: 15px 24px; color: #cbd5e1; text-decoration: none; }
        .menu li.active a { background-color: #334155; border-left: 4px solid var(--primary-color); color: white;}
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .topbar { background-color: var(--white); padding: 20px 40px; display: flex; justify-content: space-between; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .page-content { padding: 40px; overflow-y: auto; flex: 1; }
        
        /* Controles y Botones */
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; color: white; transition: 0.2s; }
        .btn-primary { background-color: var(--primary-color); }
        .btn-warning { background-color: var(--warning); color: black; }
        .btn-danger { background-color: var(--danger); }
        .btn-action { padding: 6px 12px; font-size: 0.85rem; margin-right: 5px; }
        
        /* Buscador RF_02 */
        .search-bar { display: flex; gap: 10px; margin-bottom: 20px; }
        .form-control { padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; width: 100%; outline: none; }
        
        /* Tabla */
        .card { background-color: var(--white); border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .badge { padding: 4px 10px; border-radius: 999px; font-size: 0.8rem; font-weight: bold; }
        .bg-success { background-color: #dcfce7; color: #166534; }
        .bg-danger { background-color: #fee2e2; color: #991b1b; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 6px; }
        .alert-success { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-danger { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-warning { background-color: #fef08a; color: #854d0e; }

        /* Estilos para Modales (Ventanas Emergentes) */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 100; }
        .modal-content { background: white; padding: 30px; border-radius: 8px; width: 400px; }
        .modal-content h3 { margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-size: 0.9rem; font-weight: 600; }
        .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
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
        <header class="topbar">
            <div>Catálogo de Productos</div>
            <div>Fecha: <?php echo date('d/m/Y'); ?></div>
        </header>

        <div class="page-content">
            <?php echo $mensaje; ?>

            <div class="page-header">
                <h1>Gestión de Productos</h1>
                <button class="btn btn-primary" onclick="abrirModal('modalAgregar')">+ Agregar Producto</button>
            </div>

            <div class="card">
                <form method="GET" action="index.php" class="search-bar">
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre o categoría..." value="<?php echo htmlspecialchars($busqueda); ?>">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    <?php if(!empty($busqueda)): ?>
                        <a href="index.php" class="btn btn-warning" style="text-decoration:none;">Limpiar</a>
                    <?php endif; ?>
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($productosBD) > 0): ?>
                            <?php foreach ($productosBD as $p): ?>
                                <tr>
                                    <td><?php echo $p['id']; ?></td>
                                    <td><?php echo $p['nombre']; ?></td>
                                    <td><?php echo $p['categoria']; ?></td>
                                    <td>$<?php echo number_format($p['precio'], 2); ?></td>
                                    <td><?php echo $p['stock']; ?></td>
                                    <td>
                                        <span class="badge <?php echo $p['estatus'] == 'Activo' ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo $p['estatus']; ?>
                                        </span>
                                    </td>
                                    <td style="display: flex; gap: 5px;">
                                        <button class="btn btn-warning btn-action" onclick="abrirModalEditar('<?php echo $p['id']; ?>', '<?php echo $p['nombre']; ?>', <?php echo $p['precio']; ?>, <?php echo $p['stock']; ?>)">Editar</button>
                                        
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="accion" value="cambiar_estatus">
                                            <input type="hidden" name="id_producto" value="<?php echo $p['id']; ?>">
                                            <?php if ($p['estatus'] == 'Activo'): ?>
                                                <input type="hidden" name="nuevo_estatus" value="Inactivo">
                                                <button type="submit" class="btn btn-danger btn-action" onclick="return confirm('¿Seguro que deseas desactivar este producto?');">Desactivar</button>
                                            <?php else: ?>
                                                <input type="hidden" name="nuevo_estatus" value="Activo">
                                                <button type="submit" class="btn btn-success btn-action" onclick="return confirm('¿Reactivar producto?');">Activar</button>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align:center;">No se encontraron productos coincidentes.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="modalAgregar" class="modal">
        <div class="modal-content">
            <h3>Agregar Nuevo Producto</h3>
            <form method="POST" action="index.php">
                <input type="hidden" name="accion" value="agregar">
                
                <div class="form-group">
                    <label>Nombre del Producto *</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Categoría *</label>
                    <input type="text" name="categoria" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Precio (Mayor a 0) *</label>
                    <input type="number" name="precio" step="0.01" min="0.01" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Stock Inicial *</label>
                    <input type="number" name="stock" min="0" class="form-control" required>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-danger" onclick="cerrarModal('modalAgregar')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <h3>Modificar Producto</h3>
            <form method="POST" action="index.php">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id_producto" id="edit_id">
                
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" id="edit_nombre" class="form-control" disabled>
                </div>
                <div class="form-group">
                    <label>Precio *</label>
                    <input type="number" name="precio" id="edit_precio" step="0.01" min="0.01" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Stock *</label>
                    <input type="number" name="stock" id="edit_stock" min="0" class="form-control" required>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-danger" onclick="cerrarModal('modalEditar')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Funciones de control de UI para Modales
        function abrirModal(id) {
            document.getElementById(id).style.display = 'flex';
        }
        function cerrarModal(id) {
            document.getElementById(id).style.display = 'none';
        }
        function abrirModalEditar(id, nombre, precio, stock) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_precio').value = precio;
            document.getElementById('edit_stock').value = stock;
            abrirModal('modalEditar');
        }
    </script>
</body>
</html>