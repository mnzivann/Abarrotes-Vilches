<?php
session_start();
require_once 'models/Auth.php';

// Si el usuario ya tiene sesión, lo mandamos directo al sistema
if (isset($_SESSION['rol'])) {
    header("Location: index.php");
    exit();
}

$authModel = new Auth();
$error = "";

// ============================================================================
// PROCESAMIENTO DE LOGIN (POST)
// ============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']); 

    if (!empty($usuario) && !empty($password)) {
        try {
            // Le pedimos al modelo que busque al usuario
            $empleado = $authModel->buscarUsuario($usuario);

            if ($empleado) {
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
                        $error = "Contraseña incorrecta.";
                    }
                } else {
                    $error = "Acceso denegado. El estatus de esta cuenta es: '$estatusBD'";
                }
            } else {
                $error = "El usuario '$usuario' no fue encontrado en la base de datos.";
            }
        } catch(PDOException $e) {
            $error = "Error al conectar con SQL Server: " . $e->getMessage();
        }
    } else {
        $error = "Por favor, ingresa tu usuario y contraseña.";
    }
}

// ============================================================================
// RENDERIZADO DE VISTA
// ============================================================================
require_once 'views/login_view.php';
?>