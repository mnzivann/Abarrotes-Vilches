<?php
session_start();
require_once 'conexion.php'; 
$conn = Conexion::conectar(); 

// ============================================================================
// MÓDULO DE VENTAS - LÓGICA DE BACKEND
// ============================================================================

$mensaje = "";

/**
 * [RF_10] VENTA_CANCELAR
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion === 'cancelar_venta') {
        $ticket = $_POST['ticket'];
        
        try {
            $conn->beginTransaction(); // Iniciamos transacción segura

            // 1. Verificar que la venta exista y no esté cancelada previamente
            $stmtCheck = $conn->prepare("SELECT estado FROM Ventas WHERE ticket = ?");
            $stmtCheck->execute([$ticket]);
            $ventaActual = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($ventaActual && $ventaActual['estado'] !== 'Cancelada') {
                
                // 2. Cambiar estado a Cancelada en la tabla Ventas
                $stmtUpdateVenta = $conn->prepare("UPDATE Ventas SET estado = 'Cancelada' WHERE ticket = ?");
                $stmtUpdateVenta->execute([$ticket]);

                // 3. Obtener los detalles de los productos para devolver el stock
                $stmtDetalle = $conn->prepare("SELECT id_producto, cantidad FROM DetalleVenta WHERE ticket = ?");
                $stmtDetalle->execute([$ticket]);
                $articulos = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

                // 4. Devolver stock e insertar movimiento en bitácora
                $stmtUpdateStock = $conn->prepare("UPDATE Productos SET stock = stock + ? WHERE id_producto = ?");
                
                // CORRECCIÓN DEL ERROR ROJO: Cambiamos 'Entrada por Cancelación' a 'Cancelacion' para que quepa en el VARCHAR
                $stmtMovimiento = $conn->prepare("INSERT INTO MovimientosInventario (id_producto, tipo, cantidad) VALUES (?, 'Cancelacion', ?)");

                foreach ($articulos as $art) {
                    $stmtUpdateStock->execute([$art['cantidad'], $art['id_producto']]);
                    $stmtMovimiento->execute([$art['id_producto'], $art['cantidad']]);
                }

                $conn->commit(); // Confirmamos todos los cambios
                $mensaje = "<div style='padding: 15px; margin-bottom: 20px; border-radius: 6px; background-color: #fef08a; color: #854d0e; border: 1px solid #fde047;'>
                                ✅ La venta con ticket <b>$ticket</b> ha sido cancelada y los productos regresaron al inventario.
                            </div>";
            } else {
                $conn->rollBack();
                $mensaje = "<div style='padding: 15px; margin-bottom: 20px; border-radius: 6px; background-color: #fee2e2; color: #991b1b;'>La venta ya estaba cancelada o no existe.</div>";
            }

        } catch(PDOException $e) {
            $conn->rollBack();
            $mensaje = "<div style='padding: 15px; margin-bottom: 20px; border-radius: 6px; background-color: #fee2e2; color: #991b1b;'>Error al cancelar la venta: " . $e->getMessage() . "</div>";
        }
    }
}

/**
 * [RF_09] VENTA_CONSULTAR (SELECT PRINCIPAL)
 */
$sqlVentas = "SELECT v.ticket, v.fecha, e.nombre AS cajero, v.total, v.estado 
              FROM Ventas v 
              LEFT JOIN Empleados e ON v.id_empleado = e.id_empleado 
              ORDER BY v.fecha DESC";
$stmtVentas = $conn->query($sqlVentas);
$ventasEnSQLServer = $stmtVentas->fetchAll(PDO::FETCH_ASSOC);

// Formateo de datos para la vista
foreach ($ventasEnSQLServer as &$v) {
    $v['clase'] = ($v['estado'] === 'Completada') ? 'badge-success' : 'badge-danger';
    $v['permiso_cancelar'] = ($v['estado'] === 'Completada');
    $nombres = explode(' ', $v['cajero']);
    $v['cajero'] = $nombres[0] ?? 'Cajero';
    $v['fecha'] = date('d/m/Y H:i', strtotime($v['fecha']));
}
unset($v);

/**
 * CONSULTA DE DETALLES PARA EL MODAL DE JAVASCRIPT
 */
