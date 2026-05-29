<?php
session_start();
// Simulación de consulta a SQL Server: SELECT * FROM Ventas;
$ventasEnSQLServer = [
    ["ticket" => "V-00892", "fecha" => "18/05/2026 14:20", "cajero" => "Cajero 1", "total" => 145.50, "estado" => "Completada", "clase" => "badge-success", "permiso_cancelar" => true]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas - Abarrotes Vilches</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; }
        :root { --primary-color: #2563eb; --sidebar-bg: #1e293b; --sidebar-hover: #334155; --bg-color: #f1f5f9; --text-dark: #0f172a; --text-light: #64748b; --white: #ffffff; --danger: #ef4444; --success: #22c55e; }
        body { display: flex; height: 100vh; background-color: var(--bg-color); color: var(--text-dark); }
        .sidebar { width: 260px; background-color: var(--sidebar-bg); color: var(--white); display: flex; flex-direction: column; box-shadow: 4px 0 10px rgba(0,0,0,0.1); }
        .brand { padding: 24px; text-align: center; border-bottom: 1px solid #334155; }
        .brand h2 { font-size: 1.2rem; letter-spacing: 1px; }
        .brand span { font-size: 0.8rem; color: #94a3b8; }
        .menu { list-style: none; padding: 20px 0; flex: 1; }
        .menu li a { display: flex; align-items: center; padding: 15px 24px; color: #cbd5e1; text-decoration: none; font-weight: 500; transition: all 0.3s ease; }
        .menu li a:hover, .menu li.active a { background-color: var(--sidebar-hover); color: var(--white); border-left: 4px solid var(--primary-color); }
        .user-profile { padding: 20px; background-color: #0f172a; text-align: center; font-size: 0.9rem; }
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .topbar { background-color: var(--white); padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); z-index: 10; }
        .page-content { padding: 40px; overflow-y: auto; flex: 1; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h1 { font-size: 1.8rem; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; transition: 0.2s; text-decoration: none; display: inline-block; }
        .btn-primary { background-color: var(--primary-color); color: var(--white); }
        .btn-action { padding: 6px 12px; font-size: 0.85rem; border-radius: 4px; margin-right: 5px; }
        .btn-delete { background-color: var(--danger); color: var(--white); }
        .card { background-color: var(--white); border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        thead { background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; }
        th, td { padding: 16px 24px; text-align: left; }
        th { color: var(--text-light); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; }
        td { border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }
        tbody tr:hover { background-color: #f8fafc; }
        .badge { padding: 4px 10px; border-radius: 999px; font-size: 0.8rem; font-weight: bold; }
        .badge-success { background-color: #dcfce7; color: #166534; }
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
            <li><a href="mermas.php">Mermas</a></li>
            
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
                <li><a href="empleados.php">Empleados</a></li>
                <li><a href="reportes.php">Reportes</a></li>
            <?php endif; ?>
        </ul>
        <div class="user-profile"><p>Administrador</p></div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div>Módulo de Ventas</div>
            <div>Fecha: <?php echo date('d/m/Y'); ?></div>
        </header>

        <div class="page-content">
            <div class="page-header">
                <h1>Historial de Ventas</h1>
                <a href="nueva_venta.php" class="btn btn-primary">Nueva Venta</a>
            </div>

            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>Ticket</th>
                            <th>Fecha y Hora</th>
                            <th>Atendió</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ventasEnSQLServer as $venta) { ?>
                            <tr>
                                <td><?php echo $venta['ticket']; ?></td>
                                <td><?php echo $venta['fecha']; ?></td>
                                <td><?php echo $venta['cajero']; ?></td>
                                <td>$<?php echo number_format($venta['total'], 2); ?></td>
                                <td><span class="badge <?php echo $venta['clase']; ?>"><?php echo $venta['estado']; ?></span></td>
                                <td>
                                    <button class="btn btn-action" style="background-color: #e2e8f0; border: none; font-weight: 600; color: #000;">Ver Detalle</button>
                                    <?php if ($venta['permiso_cancelar']) { ?>
                                        <button class="btn btn-action btn-delete">Cancelar</button>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>