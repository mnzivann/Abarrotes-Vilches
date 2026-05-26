<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta - Abarrotes Vilches</title>
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
        
        /* Botones y Formularios */
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; transition: 0.2s; }
        .btn-primary { background-color: var(--primary-color); color: var(--white); }
        .btn-primary:hover { background-color: #1d4ed8; }
        .btn-success { background-color: var(--success); color: var(--white); }
        .btn-success:hover { background-color: #16a34a; }
        .btn-delete { background-color: var(--danger); color: var(--white); padding: 6px 12px; font-size: 0.85rem; border-radius: 4px; }
        .form-control { padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1rem; outline: none; width: 100%; transition: 0.2s; }
        .form-control:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        .qty-input { width: 60px; padding: 6px; border: 1px solid #cbd5e1; border-radius: 4px; text-align: center; }

        /* Estructura dividida del Punto de Venta */
        .pos-container { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start; }
        .card { background-color: var(--white); border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; padding: 24px; }
        
        /* Buscador de productos */
        .search-section { margin-bottom: 20px; display: flex; gap: 10px; }
        
        /* Tablas */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead { background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; }
        th, td { padding: 12px 16px; text-align: left; }
        th { color: var(--text-light); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; }
        td { border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }
        tbody tr:hover { background-color: #f8fafc; }

        /* Panel de Cobro (Ticket) */
        .ticket-panel { background-color: #f8fafc; border: 1px solid #e2e8f0; display: flex; flex-direction: column; height: 100%; }
        .ticket-header { border-bottom: 2px dashed #cbd5e1; padding-bottom: 15px; margin-bottom: 15px; font-weight: 600; text-align: center; }
        .ticket-items { min-height: 200px; flex: 1; }
        .ticket-summary { border-top: 2px dashed #cbd5e1; padding-top: 20px; margin-top: 20px; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; color: var(--text-light); font-size: 0.95rem; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 1.5rem; font-weight: 700; color: var(--text-dark); }
        .btn-cobrar { width: 100%; padding: 16px; font-size: 1.1rem; letter-spacing: 0.5px; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="brand"><h2>Abarrotes Vilches</h2><span>Control de Sistema</span></div>
        <ul class="menu">
            <li><a href="index.html">Productos</a></li>
            <li><a href="inventario.html">Inventario</a></li>
            <li class="active"><a href="ventas.html">Ventas</a></li>
            <li><a href="mermas.html">Mermas</a></li>
            <li><a href="empleados.html">Empleados</a></li>
            <li><a href="reportes.html">Reportes</a></li>
        </ul>
        <div class="user-profile"><p>Empleado / Cajero</p></div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div>Módulo de Ventas - Nueva Transacción</div>
            <div>Fecha: 18 de Mayo, 2026</div>
        </header>

        <div class="page-content">
            <div class="page-header">
                <h1>Punto de Venta</h1>
                <a href="ventas.html" class="btn" style="background-color: #e2e8f0; color: #0f172a; text-decoration: none;">Volver al Historial</a>
            </div>

            <div class="pos-container">
                <div class="card">
                    <div class="search-section">
                        <input type="text" class="form-control" placeholder="Escanear código de barras o buscar producto por nombre...">
                        <button class="btn btn-primary">Buscar</button>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>Stock</th>
                                <th>Precio</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>7501001</td>
                                <td>Aceite Nutrioli 946 ml</td>
                                <td>24</td>
                                <td>$45.00</td>
                                <td><button class="btn btn-primary" style="padding: 6px 12px; font-size: 0.85rem;">Agregar</button></td>
                            </tr>
                            <tr>
                                <td>7501002</td>
                                <td>Leche Lala Entera 1L</td>
                                <td>12</td>
                                <td>$28.00</td>
                                <td><button class="btn btn-primary" style="padding: 6px 12px; font-size: 0.85rem;">Agregar</button></td>
                            </tr>
                            <tr>
                                <td>7501003</td>
                                <td>Sabritas Sal 40g</td>
                                <td>32</td>
                                <td>$16.00</td>
                                <td><button class="btn btn-primary" style="padding: 6px 12px; font-size: 0.85rem;">Agregar</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="card ticket-panel">
                    <div class="ticket-header">
                        Detalle de Venta actual
                    </div>
                    
                    <div class="ticket-items">
                        <table style="margin-top: 0;">
                            <thead>
                                <tr>
                                    <th>Cant.</th>
                                    <th>Producto</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="number" class="qty-input" value="2" min="1"></td>
                                    <td style="font-size: 0.9rem;">Sabritas Sal 40g</td>
                                    <td style="font-weight: 600;">$32.00</td>
                                    <td><button class="btn btn-delete">X</button></td>
                                </tr>
                                <tr>
                                    <td><input type="number" class="qty-input" value="1" min="1"></td>
                                    <td style="font-size: 0.9rem;">Leche Lala Entera 1L</td>
                                    <td style="font-weight: 600;">$28.00</td>
                                    <td><button class="btn btn-delete">X</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="ticket-summary">
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>$60.00</span>
                        </div>
                        <div class="summary-row">
                            <span>Artículos:</span>
                            <span>3</span>
                        </div>
                        <div class="total-row">
                            <span>TOTAL:</span>
                            <span style="color: var(--success);">$60.00</span>
                        </div>
                        <button class="btn btn-success btn-cobrar">Cobrar e Imprimir Ticket</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>