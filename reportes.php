<?php
// Simulación de procesamiento de métricas agregadas en SQL Server: SUM(), COUNT()
$ingresosTotales = 14250.00;
$mermasTotales = 412.00;

$vistaPreviaReporte = [
    ["fecha" => "18/05/2026", "categoria" => "Abarrotes en General", "unidades" => "142 un.", "monto" => 3450.00, "margen" => "+18%"],
    ["fecha" => "17/05/2026", "categoria" => "Lácteos y Quesos", "unidades" => "89 un.", "monto" => 2110.00, "margen" => "+15%"]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Abarrotes Vilches</title>
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
        .filter-card { background-color: var(--white); padding: 24px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 30px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; align-items: flex-end; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-size: 0.85rem; font-weight: 600; color: var(--text-light); }
        .form-control { padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; outline: none; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; transition: 0.2s; }
        .btn-primary { background-color: var(--primary-color); color: var(--white); }
        .btn-pdf { background-color: var(--danger); color: var(--white); }
        .btn-excel { background-color: var(--success); color: var(--white); }
        .metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .metric-card { background-color: var(--white); padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border-left: 4px solid var(--primary-color); }
        .metric-card.sales { border-left-color: var(--success); }
        .metric-card.mermas { border-left-color: var(--danger); }
        .metric-title { font-size: 0.85rem; color: var(--text-light); text-transform: uppercase; margin-bottom: 5px; font-weight: 600; }
        .metric-value { font-size: 1.6rem; font-weight: 700; }
        .card { background-color: var(--white); border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; }
        .card-header { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
        table { width: 100%; border-collapse: collapse; }
        thead { background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; }
        th, td { padding: 16px 24px; text-align: left; }
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
            <li><a href="empleados.php">Empleados</a></li>
            <li class="active"><a href="reportes.php">Reportes</a></li>
        </ul>
        <div class="user-profile"><p>Administrador</p></div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div>Inteligencia de Negocio</div>
            <div>Fecha: <?php echo date('d/m/Y'); ?></div>
        </header>

        <div class="page-content">
            <div class="page-header">
                <h1>Generar Reportes Gerenciales</h1>
            </div>

            <section class="filter-card">
                <div class="form-group">
                    <label for="tipo-reporte">Tipo de Reporte</label>
                    <select id="tipo-reporte" class="form-control">
                        <option value="ventas">Resumen de Ventas y Balance</option>
                        <option value="inventario">Existencias y Stock Crítico</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fecha-inicio">Fecha Inicial</label>
                    <input type="date" id="fecha-inicio" class="form-control" value="2026-05-01">
                </div>
                <div class="form-group">
                    <label for="fecha-fin">Fecha Final</label>
                    <input type="date" id="fecha-fin" class="form-control" value="2026-05-18">
                </div>
                <div>
                    <button class="btn btn-primary" style="width: 100%;">Consultar</button>
                </div>
            </section>

            <div class="metrics-grid">
                <div class="metric-card sales">
                    <div class="metric-title">Ingresos Totales</div>
                    <div class="metric-value">$<?php echo number_format($ingresosTotales, 2); ?></div>
                </div>
                <div class="metric-card mermas">
                    <div class="metric-title">Pérdida por Merma</div>
                    <div class="metric-value">$<?php echo number_format($mermasTotales, 2); ?></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Vista Previa del Reporte</h3>
                    <div>
                        <button class="btn btn-pdf">Exportar PDF</button>
                        <button class="btn btn-excel">Exportar Excel</button>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Concepto / Categoría</th>
                            <th>Unidades</th>
                            <th>Monto Total</th>
                            <th>Margen Neto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vistaPreviaReporte as $fila) { ?>
                            <tr>
                                <td><?php echo $fila['fecha']; ?></td>
                                <td><?php echo $fila['categoria']; ?></td>
                                <td><?php echo $fila['unidades']; ?></td>
                                <td>$<?php echo number_format($fila['monto'], 2); ?></td>
                                <td style="color: var(--success); font-weight: 600;"><?php echo $fila['margen']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>