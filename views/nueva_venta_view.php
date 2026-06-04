<?php
/**
 * @var array $productosBD
 * 
 */
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
        .user-profile { padding: 20px; background-color: #0f172a; text-align: center; font-size: 0.9rem; border-top: 1px solid #334155; }
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .topbar { background-color: var(--white); padding: 20px 40px; display: flex; justify-content: space-between; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .page-content { padding: 40px; display: grid; grid-template-columns: 2fr 1fr; gap: 20px; flex: 1; overflow-y: auto; }
        
        .card { background-color: var(--white); padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .form-control { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 4px; margin-bottom: 10px; font-size: 1rem; }
        .form-control:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; color: white; width: 100%; }
        .btn-primary { background-color: var(--primary-color); }
        .btn-success { background-color: var(--success); font-size: 1.2rem; padding: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        
        .ticket-resumen { margin-top: 20px; border-top: 2px dashed #cbd5e1; padding-top: 20px; font-size: 1.2rem; }
        
        .search-container { position: relative; width: 100%; }
        .suggestions-box { 
            position: absolute; top: 100%; left: 0; right: 0; background: white; 
            border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; 
            max-height: 250px; overflow-y: auto; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: none;
        }
        .suggestion-item { padding: 12px; cursor: pointer; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
        .suggestion-item:hover { background-color: #f8fafc; }
        .suggestion-item:last-child { border-bottom: none; }
        .item-name { font-weight: bold; color: #0f172a; }
        .item-meta { font-size: 0.85rem; color: #64748b; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="brand"><h2>Abarrotes Vilches</h2></div>
        <ul class="menu">
            <li><a href="index.php">Productos</a></li>
            <li><a href="categorias.php">Categorías</a></li>
            <li><a href="inventario.php">Inventario</a></li>
            <li class="active"><a href="ventas.php">Ventas</a></li>
            <li><a href="mermas.php">Mermas</a></li>
            
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
                <li><a href="empleados.php">Empleados</a></li>
                <li><a href="reportes.php">Reportes</a></li>
            <?php endif; ?>
        </ul>
        <div class="user-profile">
            <p style="margin-bottom: 10px; color: #cbd5e1;"> <?php echo $_SESSION['usuario'] ?? 'Usuario'; ?></p>
            <a href="logout.php" style="display: block; background-color: #ef4444; color: white; text-decoration: none; padding: 8px; border-radius: 4px; font-weight: bold;">Cerrar Sesión</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div>Punto de Venta</div>
            <div>Fecha: <?php echo date('d/m/Y'); ?></div>
        </header>
        
        <div class="page-content" style="display: block;"> 
            <?php echo $mensaje; ?>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                <div class="card" style="align-self: start;">
                    <h3>Lector / Buscador de Productos</h3>
                    <br>
                    
                    <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 15px;">
                        <div class="search-container">
                            <label style="font-size:0.9rem; font-weight:bold; color:#64748b;">Escanea código o busca por nombre</label>
                            <input type="text" id="inputBuscador" class="form-control" placeholder="Ej. PRD-001 o Nutrioli..." autocomplete="off" autofocus>
                            <div id="cajaSugerencias" class="suggestions-box"></div>
                        </div>

                        <div>
                            <label style="font-size:0.9rem; font-weight:bold; color:#64748b;">Cantidad</label>
                            <input type="number" id="cantidadInput" class="form-control" value="1" min="1">
                        </div>
                    </div>

                    <div id="errorStock" style="color: #ef4444; background-color: #fee2e2; padding: 10px; border-radius: 4px; margin-top: 10px; font-weight: bold; display: none;">
                        Error: Stock insuficiente.
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
                            <input type="hidden" name="lista_articulos" id="inputListaArticulos" value="[]">
                            
                            <button type="button" class="btn btn-success" onclick="cobrarVenta()"> Cobrar Venta</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const catalogoProductos = <?php echo json_encode($productosBD); ?>;
        
        let totalAcumulado = 0;
        let carrito = []; 

        const inputBuscador = document.getElementById('inputBuscador');
        const cajaSugerencias = document.getElementById('cajaSugerencias');
        const inputCantidad = document.getElementById('cantidadInput');

        inputBuscador.addEventListener('keyup', function(e) {
            const query = e.target.value.toLowerCase().trim();

            if (e.key === 'Enter') {
                e.preventDefault(); 
                
                const productoEscaneado = catalogoProductos.find(p => p.id.toLowerCase() === query);
                
                if (productoEscaneado) {
                    procesarAgregarAlTicket(productoEscaneado);
                } else {
                    alert("El código escaneado no existe o no tiene stock.");
                }
                
                limpiarBuscador();
                return;
            }

            if (query.length === 0) {
                cajaSugerencias.style.display = 'none';
                return;
            }

            const resultados = catalogoProductos.filter(p => 
                p.nombre.toLowerCase().includes(query) || p.id.toLowerCase().includes(query)
            );

            cajaSugerencias.innerHTML = '';
            if (resultados.length > 0) {
                resultados.forEach(prod => {
                    const div = document.createElement('div');
                    div.className = 'suggestion-item';
                    div.innerHTML = `
                        <div>
                            <div class="item-name">${prod.nombre}</div>
                            <div class="item-meta">Código: ${prod.id}</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-weight:bold; color:#2563eb;">$${parseFloat(prod.precio).toFixed(2)}</div>
                            <div class="item-meta">Stock: ${prod.stock}</div>
                        </div>
                    `;
                    div.onclick = () => {
                        procesarAgregarAlTicket(prod);
                        limpiarBuscador();
                    };
                    cajaSugerencias.appendChild(div);
                });
                cajaSugerencias.style.display = 'block';
            } else {
                cajaSugerencias.innerHTML = '<div class="suggestion-item" style="color:#ef4444;">No se encontraron resultados</div>';
                cajaSugerencias.style.display = 'block';
            }
        });

        document.addEventListener('click', function(e) {
            if (!inputBuscador.contains(e.target) && !cajaSugerencias.contains(e.target)) {
                cajaSugerencias.style.display = 'none';
            }
        });

        function limpiarBuscador() {
            inputBuscador.value = '';
            inputCantidad.value = 1; 
            cajaSugerencias.style.display = 'none';
            inputBuscador.focus(); 
        }

        function procesarAgregarAlTicket(producto) {
            const cantidad = parseInt(inputCantidad.value);
            const divError = document.getElementById('errorStock');

            if (cantidad > producto.stock) {
                divError.style.display = 'block';
                divError.innerText = `Error: Solo quedan ${producto.stock} piezas de ${producto.nombre}.`;
                return;
            }
            divError.style.display = 'none';

            const subtotal = cantidad * producto.precio;
            totalAcumulado += subtotal;

            const index = carrito.findIndex(p => p.id === producto.id);
            if (index > -1) {
                carrito[index].cantidad += cantidad;
                carrito[index].subtotal += subtotal;
            } else {
                carrito.push({
                    id: producto.id,
                    nombre: producto.nombre,
                    cantidad: cantidad,
                    subtotal: subtotal
                });
            }

            actualizarVistaTicket();
        }

        function actualizarVistaTicket() {
            const tbody = document.getElementById('tablaTicket');
            tbody.innerHTML = '';

            carrito.forEach(item => {
                const fila = `<tr>
                    <td>${item.cantidad}</td>
                    <td>${item.nombre}</td>
                    <td>$${item.subtotal.toFixed(2)}</td>
                </tr>`;
                tbody.innerHTML += fila;
            });

            document.getElementById('totalVentaTexto').innerText = "$" + totalAcumulado.toFixed(2);
            document.getElementById('inputTotalVenta').value = totalAcumulado;
            document.getElementById('inputListaArticulos').value = JSON.stringify(carrito);
        }

        function cobrarVenta() {
            if(totalAcumulado === 0 || carrito.length === 0) {
                alert("No puedes cobrar un ticket vacío.");
                return;
            }
            document.getElementById('formVenta').submit();
        }
    </script>
</body>
</html>