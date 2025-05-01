<?php
// Este archivo gestiona el cierre de sesión y la eliminación de cookies de sesión.

session_start(); // Iniciar sesión si no está iniciada.

// Vacía completamente el array $_SESSION.
$_SESSION = [];

// Destruir la sesión completamente en el servidor.
session_destroy();

// Eliminar la cookie de sesión en el navegador del usuario.
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/', '', false, true);
}

// Regenerar un nuevo ID de sesión en la próxima autenticación para evitar que al retroceder en la página se vuelva a entrar en la sesion.
session_regenerate_id(true);

// Redirigir al usuario a la página de inicio de sesión después de cerrar sesión.
header("Location: login.php?success=Cierre de sesión exitoso.");
exit();
?>
