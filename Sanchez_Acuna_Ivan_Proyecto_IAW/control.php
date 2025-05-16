<?php
if (!headers_sent()) { ob_start(); }

// Este archivo controla la sesión del usuario y gestiona el tiempo de inactividad.
// Si el usuario está inactivo durante más de 30 minutos, su sesión se cerrará automáticamente.
// Este código se pasa a todas las páginas para tener un control sobre la sesión del usuario en todo el momento de la actividad dentro de la página. 

// En esta línea estamos verificando si el usuario ya tiene una sesión iniciada y en caso de no tenerla se inicia. 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//  Definir el tiempo máximo de inactividad (30 minutos).
$max_inactivity = 1800;  // 1800 segundos = 30 minutos

//  Definimos el tiempo en el que va a saltar la alerta avisándonos del tiempo que queda para el cierre de sesión.
$alert_time = $max_inactivity - 120; // 1800 - 120 = 1680 segundos (28 minutos)

//  Guardamos el inicio de sesión en una sesión.
if (!isset($_SESSION['session_start_time'])) {
    $_SESSION['session_start_time'] = time();
}

//  Registramos el último momento de actividad en una sesión. 
if (!isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
}

//  En esta línea verificamos si existe algún usuario que haya iniciado sesión y organizamos según si es administrador o no. 1 para admin y 0 para usuario simple
if (isset($_SESSION['user_id'], $_SESSION['is_admin']) && $_SESSION['is_admin'] == 0) {
    
    //  En esta línea estamos restando el tiempo actual con la sesión de última actividad y comparándola con el tiempo máximo de inactividad. 
    if ((time() - $_SESSION['last_activity']) > $max_inactivity) {
        
        //  Si el usuario ha estado inactivo por más de 30 minutos, cerramos la sesión.
        
        session_unset();    //  Eliminar todas las variables de sesión.
        session_destroy();  //  Destruir la sesión completamente.
        
        setcookie(session_name(), '', time() - 3600, '/'); //  Borrar la cookie de sesión.

        session_write_close(); //  Asegurar que la sesión se cierra antes de redirigir.

        // Regenerar un nuevo ID de sesión en la próxima autenticación para evitar secuestro de sesión. Este es muy importante porque 
        // nos ayuda a cerrar la sesión y no poder recargar la página para volver a entrar en la sesión. Nos aporta seguridad en el momento de cierre de sesión. 
        session_regenerate_id(true);

        //  Redirigir al usuario a la página de inicio de sesión con un mensaje de error.
        header("Location: login.php?error=Tu sesión ha expirado por inactividad.");
        exit();
    }

    //  Si el usuario sigue manejando las funcionalidades de la página, esto sirve para recargar el tiempo activo del mismo.
    $_SESSION['last_activity'] = time();
}

//  Calcular el tiempo total transcurrido desde el inicio de sesión.
$tiempo_transcurrido = time() - $_SESSION['session_start_time'];
$horas = floor($tiempo_transcurrido / 3600);      //  Calcula las horas
$minutos = floor(($tiempo_transcurrido % 3600) / 60); //  Calcula los minutos
$segundos = $tiempo_transcurrido % 60;            //  Calcula los segundos

//  Guardamos estos valores en la sesión para mostrarlos en el resto de pantallas una vez dentro del sistema. ...
$_SESSION['horas'] = $horas;
$_SESSION['minutos'] = $minutos;
$_SESSION['segundos'] = $segundos;
if (ob_get_level()) { ob_end_flush(); }
?>
