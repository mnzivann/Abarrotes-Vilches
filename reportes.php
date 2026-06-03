<?php
session_start();
require_once 'conexion.php'; 
$conn = Conexion::conectar(); 

// ============================================================================
// MÓDULO DE REPORTES Y RESPALDOS - LÓGICA DE BACKEND
// ============================================================================

$mensaje = "";

/**
 * [RNF_04] RESPALDO DE BASE DE DATOS (Requerimiento No Funcional)
 * Descripción: Generar un respaldo seguro de la información.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'respaldo') {
    try {
        // Ruta interna del contenedor de Docker donde SQL Server tiene permisos de escritura
        $ruta_respaldo = '/var/opt/mssql/data/AbarrotesVilches_Respaldo_' . date('Ymd_His') . '.bak';
        
        // Ejecutamos el comando nativo de Microsoft SQL Server para respaldos
        $sqlBackup = "BACKUP DATABASE AbarrotesVilchesDB TO DISK = '$ruta_respaldo' WITH FORMAT, INIT";
        $conn->exec($sqlBackup);
        
        $mensaje = "<div class='alert alert-success'>✅ <b>Respaldo generado con éxito.</b><br>El archivo se guardó de forma segura en el servidor de base de datos como: <code>$ruta_respaldo</code></div>";
    } catch(PDOException $e) {
        $mensaje = "<div class='alert alert-danger'>❌ <b>Error al generar el respaldo:</b> " . $e->getMessage() . "</div>";
    }
}

/**
 * [RF_18] REPORTE_GENERAR (SELECT COMPLEJO CON JOINS Y STRING_AGG)
 * Descripción: Generar un reporte de venta con productos concatenados.
 */
try {
    // Usamos STRING_AGG para concatenar los nombres y cantidades de productos de un mismo ticket en una sola fila
    $sqlReporte = "
        SELECT 
            v.ticket, 
            v.fecha, 
            v.total,
            STRING_AGG(p.nombre + ' (' + CAST(dv.cantidad AS VARCHAR) + ')', ', ') AS productos
        FROM Ventas v
        INNER JOIN DetalleVenta dv ON v.ticket = dv.ticket
        INNER JOIN Productos p ON dv.id_producto = p.id_producto
        WHERE v.estado = 'Completada'
        GROUP BY v.ticket, v.fecha, v.total
        ORDER BY v.fecha DESC
    ";
    
    $stmt = $conn->query($sqlReporte);
    $ventasBD = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $ventasBD = [];
    $mensaje = "<div class='alert alert-danger'>Error al cargar el reporte: " . $e->getMessage() . "</div>";
}

// Validación matemática estricta [RF_18]: Calcular el total general sumando el arreglo de resultados reales
$ingresosTotales = 0;
foreach($ventasBD as &$v) { 
    $ingresosTotales += $v['total']; 
    // Damos formato a la fecha de SQL Server a un formato más legible
    $v['fecha'] = date('d/m/Y H:i', strtotime($v['fecha']));
}
unset($v);

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
        .alert-danger { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .metric { font-size: 2rem; font-weight: bold; color: var(--success); }
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
        
        <div class="user-profile" style="padding: 20px; background-color: #0f172a; text-align: center; border-top: 1px solid #334155;">
            <p style="margin-bottom: 10px; color: #cbd5e1; font-size: 0.9rem;">
                👤 <?php echo $_SESSION['usuario'] ?? 'Usuario'; ?> (<?php echo $_SESSION['rol'] ?? 'Rol'; ?>)
            </p>
            <a href="logout.php" style="display: block; background-color: #ef4444; color: white; text-decoration: none; padding: 8px; border-radius: 4px; font-weight: bold; font-size: 0.85rem; transition: 0.2s;">
                Cerrar Sesión
            </a>
        </div>
        </aside>

    <main class="main-content">
        <header class="topbar">
            <div>Inteligencia de Negocio</div>
            <form method="POST" style="margin:0;" onsubmit="return confirm('¿Iniciar proceso de respaldo de la base de datos SQL Server?');">
                <input type="hidden" name="accion" value="respaldo">
                <button type="submit" class="btn btn-primary">Generar Respaldo (Backup BD)</button>
            </form>
        </header>
        
        <div class="page-content">
            <?php echo $mensaje; ?>
            
            <div class="card" style="display: flex; justify-content: space-between; align-items: center; border-left: 5px solid var(--success);">
                <div>
                    <h3>Ingresos Totales Históricos</h3>
                    <p>Suma calculada automáticamente de tickets completados</p>
                </div>
                <div class="metric">$<?php echo number_format($ingresosTotales, 2); ?></div>
            </div>

            <div class="card">
                <h3>Detalle de Ventas Consolidadas</h3>
                <br>
                <table>
                    <thead>
                        <tr><th>Ticket</th><th>Fecha</th><th>Productos Vendidos</th><th>Total de la Venta</th></tr>
                    </thead>
                    <tbody>
                        <?php if(count($ventasBD) > 0): ?>
                            <?php foreach($ventasBD as $v): ?>
                                <tr>
                                    <td><?php echo $v['ticket']; ?></td>
                                    <td><?php echo $v['fecha']; ?></td>
                                    <td><?php echo htmlspecialchars($v['productos']); ?></td>
                                    <td style="font-weight:bold;">$<?php echo number_format($v['total'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align: center;">No hay ventas registradas o completadas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>