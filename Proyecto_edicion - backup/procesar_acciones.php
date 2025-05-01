<?php
// Inicia la sesión
session_start();

// Conectar a la base de datos
require 'db_connection.php';

// Verifica si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Debes iniciar sesión.");
    exit();
}

// Identifica al usuario y si es administrador
$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['is_admin'] ?? 0; // Si no está definido, es 0 (usuario normal)

// Verifica si se ha recibido una acción y un origen
if (!isset($_POST['accion']) || !isset($_POST['origen'])) {
    $redirect_url = $_POST['origen'] ?? "listar_incidencias.php";
    header("Location: $redirect_url?error=Acción no válida.");
    exit();
}

$accion = $_POST['accion'];
$origen = $_POST['origen'];
$redirect_url = ($origen === "listar_incidencias_admin.php") ? "listar_incidencias_admin.php" : "listar_incidencias.php";

// -------------------- MODIFICAR DESCRIPCIÓN (Paso 1) --------------------
if ($accion === 'modificar_descripcion') {
    // Solo se puede modificar la descripción desde listar_incidencias.php
    if ($origen !== "listar_incidencias.php") {
        header("Location: $redirect_url?error=No tienes permisos para modificar la descripción.");
        exit();
    }

    // Verificar que se hayan seleccionado incidencias
    if (!isset($_POST['ids']) || empty($_POST['ids'])) {
        header("Location: listar_incidencias.php?error=No se seleccionaron incidencias.");
        exit();
    }

    // Guardar los IDs en sesión para pasarlos a `modificar_descripcion.php`
    $_SESSION['ids_a_modificar'] = array_map('intval', $_POST['ids']);

    // Redirigir a modificar_descripcion.php
    header("Location: modificar_descripcion.php");
    exit();
}

// -------------------- GUARDAR DESCRIPCIÓN (Paso 2) --------------------
if ($accion === 'guardar_descripcion') {
    if (!isset($_SESSION['ids_a_modificar']) || empty($_SESSION['ids_a_modificar'])) {
        header("Location: listar_incidencias.php?error=No hay incidencias para modificar.");
        exit();
    }

    // Verifica que el formulario haya enviado nuevas descripciones
    if (!isset($_POST['nueva_descripcion']) || !is_array($_POST['nueva_descripcion'])) {
        header("Location: listar_incidencias.php?error=No se proporcionaron descripciones.");
        exit();
    }

    // Recorre todas las incidencias enviadas en el formulario y actualiza la descripción
    foreach ($_POST['nueva_descripcion'] as $id_incidencia => $nueva_descripcion) {
        $nueva_descripcion = trim($nueva_descripcion);

        $sql = "UPDATE INCIDENCIAS SET DESCRIPCION = ? WHERE ID_INCIDENCIA = ? AND ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $nueva_descripcion, $id_incidencia, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    // Limpiar la sesión después de modificar
    unset($_SESSION['ids_a_modificar']);

    header("Location: listar_incidencias.php?success=Descripción actualizada.");
    exit();
}

// -------------------- ELIMINAR INCIDENCIA(S) --------------------
elseif ($accion === 'eliminar') {
    if (!isset($_POST['ids']) || empty($_POST['ids'])) {
        header("Location: $redirect_url?error=No se seleccionaron incidencias.");
        exit();
    }

    $ids = array_map('intval', $_POST['ids']);
    $_SESSION['ids_a_eliminar'] = $ids;
    
    header("Location: eliminar.php");
    exit();
}

// -------------------- EDITAR INCIDENCIA --------------------
elseif ($accion === 'editar') {
    if (!isset($_POST['ids']) || count($_POST['ids']) !== 1) {
        header("Location: $redirect_url?error=Selecciona una única incidencia para editar.");
        exit();
    }

    $id_incidencia = intval($_POST['ids'][0]);

    // Verificar si el usuario tiene permiso para editar la incidencia
    if (!$is_admin) {
        $stmt = $conn->prepare("SELECT ID_INCIDENCIA FROM INCIDENCIAS WHERE ID_INCIDENCIA = ? AND ID = ?");
        $stmt->bind_param("ii", $id_incidencia, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            header("Location: $redirect_url?error=No tienes permiso para editar esta incidencia.");
            exit();
        }
    }

    header("Location: editar_incidencia.php?id=$id_incidencia&origen=$redirect_url");
    exit();
}

// Si la acción no es válida
header("Location: $redirect_url?error=Acción no válida.");
exit();
?>
