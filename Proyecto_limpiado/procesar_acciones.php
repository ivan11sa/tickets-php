<?php
// Inicia la sesión para gestionar la autenticación del usuario
session_start();

// Incluye el archivo de conexión a la base de datos
require 'db_connection.php';

// Verifica si el usuario es administrador, de lo contrario, deniega el acceso
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: listar_incidencias_admin.php?error=Acceso denegado");
    exit();
}

// Verifica si se ha recibido una acción y una lista de incidencias seleccionadas
if (!isset($_POST['accion']) || !isset($_POST['ids']) || empty($_POST['ids'])) {
    header("Location: listar_incidencias_admin.php?error=No se seleccionaron incidencias.");
    exit();
}

// Obtiene la acción seleccionada (eliminar o editar) y los IDs de las incidencias
$accion = $_POST['accion'];
$ids = $_POST['ids'];

// Convierte los valores recibidos en un array de enteros para evitar problemas de seguridad
$ids = array_map('intval', $ids);

// Verifica si el usuario hizo clic en "Eliminar"
if ($_POST['accion'] === 'eliminar') {

    // Verifica si el usuario seleccionó al menos una incidencia para eliminar
    if (!isset($_POST['ids']) || !is_array($_POST['ids']) || empty($_POST['ids'])) {
        // Si no seleccionó ninguna, lo redirigimos con un mensaje de error
        header("Location: listar_incidencias_admin.php?error=No se seleccionaron incidencias para eliminar.");
        exit();
    }

    // Guardamos los IDs seleccionados en la sesión para confirmación en la siguiente página
    $_SESSION['ids_a_eliminar'] = $_POST['ids'];

    // Redirigir a la página de confirmación para que el usuario decida si quiere eliminar
    header("Location: confirmar_eliminacion.php");
    exit();
} elseif ($accion === "editar") {
    // Verifica que solo se haya seleccionado una incidencia para editar
    if (count($ids) !== 1) {
        header("Location: listar_incidencias_admin.php?error=Selecciona una única incidencia para editar.");
        exit();
    }

    // Redirige a la página de edición de la incidencia seleccionada
    header("Location: editar_incidencia.php?id=" . $ids[0]);
    exit();
}

// Si la acción no es válida, redirige con un mensaje de error
header("Location: listar_incidencias_admin.php?error=Acción no válida.");
exit();
?>