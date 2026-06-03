<?php
session_start();
require_once 'conexion.php'; 

// Si el usuario ya tiene sesión, lo mandamos directo al sistema
if (isset($_SESSION['rol'])) {
    header("Location: index.php");
    exit();
}

$conn = Conexion::conectar();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']); 

    if (!empty($usuario) && !empty($password)) {
        try {
            // LTRIM y RTRIM en SQL limpian cualquier espacio fantasma directamente en la tabla
            $sql = "SELECT id_empleado, nombre, rol, password, estatus FROM Empleados WHERE LTRIM(RTRIM(usuario)) = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$usuario]);
            
            $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($empleado) {
                // Limpiamos los datos que vienen de la BD
                $estatusBD = trim($empleado['estatus']);
                $passwordBD = trim($empleado['password']);

                if ($estatusBD === 'Activo') {
                    
                    if ($password === $passwordBD) {
                        // TODO CORRECTO: Iniciamos sesión
                        $_SESSION['id_empleado'] = $empleado['id_empleado'];
                        $_SESSION['usuario'] = explode(" ", $empleado['nombre'])[0]; 
                        $_SESSION['rol'] = trim($empleado['rol']); 
                        
                        header("Location: index.php");
                        exit();
                    } else {
                        // ERROR DE CONTRASEÑA ESPECÍFICO
                        $error = "Contraseña incorrecta. Escribiste: '$password', pero la BD tiene: '$passwordBD'";
                    }
                } else {
                    // ERROR DE ESTATUS ESPECÍFICO
                    $error = "Acceso denegado. El estatus de esta cuenta es: '$estatusBD'";
                }
            } else {
                // ERROR DE USUARIO INEXISTENTE
                $error = "El usuario '$usuario' no fue encontrado en la base de datos.";
            }
        } catch(PDOException $e) {
            $error = "Error al conectar con SQL Server: " . $e->getMessage();
        }
    } else {
        $error = "Por favor, ingresa tu usuario y contraseña.";
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
        body { background-color: #f1f5f9; display: flex; justify-content: center; align-items: center; height: 100vh; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; margin: 0; }
        .login-card { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .form-control { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box; outline: none; }
        .form-control:focus { border-color: #2563eb; }
        .btn { width: 100%; padding: 12px; background-color: #2563eb; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; transition: 0.2s; }
        .btn:hover { background-color: #1d4ed8; }
        .alert { background-color: #fee2e2; color: #991b1b; padding: 15px; border-radius: 4px; margin-bottom: 20px; font-size: 0.9rem; border: 1px solid #fecaca; }
        .info { background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 4px; text-align: left; font-size: 0.85rem; color: #475569; margin-top: 20px;}
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Abarrotes Vilches</h2>
        <p style="color: #64748b; margin-bottom: 20px;">Ingresa tus credenciales para continuar</p>
        
        <?php if(!empty($error)): ?>
            <div class="alert"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="usuario" class="form-control" placeholder="Usuario de acceso" required autofocus>
            <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
            <button type="submit" class="btn">Iniciar Sesión</button>
        </form>

        
    </div>
</body>
</html>