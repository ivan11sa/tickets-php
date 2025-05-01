<?php
// Inicia la sesión para manejar variables de sesión y autenticación del usuario
session_start();

// Incluye el archivo de conexión a la base de datos
require 'db_connection.php';

// Verifica si el usuario ha iniciado sesión y si tiene permisos de administrador
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    // Si el usuario no es administrador, lo redirige a la página de inicio de sesión
    header("Location: login.php");
    exit();
}

// Verifica si se han enviado incidencias para eliminar
if (!isset($_POST['ids']) || !is_array($_POST['ids']) || empty($_POST['ids'])) {
    // Si no se seleccionaron incidencias, redirige con un mensaje de error
    header("Location: listar_incidencias_admin.php?error=No se seleccionaron incidencias para eliminar.");
    exit();
}

// Convierte los valores recibidos en un array de enteros para mayor seguridad
$ids = array_map('intval', $_POST['ids']);

// Si hay incidencias válidas en la lista, procede con la eliminación
if (count($ids) > 0) {

    // Genera una lista de placeholders "?" igual al número de incidencias seleccionadas
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    // Prepara la consulta SQL para eliminar las incidencias seleccionadas
    $sql = "DELETE FROM INCIDENCIAS WHERE ID_INCIDENCIA IN ($placeholders)";
    $stmt = $conn->prepare($sql);

    // Verifica si la consulta se preparó correctamente
    if (!$stmt) {
        error_log("Error en la preparación de la consulta SQL: " . $conn->error);
        header("Location: listar_incidencias_admin.php?error=Error al preparar la consulta.");
        exit();
    }

    // Define el tipo de datos para los parámetros (i = entero) y los vincula a la consulta preparada
    $types = str_repeat("i", count($ids));
    $stmt->bind_param($types, ...$ids);
    
    // Ejecuta la consulta y verifica si la eliminación fue exitosa
    if ($stmt->execute()) {
        // Cierra la consulta y redirige con un mensaje de éxito
        $stmt->close();
        header("Location: listar_incidencias_admin.php?success=Incidencias eliminadas correctamente");
        exit();
    } else {
        // Si hay un error al ejecutar la consulta, se registra en el log y se redirige con un mensaje de error
        error_log("Error en la ejecución de la consulta SQL: " . $stmt->error);
        header("Location: listar_incidencias_admin.php?error=Error al ejecutar la eliminación.");
        exit();
    }

} else {
    // Si no se encontraron incidencias válidas, redirige con un mensaje de error
    header("Location: listar_incidencias_admin.php?error=No se seleccionaron incidencias válidas.");
    exit();
}
?>

