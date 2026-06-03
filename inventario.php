<?php
session_start();
require_once 'conexion.php'; 
$conn = Conexion::conectar(); 

$mensaje = "";

// ============================================================================
// PROCESAMIENTO DE MOVIMIENTOS (POST)
// ============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id_producto = $_POST['id_producto'];
    $cantidad = intval($_POST['cantidad']);

    // 1. Consultar el stock actual DIRECTO de la BD para validaciones
    $stmt_stock = $conn->prepare("SELECT stock FROM Productos WHERE id_producto = ?");
    $stmt_stock->execute([$id_producto]);
    $row = $stmt_stock->fetch(PDO::FETCH_ASSOC);
    $stock_actual = $row ? intval($row['stock']) : 0;

    /**
     * [RF_05] INVENTARIO_ENTRADA
     */
    if ($accion === 'entrada') {
        if ($cantidad > 0) {
            try {
                $conn->beginTransaction(); // Iniciamos transacción segura
                
                // 1. Actualizar stock en Productos
                $sql_update = "UPDATE Productos SET stock = stock + ? WHERE id_producto = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->execute([$cantidad, $id_producto]);
                
                // 2. Guardar bitácora en MovimientosInventario
                $sql_mov = "INSERT INTO MovimientosInventario (id_producto, tipo, cantidad) VALUES (?, 'Entrada', ?)";
                $stmt_mov = $conn->prepare($sql_mov);
                $stmt_mov->execute([$id_producto, $cantidad]);
                
                $conn->commit(); // Confirmamos los cambios
                $mensaje = "<div class='alert alert-success'>Entrada registrada exitosamente: Se agregaron $cantidad unidades al inventario.</div>";
            } catch(PDOException $e) {
                $conn->rollBack(); // Si algo falla, deshacemos todo
                $mensaje = "<div class='alert alert-danger'>Error al registrar entrada: " . $e->getMessage() . "</div>";
            }
        } else {
            $mensaje = "<div class='alert alert-danger'>Error de Validación [RF_05]: La cantidad de entrada debe ser mayor a 0.</div>";
        }
    }

    /**
     * [RF_06] INVENTARIO_SALIDA
     */
    if ($accion === 'salida') {
        // Validación estricta: Cantidad válida y no supera el stock físico
        if ($cantidad > 0 && $stock_actual >= $cantidad) {
            try {
                $conn->beginTransaction();
                
                // 1. Restar stock en Productos
                $sql_update = "UPDATE Productos SET stock = stock - ? WHERE id_producto = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->execute([$cantidad, $id_producto]);
                
                // 2. Guardar bitácora en MovimientosInventario
                $sql_mov = "INSERT INTO MovimientosInventario (id_producto, tipo, cantidad) VALUES (?, 'Salida', ?)";
                $stmt_mov = $conn->prepare($sql_mov);
                $stmt_mov->execute([$id_producto, $cantidad]);
                
                $conn->commit();
                $mensaje = "<div class='alert alert-warning'>Salida registrada exitosamente: Se retiraron $cantidad unidades.</div>";
            } catch(PDOException $e) {
                $conn->rollBack();
                $mensaje = "<div class='alert alert-danger'>Error al registrar salida: " . $e->getMessage() . "</div>";
            }
        } 
        else if ($cantidad > $stock_actual) {
            $mensaje = "<div class='alert alert-danger'>Error de Validación [RF_06]: No hay suficiente stock para la salida. (Stock actual disponible: $stock_actual).</div>";
        } 
        else {
            $mensaje = "<div class='alert alert-danger'>Error: La cantidad de salida debe ser mayor a 0.</div>";
        }
    }
}