$sqlDetalles = "SELECT dv.ticket, p.nombre, dv.cantidad, dv.subtotal 
                FROM DetalleVenta dv 
                INNER JOIN Productos p ON dv.id_producto = p.id_producto";
$stmtDetalles = $conn->query($sqlDetalles);
$todosLosDetalles = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);

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
        .user-profile { padding: 20px; background-color: #0f172a; text-align: center; font-size: 0.9rem; border-top: 1px solid #334155; }
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
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        
        /* Estilos del Modal */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 100; }
        .modal-content { background: white; padding: 30px; border-radius: 8px; width: 500px; max-height: 80vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 15px; }
        .close-btn { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #64748b; }
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
        <div class="user-profile">
            <p style="margin-bottom: 10px; color: #cbd5e1;">
                👤 <?php echo $_SESSION['usuario'] ?? 'Usuario'; ?>
            </p>
            <a href="logout.php" style="display: block; background-color: #ef4444; color: white; text-decoration: none; padding: 8px; border-radius: 4px; font-weight: bold;">Cerrar Sesión</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div>Módulo de Ventas</div>
            <div>Fecha: <?php echo date('d/m/Y'); ?></div>
        </header>

        <div class="page-content">
            <?php echo $mensaje; ?>
            
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
                        <?php if(count($ventasEnSQLServer) > 0): ?>
                            <?php foreach ($ventasEnSQLServer as $venta) { ?>
                                <tr>
                                    <td><?php echo $venta['ticket']; ?></td>
                                    <td><?php echo $venta['fecha']; ?></td>
                                    <td><?php echo htmlspecialchars($venta['cajero']); ?></td>
                                    <td>$<?php echo number_format($venta['total'], 2); ?></td>
                                    <td><span class="badge <?php echo $venta['clase']; ?>"><?php echo $venta['estado']; ?></span></td>
                                    <td style="display: flex; gap: 5px;">
                                        <button type="button" class="btn btn-action" style="background-color: #e2e8f0; border: none; font-weight: 600; color: #0f172a;" onclick="abrirModalDetalle('<?php echo $venta['ticket']; ?>')">Ver Detalle</button>
                                        
                                        <?php if ($venta['permiso_cancelar']) { ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="accion" value="cancelar_venta">
                                                <input type="hidden" name="ticket" value="<?php echo $venta['ticket']; ?>">
                                                <button type="submit" class="btn btn-action btn-delete" onclick="return confirm('¿Estás seguro de que deseas cancelar la venta <?php echo $venta['ticket']; ?>? Esta acción regresará los productos al inventario.');">Cancelar</button>
                                            </form>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align: center;">No hay ventas registradas aún.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="modalDetalle" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="tituloModalDetalle">Detalle del Ticket</h3>
                <button class="close-btn" onclick="document.getElementById('modalDetalle').style.display='none'">&times;</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Cant</th>
                        <th>Producto</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody id="cuerpoTablaDetalle">
                    </tbody>
            </table>
        </div>
    </div>

    <script>
        // Exportamos los datos de PHP a un arreglo de JavaScript
        const todosLosDetalles = <?php echo json_encode($todosLosDetalles); ?>;

        function abrirModalDetalle(ticket) {
            // Actualizamos el título
            document.getElementById('tituloModalDetalle').innerText = 'Artículos del Ticket: ' + ticket;
            
            // Filtramos la información para mostrar solo los productos de este ticket
            const detallesTicket = todosLosDetalles.filter(item => item.ticket === ticket);
            const tbody = document.getElementById('cuerpoTablaDetalle');
            tbody.innerHTML = ''; // Limpiamos la tabla

            if (detallesTicket.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;">No se encontraron artículos para este ticket.</td></tr>';
            } else {
                detallesTicket.forEach(prod => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${prod.cantidad}</td>
                            <td>${prod.nombre}</td>
                            <td style="font-weight:bold;">$${parseFloat(prod.subtotal).toFixed(2)}</td>
                        </tr>
                    `;
                });
            }

            // Mostramos el modal
            document.getElementById('modalDetalle').style.display = 'flex';
        }
    </script>
</body>
</html>