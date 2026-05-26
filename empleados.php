<?php
// Simulación de consulta a SQL Server: SELECT * FROM Empleados;
$empleadosEnSQLServer = [
    ["id" => "EMP-01", "nombre" => "Jorge Ivan Muñiz Samano", "puesto" => "Administrador", "telefono" => "477-555-0101", "estado" => "Activo", "clase" => "badge-success"],
    ["id" => "EMP-02", "nombre" => "Hazziel Enrique Ramirez Vilches", "puesto" => "Cajero", "telefono" => "477-555-0202", "estado" => "Activo", "clase" => "badge-success"],
    ["id" => "EMP-03", "nombre" => "Luis Angel Jacobo Vite", "puesto" => "Almacenista", "telefono" => "477-555-0303", "estado" => "Inactivo", "clase" => "badge-danger"]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados - Abarrotes Vilches</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; }
        :root { --primary-color: #2563eb; --sidebar-bg: #1e293b; --sidebar-hover: #334155; --bg-color: #f1f5f9; --text-dark: #0f172a; --text-light: #64748b; --white: #ffffff; --danger: #ef4444; --success: #22c55e; --warning: #eab308; }
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
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; transition: 0.2s; }
        .btn-primary { background-color: var(--primary-color); color: var(--white); }
        .btn-action { padding: 6px 12px; font-size: 0.85rem; border-radius: 4px; margin-right: 5px; }
        .btn-edit { background-color: var(--warning); color: #000; }
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
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="brand"><h2>Abarrotes Vilches</h2><span>Control de Sistema</span></div>
        <ul class="menu">
            <li><a href="index.php">Productos</a></li>
            <li><a href="inventario.php">Inventario</a></li>
            <li><a href="ventas.php">Ventas</a></li>
            <li><a href="mermas.php">Mermas</a></li>
            <li class="active"><a href="empleados.php">Empleados</a></li>
            <li><a href="reportes.php">Reportes</a></li>
        </ul>
        <div class="user-profile"><p>Administrador</p></div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div>Gestión de Recursos Humanos</div>
            <div>Fecha: <?php echo date('d/m/Y'); ?></div>
        </header>

        <div class="page-content">
            <div class="page-header">
                <h1>Directorio de Empleados</h1>
                <button class="btn btn-primary">Agregar Empleado</button>
            </div>

            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>ID Empleado</th>
                            <th>Nombre Completo</th>
                            <th>Rol / Puesto</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($empleadosEnSQLServer as $emp) { ?>
                            <tr>
                                <td><?php echo $emp['id']; ?></td>
                                <td><?php echo $emp['nombre']; ?></td>
                                <td><?php echo $emp['puesto']; ?></td>
                                <td><?php echo $emp['telefono']; ?></td>
                                <td><span class="badge <?php echo $emp['clase']; ?>"><?php echo $emp['estado']; ?></span></td>
                                <td>
                                    <button class="btn btn-action btn-edit">Editar</button>
                                    <button class="btn btn-action btn-delete">Eliminar</button>
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