// ============================================================================
// [RF_07] INVENTARIO_CONSULTAR (SELECT REAL)
// ============================================================================
// Usamos AS id para que coincida exactamente con las variables que ya lee tu HTML
$sql = "SELECT id_producto AS id, nombre, stock, estatus FROM Productos";
$stmt = $conn->query($sql);
$productosBD = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - Abarrotes Vilches</title>
    <style>
        /* Estilos base del sistema */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; }
        :root { --primary-color: #2563eb; --sidebar-bg: #1e293b; --bg-color: #f1f5f9; --white: #ffffff; --danger: #ef4444; --success: #22c55e; --warning: #eab308; }
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
        .btn-success { background-color: var(--success); }
        .btn-warning { background-color: var(--warning); color: black; }
        .btn-danger { background-color: var(--danger); }
        .card { background-color: var(--white); border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); padding: 20px; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 6px; }
        .alert-success { background-color: #dcfce7; color: #166534; }
        .alert-warning { background-color: #fef08a; color: #854d0e; }
        .alert-danger { background-color: #fee2e2; color: #991b1b; }
        
        /* Modal Unificado para Entradas y Salidas */
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
            <li><a href="mermas.php">Mermas</a></li>
            
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
                <li><a href="empleados.php">Empleados</a></li>
                <li><a href="reportes.php">Reportes</a></li>
            <?php endif; ?>
        </ul>
        
        <div class="user-profile" style="padding: 20px; background-color: #0f172a; text-align: center; border-top: 1px solid #334155;">
            <p style="margin-bottom: 10px; color: #cbd5e1; font-size: 0.9rem;">
                <?php echo $_SESSION['usuario'] ?? 'Usuario'; ?> (<?php echo $_SESSION['rol'] ?? 'Rol'; ?>)
            </p>
            <a href="logout.php" style="display: block; background-color: #ef4444; color: white; text-decoration: none; padding: 8px; border-radius: 4px; font-weight: bold; font-size: 0.85rem; transition: 0.2s;">
                Cerrar Sesión
            </a>
        </div>
        </aside>

    <main class="main-content">
        <header class="topbar"><div>Control de Inventario</div><div>Fecha: <?php echo date('d/m/Y'); ?></div></header>
        <div class="page-content">
            <?php echo $mensaje; ?>
            
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h1>Niveles de Stock</h1>
                <div>
                    <button class="btn btn-success" onclick="abrirModal('entrada')">Registrar Entrada</button>
                    <button class="btn btn-warning" onclick="abrirModal('salida')">Registrar Salida</button>
                </div>
            </div>

            <div class="card">
                <table>
                    <thead><tr><th>ID</th><th>Producto</th><th>Stock Disponible</th><th>Estatus</th></tr></thead>
                    <tbody>
                        <?php foreach($productosBD as $p): ?>
                            <tr>
                                <td><?php echo $p['id']; ?></td>
                                <td><?php echo $p['nombre']; ?></td>
                                <td style="font-weight: bold; <?php echo $p['stock'] < 10 ? 'color: red;' : 'color: green;'; ?>"><?php echo $p['stock']; ?></td>
                                <td><?php echo $p['estatus']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="modalMovimiento" class="modal">
        <div class="modal-content">
            <h3 id="modalTitulo">Registrar Movimiento</h3>
            <form method="POST" action="inventario.php">
                <input type="hidden" name="accion" id="accionInput">
                
                <label>Producto *</label>
                <select name="id_producto" class="form-control" required>
                    <?php foreach($productosBD as $p): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo $p['nombre']; ?> (Stock: <?php echo $p['stock']; ?>)</option>
                    <?php endforeach; ?>
                </select>
                
                <label>Cantidad *</label>
                <input type="number" name="cantidad" min="1" class="form-control" required>
                
                <div style="display:flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn btn-danger" onclick="document.getElementById('modalMovimiento').style.display='none'">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="btnGuardar">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Función para reutilizar el modal cambiando el contexto (Entrada / Salida)
        function abrirModal(tipo) {
            document.getElementById('accionInput').value = tipo;
            document.getElementById('modalTitulo').innerText = tipo === 'entrada' ? 'Registrar Entrada' : 'Registrar Salida';
            document.getElementById('btnGuardar').className = tipo === 'entrada' ? 'btn btn-success' : 'btn btn-warning';
            document.getElementById('modalMovimiento').style.display = 'flex';
        }
    </script>
</body>
</html>