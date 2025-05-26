<?php
ob_start();
// Este archivo maneja la modificación, eliminación y edición de incidencias en la base de datos.

// Iniciar la sesión si aún no está iniciada.
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Iniciamos la sesión para poder usar variables de sesión.
}

// Incluir la conexión a la base de datos.
require 'db_connection.php'; // Archivo que contiene la conexión a la base de datos.

// Verificar si el usuario ha iniciado sesión, si no, redirigir al login.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Debes iniciar sesión."); // Si no está logueado, lo mandamos al login con un error.
    exit(); // Detenemos la ejecución del script para evitar que continúe.
}

// Obtener datos de sesión del usuario.
$user_id = intval($_SESSION['user_id']); // Convertimos el ID del usuario en número entero por seguridad.
$is_admin = isset($_SESSION['is_admin']) ? intval($_SESSION['is_admin']) : 0; // Verificamos si el usuario tiene permisos de administrador.

// Verificar si se recibió una acción y un origen en la solicitud.
if (!isset($_POST['accion']) || !isset($_POST['origen'])) {
    header("Location: listar_incidencias.php?error=Acción no válida."); // Si no se envía una acción válida, redirigimos con un error.
    exit();
}

// Guardamos los valores recibidos en variables.
$accion = $_POST['accion']; // Acción que el usuario intenta realizar.
$origen = $_POST['origen']; // Página desde la que se envió la solicitud.
$redirect_url = ($origen === "listar_incidencias_admin.php") ? "listar_incidencias_admin.php" : "listar_incidencias.php"; // URL a la que redirigir dependiendo del origen.

// MODIFICAR DESCRIPCIÓN (Paso 1)
if ($accion === 'modificar_descripcion') {
    if (!isset($_POST['ids']) || empty($_POST['ids'])) {
        header("Location: listar_incidencias.php?error=No se seleccionaron incidencias."); // Verificamos que se hayan seleccionado incidencias.
        exit();
    }

    // Guardar los IDs en sesión para ser usados posteriormente.
    $_SESSION['ids_a_modificar'] = array_map('intval', $_POST['ids']); // Convertimos los IDs en enteros por seguridad.
    header("Location: modificar_descripcion.php"); // Redirigimos a la página de modificación de descripción.
    exit();
}

// GUARDAR DESCRIPCIÓN (Paso 2)
if ($accion === 'guardar_descripcion') {
    if (!isset($_SESSION['ids_a_modificar']) || empty($_SESSION['ids_a_modificar'])) {
        header("Location: listar_incidencias.php?error=No hay incidencias para modificar."); // Si no hay incidencias en sesión, error.
        exit();
    }

    if (!isset($_POST['nueva_descripcion']) || !is_array($_POST['nueva_descripcion'])) {
        header("Location: listar_incidencias.php?error=No se proporcionaron descripciones."); // Si no hay descripciones, error.
        exit();
    }

    // Recorremos cada incidencia seleccionada para actualizar su descripción en la base de datos.
    foreach ($_POST['nueva_descripcion'] as $id_incidencia => $nueva_descripcion) {
        $nueva_descripcion = trim($nueva_descripcion); // Eliminamos espacios en blanco adicionales.
        $id_incidencia = intval($id_incidencia); // Convertimos el ID en entero por seguridad.

        // Consulta para actualizar la descripción de la incidencia.
        $sql = "UPDATE INCIDENCIAS SET DESCRIPCION = ? WHERE ID_INCIDENCIA = ? AND ID = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sii", $nueva_descripcion, $id_incidencia, $user_id);
            $stmt->execute();
            $stmt->close();
        } else {
            error_log("Error en la consulta de actualización: " . $conn->error); // Guardamos en el log si hay un error.
        }
    }

    unset($_SESSION['ids_a_modificar']); // Limpiamos la sesión.
    header("Location: listar_incidencias.php?success=Descripción actualizada."); // Redirigimos con éxito.
    exit();
}

// ELIMINAR INCIDENCIA(S)
elseif ($accion === 'eliminar') {
    if (!isset($_POST['ids']) || empty($_POST['ids'])) {
        header("Location: $redirect_url?error=No se seleccionaron incidencias."); // Verificamos que haya incidencias seleccionadas.
        exit();
    }

    // Guardamos los IDs en sesión para eliminarlos después.
    $_SESSION['ids_a_eliminar'] = array_map('intval', $_POST['ids']);
    $_SESSION['origen'] = $origen;

    // Registro en logs para depuración.
    error_log("Acción recibida: eliminar");
    error_log("IDs almacenados en sesión: " . print_r($_SESSION['ids_a_eliminar'], true));

    $delete_url = $is_admin ? "eliminar.php" : "eliminar.php"; // Determinamos la URL según el rol.
    header("Location: $delete_url"); // Redirigimos a la página de eliminación.
    exit();
}

// EDITAR INCIDENCIA
elseif ($accion === 'editar') {
    if (!isset($_POST['ids']) || count($_POST['ids']) !== 1) {
        header("Location: $redirect_url?error=Selecciona una única incidencia para editar."); // Solo se puede editar una a la vez.
        exit();
    }

    $id_incidencia = intval($_POST['ids'][0]);

    // Si el usuario no es administrador, validamos si tiene permiso para editar la incidencia.
    if (!$is_admin) {
        $stmt = $conn->prepare("SELECT ID_INCIDENCIA FROM INCIDENCIAS WHERE ID_INCIDENCIA = ? AND ID = ?");
        if ($stmt) {
            $stmt->bind_param("ii", $id_incidencia, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                header("Location: $redirect_url?error=No tienes permiso para editar esta incidencia.");
                exit();
            }

            $stmt->close();
        } else {
            header("Location: $redirect_url?error=Error en la validación de permisos.");
            exit();
        }
    }

    header("Location: editar_incidencia.php?id=$id_incidencia&origen=$redirect_url"); // Redirigimos a la página de edición.
    exit();
}

// Si la acción no es válida, redirigimos con un mensaje de error.
header("Location: $redirect_url?error=Acción no válida.");
exit();
?>
