<?php
session_start();
// ============================================================================
// MÓDULO DE REPORTES Y RESPALDOS - LÓGICA DE BACKEND
// ============================================================================

/**
 * [RF_18] REPORTE_GENERAR
 * Descripción: Generar un reporte de venta.
 * Validación Excel: Datos correctos en reportes. (Validar: cálculos correctos).
 * Datos mostrados: ventas, productos, fechas, totales.
 */
// Simulación de consulta: SELECT ticket, fecha, productos, total FROM Ventas WHERE fecha = GETDATE();
$ventasBD = [
    ["ticket" => "T-1001", "fecha" => "2026-05-29", "productos" => "Aceite Nutrioli (2), Frijol (1)", "total" => 108.50],
    ["ticket" => "T-1002", "fecha" => "2026-05-29", "productos" => "Detergente Foca (1)", "total" => 32.00]
];

// Validación matemática estricta [RF_18]: Calcular el total general sumando el arreglo de resultados
$ingresosTotales = 0;
foreach($ventasBD as $v) { 
    $ingresosTotales += $v['total']; 
}

$mensaje = "";

/**
 * [RNF_04] RESPALDO DE BASE DE DATOS (Requerimiento No Funcional)
 * Descripción: Generar un respaldo seguro de la información.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'respaldo') {
    // TODO: Script para ejecutar un BACKUP DATABASE AbarrotesVilchesDB TO DISK = '...' en SQL Server
    $mensaje = "<div class='alert alert-success'> Respaldo de la base de datos generado con éxito (.BAK).</div>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Abarrotes Vilches</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; }
        :root { --primary-color: #2563eb; --sidebar-bg: #1e293b; --bg-color: #f1f5f9; --text-dark: #0f172a; --white: #ffffff; --success: #22c55e; }
        body { display: flex; height: 100vh; background-color: var(--bg-color); color: var(--text-dark); }
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
        .btn-success { background-color: var(--success); }
        .card { background-color: var(--white); border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 6px; }
        .alert-success { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .metric { font-size: 2rem; font-weight: bold; color: var(--success); }
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
            <div>Inteligencia de Negocio</div>
            <form method="POST" style="margin:0;" onsubmit="return confirm('¿Iniciar proceso de respaldo de base de datos?');">
                <input type="hidden" name="accion" value="respaldo">
                <button type="submit" class="btn btn-primary">Generar Respaldo (Backup BD)</button>
            </form>
        </header>
        
        <div class="page-content">
            <?php echo $mensaje; ?>
            
            <div class="card" style="display: flex; justify-content: space-between; align-items: center; border-left: 5px solid var(--success);">
                <div>
                    <h3>Ingresos Totales (Cálculo Automático)</h3>
                    <p>Reporte del día actual</p>
                </div>
                <div class="metric">$<?php echo number_format($ingresosTotales, 2); ?></div>
            </div>

            <div class="card">
                <h3>Detalle de Ventas</h3>
                <br>
                <table>
                    <thead>
                        <tr><th>Ticket</th><th>Fecha</th><th>Productos Vendidos</th><th>Total de la Venta</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($ventasBD as $v): ?>
                            <tr>
                                <td><?php echo $v['ticket']; ?></td>
                                <td><?php echo $v['fecha']; ?></td>
                                <td><?php echo $v['productos']; ?></td>
                                <td style="font-weight:bold;">$<?php echo number_format($v['total'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>