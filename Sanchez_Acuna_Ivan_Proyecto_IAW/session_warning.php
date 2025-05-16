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
