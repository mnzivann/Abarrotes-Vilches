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
        <div class="page-content">
            
            <div class="card">
                <h3>Agregar Producto a Venta</h3>
                <br>
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 10px;">
                    <div>
                        <label>Seleccionar Producto (Simulado)</label>
                        <select id="productoSelect" class="form-control">
                            <option value='{"id":"PRD-001", "nombre":"Aceite Nutrioli", "precio":45.00, "stock":24}'>Aceite Nutrioli - $45.00 (Stock: 24)</option>
                            <option value='{"id":"PRD-002", "nombre":"Frijol La Sierra", "precio":18.50, "stock":3}'>Frijol La Sierra - $18.50 (Stock: 3)</option>
                        </select>
                    </div>
                    <div>
                        <label>Cantidad</label>
                        <input type="number" id="cantidadInput" class="form-control" value="1" min="1">
                    </div>
                </div>
                <button class="btn btn-primary" onclick="agregarAlTicket()">Agregar al Ticket</button>
                <div id="errorStock" style="color: red; margin-top: 10px; font-weight: bold; display: none;">Error: Stock insuficiente para realizar la venta.</div>
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
                        <span id="totalVenta">$0.00</span>
                    </div>
                    <br>
                    <form method="POST" action="ventas.php" id="formVenta">
                        <button type="button" class="btn btn-success" onclick="cobrarVenta()">Cobrar Venta</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        let totalAcumulado = 0;

        function agregarAlTicket() {
            // 1. Obtener datos capturados
            const productoJSON = document.getElementById('productoSelect').value;
            const producto = JSON.parse(productoJSON);
            const cantidad = parseInt(document.getElementById('cantidadInput').value);
            const divError = document.getElementById('errorStock');

            // 2. [RF_08] Validar stock antes de vender (stock >= cantidad)
            if (cantidad > producto.stock) {
                divError.style.display = 'block';
                return; // Detiene la ejecución, no agrega al ticket
            }
            divError.style.display = 'none';

            // 3. [RF_08] Calcular total automáticamente (cantidad * precio)
            const subtotal = cantidad * producto.precio;
            totalAcumulado += subtotal;

            // 4. Actualizar la interfaz visual
            const tbody = document.getElementById('tablaTicket');
            const fila = `<tr>
                <td>${cantidad}</td>
                <td>${producto.nombre}</td>
                <td>$${subtotal.toFixed(2)}</td>
            </tr>`;
            tbody.innerHTML += fila;

            document.getElementById('totalVenta').innerText = "$" + totalAcumulado.toFixed(2);
        }

        function cobrarVenta() {
            if(totalAcumulado === 0) {
                alert("El ticket está vacío.");
                return;
            }
            alert("Venta procesada con éxito por $" + totalAcumulado.toFixed(2));
            // En el código real, aquí se envía el formulario al servidor para guardar en SQL
            window.location.href = "ventas.php";
        }
    </script>
</body>
</html>