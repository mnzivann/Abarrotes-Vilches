<?php
// Iniciamos la memoria de la sesión
session_start();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // [RN_01 y RN_02] Validación simulada con Roles
    if ($usuario === 'admin' && $password === '1234') {
        $_SESSION['usuario'] = 'Jorge'; // O el nombre del admin
        $_SESSION['rol'] = 'Administrador';
        header("Location: index.php");
        exit();
    } else if ($usuario === 'empleado' && $password === '1234') {
        $_SESSION['usuario'] = 'Hazziel'; // O el nombre del cajero/almacenista
        $_SESSION['rol'] = 'Empleado';
        header("Location: index.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Abarrotes Vilches</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; }
        body { display: flex; height: 100vh; background-color: #f1f5f9; justify-content: center; align-items: center; }
        .login-card { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .login-card h2 { margin-bottom: 20px; color: #0f172a; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; margin-bottom: 20px; outline: none; }
        .btn-primary { background-color: #2563eb; color: white; padding: 12px; width: 100%; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 1rem; }
        .error-msg { color: #ef4444; margin-bottom: 15px; font-weight: bold; }
        .hint { margin-top: 20px; font-size: 0.85rem; color: #64748b; text-align: left; background: #f8fafc; padding: 10px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Abarrotes Vilches</h2>
        <p style="margin-bottom: 20px; color: #64748b;">Ingresa tus credenciales para continuar</p>
        
        <?php if($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="text" name="usuario" class="form-control" placeholder="Usuario (admin o empleado)" required>
            <input type="password" name="password" class="form-control" placeholder="Contraseña (1234)" required>
            <button type="submit" class="btn-primary">Iniciar Sesión</button>
        </form>

        <div class="hint">
            <strong>Usuarios de prueba:</strong><br>
            Admin: <code>admin</code> / <code>1234</code><br>
            Empleado: <code>empleado</code> / <code>1234</code>
        </div>
    </div>
</body>
</html>