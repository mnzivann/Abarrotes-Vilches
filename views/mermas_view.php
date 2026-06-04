<?php
/**
 * @var array $mermasBD
 * @var array $productosDisponibles
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mermas - Abarrotes Vilches</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; }
        :root { --primary-color: #2563eb; --sidebar-bg: #1e293b; --bg-color: #f1f5f9; --text-dark: #0f172a; --white: #ffffff; --danger: #ef4444; --success: #22c55e; --warning: #eab308; }
        body { display: flex; height: 100vh; background-color: var(--bg-color); color: var(--text-dark); }
        .sidebar { width: 260px; background-color: var(--sidebar-bg); color: var(--white); display: flex; flex-direction: column; box-shadow: 4px 0 10px rgba(0,0,0,0.1); z-index: 20;}
        .brand { padding: 24px; text-align: center; border-bottom: 1px solid #334155; }
        .brand h2 { font-size: 1.2rem; letter-spacing: 1px; }
        .brand span { font-size: 0.8rem; color: #94a3b8; }
        .menu { list-style: none; padding: 20px 0; flex: 1; }
        .menu li a { display: block; padding: 15px 24px; color: #cbd5e1; text-decoration: none; }
        .menu li.active a { background-color: #334155; border-left: 4px solid var(--primary-color); color: white;}
        .user-profile { padding: 20px; background-color: #0f172a; text-align: center; font-size: 0.9rem; border-top: 1px solid #334155; }
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .topbar { background-color: var(--white); padding: 20px 40px; display: flex; justify-content: space-between; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .page-content { padding: 40px; overflow-y: auto; flex: 1; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; color: white; }
        .btn-primary { background-color: var(--primary-color); }
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
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="brand"><h2>Abarrotes Vilches</h2><span>Control de Sistema</span></div>
        <ul class="menu">
            <li><a href="index.php">Productos</a></li>
            <li><a href="categorias.php">Categorías</a></li>
            <li><a href="inventario.php">Inventario</a></li>
            <li><a href="ventas.php">Ventas</a></li>
            <li class="active"><a href="mermas.php">Mermas</a></li>
            
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
                <li><a href="empleados.php">Empleados</a></li>
                <li><a href="reportes.php">Reportes</a></li>
            <?php endif; ?>
        </ul>
        <div class="user-profile">
            <p style="margin-bottom: 10px; color: #cbd5e1;">👤 <?php echo $_SESSION['usuario'] ?? 'Usuario'; ?></p>
            <a href="logout.php" style="display: block; background-color: #ef4444; color: white; text-decoration: none; padding: 8px; border-radius: 4px; font-weight: bold;">Cerrar Sesión</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar"><div>Control de Pérdidas</div><div>Fecha: <?php echo date('d/m/Y'); ?></div></header>
        <div class="page-content">
            <?php echo $mensaje; ?>

            <div class="page-header">
                <h1>Registro de Mermas y Caducidad</h1>
                <button class="btn btn-danger" onclick="document.getElementById('modalMerma').style.display='flex'">+ Registrar Merma</button>
            </div>

            <div class="card">
                <table>
                    <thead>
                        <tr><th>ID</th><th>Producto</th><th>Cantidad</th><th>Motivo</th><th>Fecha</th><th>Estatus</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                        <?php if (count($mermasBD) > 0): ?>
                            <?php foreach ($mermasBD as $merma): ?>
                                <tr>
                                    <td><?php echo $merma['id']; ?></td>
                                    <td><?php echo $merma['producto']; ?></td>
                                    <td><?php echo $merma['cantidad']; ?></td>
                                    <td><?php echo $merma['motivo']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($merma['fecha'])); ?></td>
                                    <td><span style="font-weight:bold; color: <?php echo $merma['estatus'] == 'Activo' ? 'green' : 'red'; ?>"><?php echo $merma['estatus']; ?></span></td>
                                    <td>
                                        <form method="POST" action="mermas.php" style="display:inline;">
                                            <input type="hidden" name="accion" value="cambiar_estatus">
                                            <input type="hidden" name="id_merma" value="<?php echo $merma['id']; ?>">
                                            <?php if ($merma['estatus'] == 'Activo'): ?>
                                                <input type="hidden" name="nuevo_estatus" value="Inactivo">
                                                <button type="submit" class="btn btn-danger" style="padding: 5px 10px;" onclick="return confirm('¿Anular este registro? Las piezas regresarán al inventario.');">Anular</button>
                                            <?php else: ?>
                                                <input type="hidden" name="nuevo_estatus" value="Activo">
                                                <button type="submit" class="btn btn-success" style="padding: 5px 10px;" onclick="return confirm('¿Restaurar este registro? Las piezas se volverán a descontar.');">Restaurar</button>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align:center;">No hay mermas registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="modalMerma" class="modal">
        <div class="modal-content">
            <h3>Reportar Producto Dañado</h3>
            <form method="POST" action="mermas.php">
                <input type="hidden" name="accion" value="registrar">
                
                <label>Producto *</label>
                <select name="producto" class="form-control" required>
                    <option value="">Selecciona el producto...</option>
                    <?php foreach($productosDisponibles as $prod): ?>
                        <option value="<?php echo $prod['id_producto']; ?>">
                            <?php echo $prod['nombre']; ?> (Disp: <?php echo $prod['stock']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <label>Cantidad (Mayor a 0) *</label>
                <input type="number" name="cantidad" min="1" class="form-control" required>
                
                <label>Motivo *</label>
                <select name="motivo" class="form-control" required>
                    <option value="Caducidad">Caducidad</option>
                    <option value="Empaque Roto">Empaque Roto</option>
                    <option value="Dañado en transporte">Dañado en transporte</option>
                    <option value="Robo o Extravío">Robo o Extravío</option>
                </select>
                
                <div style="display:flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn btn-primary" style="background-color:gray;" onclick="document.getElementById('modalMerma').style.display='none'">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Merma</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>