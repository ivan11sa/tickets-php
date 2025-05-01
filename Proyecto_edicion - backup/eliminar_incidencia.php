<?php
// Inicia la sesión para manejar autenticación y permisos
session_start();

// Incluye la conexión a la base de datos
require 'db_connection.php';

// Verifica si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Debes iniciar sesión.");
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['is_admin'] ?? 0;

// Verifica si se han enviado incidencias para eliminar
if (!isset($_POST['ids']) || !is_array($_POST['ids']) || empty($_POST['ids'])) {
    header("Location: listar_incidencias.php?error=No se seleccionaron incidencias para eliminar.");
    exit();
}

$ids = array_map('intval', $_POST['ids']); // Sanitiza los IDs

if (count($ids) > 0) {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    if ($is_admin) {
        // Si es administrador, puede eliminar cualquier incidencia
        $sql = "DELETE FROM INCIDENCIAS WHERE ID_INCIDENCIA IN ($placeholders)";
        $params = $ids;
    } else {
        // Si es usuario normal, solo puede eliminar sus propias incidencias
        $sql = "DELETE FROM INCIDENCIAS WHERE ID_INCIDENCIA IN ($placeholders) AND ID = ?";
        $params = array_merge($ids, [$user_id]);
    }

    $stmt = $conn->prepare($sql);
    $types = str_repeat("i", count($params));
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        header("Location: listar_incidencias.php?success=Incidencias eliminadas correctamente.");
    } else {
        header("Location: listar_incidencias.php?error=Error al eliminar las incidencias.");
    }
    exit();
}
?>
