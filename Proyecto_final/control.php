<?php
// Verifica si la sesión no ha sido iniciada, en cuyo caso la inicia
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define el tiempo máximo de inactividad en segundos (30 minutos)
$max_inactivity = 10; 

// Si la sesión no tiene registrado el tiempo de inicio, lo establece en el tiempo actual
if (!isset($_SESSION['session_start_time'])) {
    $_SESSION['session_start_time'] = time();
}

// Verifica si el usuario está autenticado y tiene el rol de "simple"
if (isset($_SESSION['user_id'], $_SESSION['user_role']) && $_SESSION['user_role'] == 'simple') {
    
    // Comprueba si el usuario ha estado inactivo por más tiempo del límite permitido
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $max_inactivity)) {
        // Si la sesión ha expirado, se eliminan las variables de sesión y se destruye la sesión
        session_unset();
        session_destroy();
        
        // Redirige al usuario a la página de inicio de sesión con un mensaje de error
        header("Location: login.php?error=Sesión expirada. Inicia sesión nuevamente.");
        exit();
    }

    // Si la sesión sigue activa, actualiza el tiempo de la última actividad
    $_SESSION['last_activity'] = time();
}

// Calcula el tiempo transcurrido desde que se inició la sesión
$tiempo_transcurrido = time() - $_SESSION['session_start_time'];
$horas = floor($tiempo_transcurrido / 3600);
$minutos = floor(($tiempo_transcurrido % 3600) / 60);
$segundos = $tiempo_transcurrido % 60;
?>


