<?php
// 1) Buffer para evitar salidas antes de headers
if (!headers_sent()) {
    ob_start();
}

// 2) Iniciar sesión si no existe
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3) Definir tiempos
$max_inactivity = 1800;      // 30 minutos en segundos
$alert_time     = $max_inactivity - 120;  // 28 minutos en segundos

// 4) Inicializar marcas de tiempo
if (!isset($_SESSION['session_start_time'])) {
    $_SESSION['session_start_time'] = time();
}
if (!isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
}

// 5) Calcular período de inactividad
$inactive = time() - $_SESSION['last_activity'];

// 6) Comprobar expiración total (30 min)
//    Solo para usuarios no-admin (is_admin == 0)
if (isset($_SESSION['user_id'], $_SESSION['is_admin']) && $_SESSION['is_admin'] == 0) {
    if ($inactive > $max_inactivity) {
        // 6.1) Destruir sesión
        session_unset();
        session_destroy();
        setcookie(session_name(), '', time() - 42000, '/');
        session_write_close();
        session_regenerate_id(true);

        // 6.2) Redirigir por expiración
        header('Location: login.php?error=Tu sesión ha expirado por inactividad.');
        exit();
    }
    // 6.3) Actualizar último momento de actividad
    $_SESSION['last_activity'] = time();
}

// 7) Flag para mostrar advertencia cuando >= 28 min
$show_warning = ($inactive >= $alert_time);
$_SESSION['show_warning'] = $show_warning;

// 8) Calcular tiempo transcurrido desde login
$elapsed = time() - $_SESSION['session_start_time'];
$_SESSION['horas']   = floor($elapsed / 3600);
$_SESSION['minutos'] = floor(($elapsed % 3600) / 60);
$_SESSION['segundos'] = $elapsed % 60;

// 9) Enviar buffer si estaba abierto
if (ob_get_level()) {
    ob_end_flush();
}
?>
