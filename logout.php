<?php
session_start();

// Vaciamos todas las variables de sesión
$_SESSION = array();

// Destruimos la sesión en el servidor
session_destroy();

// Redirigimos al usuario a la pantalla de inicio de sesión
header("Location: login.php");
exit();
?>