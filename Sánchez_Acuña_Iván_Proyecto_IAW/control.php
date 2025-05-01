<?php
// Este archivo controla la sesión del usuario y gestiona el tiempo de inactividad.
// Si el usuario está inactivo durante más de 30 minutos, su sesión se cerrará automáticamente.
// Este codigo se pasa a todas lsa paginas para tener un control sobre la sesion del usuario en todo el momento de la actividad dentro de la página. 

// En esta línea estamos verificando si el usuario ya tiene una sesión iniciada y en caso de no tenerla se inicia. 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//  Definir el tiempo máximo de inactividad (30 minutos).
$max_inactivity = 1800;  // 1800 segundos = 30 minutos

//  Definimos el tiempo en el que va a saltar la alerta avisandonos del tiempo que queda para el cierre de sesion.
$alert_time = $max_inactivity - 120; // 1800 - 120 = 1680 segundos (28 minutos)

//  Guardamos el inicion de sesion en una session.
if (!isset($_SESSION['session_start_time'])) {
    $_SESSION['session_start_time'] = time();
}

//  Registramos el último momento de actividad en una session. 
if (!isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
}

//  En esta línea verificamos si existe algun usuario que haya iniciado sesion y organizamos segun si es administrador o no. 1 para admin y 0 para usuario simple
if (isset($_SESSION['user_id'], $_SESSION['is_admin']) && $_SESSION['is_admin'] == 0) {
    
    //  En esta linea estámos restando el tiempo actual con la session de ultima actividad y comparandola con el tiempo maximo de inactividad. 
    if ((time() - $_SESSION['last_activity']) > $max_inactivity) {
        
        //  Si el usuario ha estado inactivo por más de 30 minutos, cerramos la sesión.
        
        session_unset();    //  Eliminar todas las variables de sesión.
        session_destroy();  //  Destruir la sesión completamente.
        
        setcookie(session_name(), '', time() - 3600, '/'); //  Borrar la cookie de sesión.

        session_write_close(); //  Asegurar que la sesión se cierra antes de redirigir.

        // Regenerar un nuevo ID de sesión en la próxima autenticación para evitar secuestro de sesión. Este es muy importante porque 
        // nos ayuda a cerrar la sesión y no poder recargar la página para volver a entrar en la sesion. Nos aporta seguridad en el momento de cierre de sesión. 
        session_regenerate_id(true);

        //  Redirigir al usuario a la página de inicio de sesión con un mensaje de error.
        header("Location: login.php?error=Tu sesión ha expirado por inactividad.");
        exit();
    }

    //  Si el usuario sigue manejando las funcionalidades de la página, esto sirve para recargar el tiempo activo del mismo. .
    $_SESSION['last_activity'] = time();
}

//  Calcular el tiempo total transcurrido desde el inicio de sesión.
$tiempo_transcurrido = time() - $_SESSION['session_start_time'];
$horas = floor($tiempo_transcurrido / 3600);      //  Calcula las horas
$minutos = floor(($tiempo_transcurrido % 3600) / 60); //  Calcula los minutos
$segundos = $tiempo_transcurrido % 60;            //  Calcula los segundos

//  Guardamos estos valores en la sesión para mostrarlos en el resto de pantallas una vez dentro del sistema. .
$_SESSION['horas'] = $horas;
$_SESSION['minutos'] = $minutos;
$_SESSION['segundos'] = $segundos;
?>
<!-- Pop-up de advertencia de cierre de sesión -->
<div class="overlayy" id="sessionWarning" onclick="resetInactivity()">
    <div class="popupp container">
        <div class="alerta-incidencias">
            <strong>⚠️ ATENCIÓN:</strong> Tu sesión está a punto de expirar por inactividad.
            <p>Si no realizas ninguna acción en los próximos <strong>2 minutos</strong>, se cerrará tu sesión automáticamente.</p>
            <p>Haz clic en cualquier lugar para continuar.</p>
        </div>
    </div>
</div>
<!-- Las siguientes líneas han sido buscadas por la red para poder definir la señal de alerta antes de que la sesion expire. -->
<script>
    // CONTROL DEL TIEMPO DE INACTIVIDAD DEL USUARIO

    let inactivityTime = 0;  //  Contador de inactividad en segundos
    const maxInactiveTime = 1800; //  30 minutos en segundos
    const alertTime = maxInactiveTime - 120; //  Pop-up aparece a los 28 minutos

    let sessionWarning = document.getElementById("sessionWarning"); //  Captura el pop-up de advertencia
    let timeout; //  Variable para el temporizador

    //  Función para reiniciar el contador de inactividad cuando el usuario interactúa.
    function resetInactivity() {
        inactivityTime = 0; //  Reinicia el contador de inactividad
        sessionWarning.classList.remove("active"); //  Oculta el pop-up si estaba visible
        clearTimeout(timeout); //  Elimina cualquier temporizador anterior
        timeout = setTimeout(showWarning, alertTime * 1000); //  Vuelve a programar la alerta
    }

    //  Función que muestra la advertencia de sesión inactiva.
    function showWarning() {
        sessionWarning.classList.add("active"); //  Muestra el pop-up de advertencia

        //  Si el usuario no interactúa en 2 minutos, cerramos sesión automáticamente.
        setTimeout(() => {
            if (sessionWarning.classList.contains("active")) {
                window.location.href = "login.php?error=Tu sesión ha expirado por inactividad.";
            }
        }, 120000); //  120000ms = 2 minutos
    }

    //  Escuchar eventos del usuario (movimiento del ratón o teclas presionadas)
    document.addEventListener("mousemove", resetInactivity);
    document.addEventListener("keydown", resetInactivity);

    //  Configurar el temporizador inicial para mostrar la advertencia a los 28 minutos
    timeout = setTimeout(showWarning, alertTime * 1000);
</script>
<style>
    .overlayy {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        visibility: hidden;
        opacity: 0;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .overlayy.active {
        visibility: visible;
        opacity: 1;
    }

    .popupp {
        width: 90%;
        max-width: 1200px;
        background: rgba(255, 255, 255, 0.95);
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        text-align: center;
    }

    .alerta-incidencias {
        font-size: 18px;
        color: #333;
    }
</style>
