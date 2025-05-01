<?php
// Inicia la sesión para poder destruirla después
session_start();

// Limpia todas las variables de sesión
$_SESSION = array(); 

// Destruye la sesión actual
session_destroy();

// Elimina la cookie de sesión en el navegador del usuario (opcional, pero recomendable)
setcookie(session_name(), '', time() - 42000, '/'); 

// Redirige al usuario a la página de inicio de sesión después de cerrar sesión
header("Location: login.php");
exit();
?>
