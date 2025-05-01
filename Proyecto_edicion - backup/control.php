<?php
// Verifica si la sesión no ha sido iniciada y, en ese caso, la inicia
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Definir el tiempo máximo de inactividad 
$max_inactivity = 1800;  

// Inicializar el tiempo de inicio de sesión si no está definido
if (!isset($_SESSION['session_start_time'])) {
    $_SESSION['session_start_time'] = time();
}

// Inicializar `last_activity` si no está definido
if (!isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
}

// Verificar si el usuario está autenticado
if (isset($_SESSION['user_id'], $_SESSION['is_admin']) && $_SESSION['is_admin'] == 0) {

    // Verificar la inactividad
    if ((time() - $_SESSION['last_activity']) > $max_inactivity) {
        session_unset();    // Eliminar todas las variables de sesión
        session_destroy();  // Destruir la sesión completamente
        setcookie(session_name(), '', time() - 3600, '/'); // Borrar la cookie de sesión
        session_write_close(); // Asegurar que la sesión se cierra antes de redirigir
        header("Location: login.php?error=Tu sesión ha expirado");
        exit();
    }

    // Actualizar el tiempo de última actividad
    $_SESSION['last_activity'] = time();
}

// Calcula el tiempo transcurrido desde que se inició la sesión
$tiempo_transcurrido = time() - $_SESSION['session_start_time'];
$horas = floor($tiempo_transcurrido / 3600);
$minutos = floor(($tiempo_transcurrido % 3600) / 60);
$segundos = $tiempo_transcurrido % 60;


// Recarga la página cada 10 segundos (para pruebas), cambiar a 1800 para producción
header("Refresh: 1800");
?>


