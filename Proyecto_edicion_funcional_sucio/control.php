<?php
// Verifica si la sesión no ha sido iniciada y, en ese caso, la inicia
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Definir el tiempo máximo de inactividad para usuarios normales (30 minutos)
$max_inactivity = 1800;  // 30 minutos
$alert_time = $max_inactivity - 120; // Mostrar alerta 2 minutos antes (28 minutos)

// Inicializar el tiempo de inicio de sesión si no está definido
if (!isset($_SESSION['session_start_time'])) {
    $_SESSION['session_start_time'] = time();
}

// Inicializar `last_activity` si no está definido
if (!isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
}

// Verificar si el usuario está autenticado y si es un usuario normal (no administrador)
if (isset($_SESSION['user_id'], $_SESSION['is_admin']) && $_SESSION['is_admin'] == 0) {
    // Verificar la inactividad y cerrar sesión si supera el tiempo límite
    if ((time() - $_SESSION['last_activity']) > $max_inactivity) {
        session_unset();    // Eliminar todas las variables de sesión
        session_destroy();  // Destruir la sesión completamente
        setcookie(session_name(), '', time() - 3600, '/'); // Borrar la cookie de sesión
        session_write_close(); // Asegurar que la sesión se cierra antes de redirigir
        header("Location: login.php?error=Tu sesión ha expirado por inactividad.");
        exit();
    }
    // Actualizar el tiempo de última actividad solo si es usuario normal
    $_SESSION['last_activity'] = time();
}

// Calcula el tiempo transcurrido desde que se inició la sesión
$tiempo_transcurrido = time() - $_SESSION['session_start_time'];
$horas = floor($tiempo_transcurrido / 3600);
$minutos = floor(($tiempo_transcurrido % 3600) / 60);
$segundos = $tiempo_transcurrido % 60;

// Guardar valores de tiempo en variables de sesión para usarlos en dashboard.php
$_SESSION['horas'] = $horas;
$_SESSION['minutos'] = $minutos;
$_SESSION['segundos'] = $segundos;
?>

<!-- Pop-up de advertencia de cierre de sesión para usuarios normales -->
<div class="overlayy" id="sessionWarning" onclick="reiniciarTemporizador()">
    <div class="popupp container">
        <div class="alerta-incidencias">
            <strong>⚠️ ATENCIÓN:</strong> Tu sesión está a punto de expirar por inactividad.
            <p>Si no realizas ninguna acción en los próximos <strong>2 minutos</strong>, se cerrará tu sesión automáticamente.</p>
            <p>Haz clic en cualquier lugar para continuar.</p>
        </div>
    </div>
</div>

<script>
    let inactivityTime = 0;
    const maxInactiveTime = 1800; // 30 minutos
    const alertTime = maxInactiveTime - 120; // Pop-up aparece a los 28 minutos
    let sessionWarning = document.getElementById("sessionWarning");
    let timeout;

    function resetInactivity() {
        inactivityTime = 0;
        sessionWarning.classList.remove("active");
        clearTimeout(timeout);
        timeout = setTimeout(showWarning, alertTime * 1000);
    }

    function showWarning() {
        sessionWarning.classList.add("active");
        setTimeout(() => {
            if (sessionWarning.classList.contains("active")) {
                window.location.href = "login.php?error=Tu sesión ha expirado por inactividad.";
            }
        }, 120000); // Redirigir después de 2 minutos si no hay interacción
    }

    document.addEventListener("mousemove", resetInactivity);
    document.addEventListener("keydown", resetInactivity);
    timeout = setTimeout(showWarning, alertTime * 1000);
</script>

<style>
    /* Estilos del pop-up con tamaño del .container */
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
