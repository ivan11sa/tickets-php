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
$origen = $_POST['origen'] ?? "listar_incidencias.php";

// Verifica si se han enviado incidencias para eliminar
if (!isset($_POST['ids']) || !is_array($_POST['ids']) || empty($_POST['ids'])) {
    header("Location: $origen?error=No se seleccionaron incidencias para eliminar.");
    exit();
}

error_log("Antes de eliminar, IDs recibidos: " . print_r($ids, true));

$ids = array_map('intval', $ids);

error_log("Después de convertir a enteros, IDs eliminados: " . print_r($ids, true));

if (count($ids) > 0) {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    if ($is_admin || $origen === "listar_incidencias_admin.php") {
        // Si es administrador y está en listar_incidencias_admin.php, puede eliminar cualquier incidencia
        $sql = "DELETE FROM INCIDENCIAS WHERE ID_INCIDENCIA IN ($placeholders)";
        $params = $ids;
    } else {
        // Si es usuario normal o un admin en listar_incidencias.php, solo puede eliminar sus incidencias
        $sql = "DELETE FROM INCIDENCIAS WHERE ID_INCIDENCIA IN ($placeholders) AND ID = ?";
        $params = array_merge($ids, [$user_id]);
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Error en la consulta SQL: " . $conn->error);
        header("Location: $origen?error=Error al preparar la consulta.");
        exit();
    }

    $types = str_repeat("i", count($params));
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        header("Location: $origen?success=Incidencias eliminadas correctamente.");
    } else {
        error_log("Error en la eliminación: " . $stmt->error);
        header("Location: $origen?error=Error al eliminar las incidencias.");
    }
    exit();
}
?>

