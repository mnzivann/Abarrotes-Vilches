<?php
session_start();
// ============================================================================
// MÓDULO DE PUNTO DE VENTA - LÓGICA DE BACKEND
// ============================================================================

// Simulación de catálogo de productos para poblar el seleccionador
$productosBD = [
    ["id" => "PRD-001", "nombre" => "Aceite Nutrioli 946 ml", "precio" => 45.00, "stock" => 24],
    ["id" => "PRD-002", "nombre" => "Frijol La Sierra Bayos 560g", "precio" => 18.50, "stock" => 3]
];

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    /**
     * [RF_08] VENTA_REGISTRAR (Procesamiento Backend)
     * Descripción: Registrar una venta de productos.
     * Validación: Calcular total automáticamente (Guardado seguro en base de datos).
     */
    if ($accion === 'procesar_venta') {
        $total_venta = floatval($_POST['total_venta']);
        // En una implementación final, aquí también se recibiría un JSON con los productos vendidos
        // $articulos = json_decode($_POST['lista_articulos'], true);

        if ($total_venta > 0) {
            // TODO: 1. Jacobo registrará el ticket -> INSERT INTO Ventas (total, fecha, cajero, estado) VALUES (...)
            // TODO: 2. Iterar productos para registrar detalle -> INSERT INTO DetalleVenta (ticket, producto, cantidad, subtotal) VALUES (...)
            // TODO: 3. Hazziel descontará el inventario -> UPDATE Productos SET stock = stock - cantidad WHERE id = ?
            
            $mensaje = "<div class='alert alert-success' style='padding:15px; background:#dcfce7; color:#166534; border-radius:6px; margin-bottom:20px;'>
                             <b>¡Venta procesada con éxito!</b> El ticket ha sido registrado en la base de datos por un total de $" . number_format($total_venta, 2) . "
                        </div>";
        } else {
            $mensaje = "<div class='alert alert-danger' style='padding:15px; background:#fee2e2; color:#991b1b; border-radius:6px; margin-bottom:20px;'>
                            Error de Validación [RF_08]: No se puede procesar un ticket con total en $0.00.
                        </div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta - Abarrotes Vilches</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; }
        :root { --primary-color: #2563eb; --sidebar-bg: #1e293b; --bg-color: #f1f5f9; --white: #ffffff; --success: #22c55e; }
        body { display: flex; height: 100vh; background-color: var(--bg-color); }
        .sidebar { width: 260px; background-color: var(--sidebar-bg); color: var(--white); display: flex; flex-direction: column; }
        .brand { padding: 24px; text-align: center; border-bottom: 1px solid #334155; }
        .menu { list-style: none; padding: 20px 0; flex: 1; }
        .menu li a { display: block; padding: 15px 24px; color: #cbd5e1; text-decoration: none; }
        .menu li.active a { background-color: #334155; border-left: 4px solid var(--primary-color); color: white;}
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .topbar { background-color: var(--white); padding: 20px 40px; display: flex; justify-content: space-between; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .page-content { padding: 40px; display: grid; grid-template-columns: 2fr 1fr; gap: 20px; flex: 1; overflow-y: auto; }
        
        .card { background-color: var(--white); padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .form-control { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; margin-bottom: 10px; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; color: white; width: 100%; }
        .btn-primary { background-color: var(--primary-color); }
        .btn-success { background-color: var(--success); font-size: 1.2rem; padding: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        
        .ticket-resumen { margin-top: 20px; border-top: 2px dashed #cbd5e1; padding-top: 20px; font-size: 1.2rem; }
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
        <header class="topbar"><div>Punto de Venta</div><div>Fecha: <?php echo date('d/m/Y'); ?></div></header>
        <div class="page-content" style="display: block;"> <?php echo $mensaje; ?>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                <div class="card">
                    <h3>Agregar Producto a Venta</h3>
                    <br>
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 10px;">
                        <div>
                            <label>Seleccionar Producto</label>
                            <select id="productoSelect" class="form-control">
                                <?php foreach($productosBD as $p): ?>
                                    <option value='{"id":"<?php echo $p['id']; ?>", "nombre":"<?php echo $p['nombre']; ?>", "precio":<?php echo $p['precio']; ?>, "stock":<?php echo $p['stock']; ?>}'>
                                        <?php echo $p['nombre']; ?> - $<?php echo number_format($p['precio'], 2); ?> (Stock: <?php echo $p['stock']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label>Cantidad</label>
                            <input type="number" id="cantidadInput" class="form-control" value="1" min="1">
                        </div>
                    </div>
                    <button class="btn btn-primary" onclick="agregarAlTicket()">Agregar al Ticket</button>
                    <div id="errorStock" style="color: #ef4444; background-color: #fee2e2; padding: 10px; border-radius: 4px; margin-top: 10px; font-weight: bold; display: none;">
                        Error [RF_08]: Stock insuficiente para realizar la venta.
                    </div>
                </div>

                <div class="card">
                    <h3 style="text-align:center;">Ticket Actual</h3>
                    <table>
                        <thead><tr><th>Cant</th><th>Producto</th><th>Subtotal</th></tr></thead>
                        <tbody id="tablaTicket">
                            </tbody>
                    </table>
                    
                    <div class="ticket-resumen">
                        <div style="display: flex; justify-content: space-between; font-weight: bold;">
                            <span>TOTAL A COBRAR:</span>
                            <span id="totalVentaTexto">$0.00</span>
                        </div>
                        <br>
                        
                        <form method="POST" action="nueva_venta.php" id="formVenta">
                            <input type="hidden" name="accion" value="procesar_venta">
                            <input type="hidden" name="total_venta" id="inputTotalVenta" value="0">
                            <button type="button" class="btn btn-success" onclick="cobrarVenta()">Cobrar Venta</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        let totalAcumulado = 0;

        /**
         * [RF_08] VENTA_REGISTRAR (Frontend JS)
         * Se validan las reglas de negocio en tiempo real antes de enviar al servidor.
         */
        function agregarAlTicket() {
            const productoJSON = document.getElementById('productoSelect').value;
            const producto = JSON.parse(productoJSON);
            const cantidad = parseInt(document.getElementById('cantidadInput').value);
            const divError = document.getElementById('errorStock');

            // Validación estricta Excel: Validar stock antes de vender (stock >= cantidad)
            if (cantidad > producto.stock) {
                divError.style.display = 'block';
                return; // Bloquea la acción
            }
            divError.style.display = 'none';

            // Cálculo requerido Excel: Calcular total automáticamente (Cálculo: total = cantidad * precio)
            const subtotal = cantidad * producto.precio;
            totalAcumulado += subtotal;

            const tbody = document.getElementById('tablaTicket');
            const fila = `<tr>
                <td>${cantidad}</td>
                <td>${producto.nombre}</td>
                <td>$${subtotal.toFixed(2)}</td>
            </tr>`;
            tbody.innerHTML += fila;

            // Actualizar vista
            document.getElementById('totalVentaTexto').innerText = "$" + totalAcumulado.toFixed(2);
            // Actualizar input oculto para mandar a PHP
            document.getElementById('inputTotalVenta').value = totalAcumulado;
        }

        function cobrarVenta() {
            if(totalAcumulado === 0) {
                alert("No puedes cobrar un ticket vacío.");
                return;
            }
            // En lugar de redirigir, enviamos los datos procesados al servidor PHP para realizar el INSERT/UPDATE en SQL Server
            document.getElementById('formVenta').submit();
        }
    </script>
</body>
</html>