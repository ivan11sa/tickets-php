<?php
// Iniciar sesión para autenticación
session_start();

// Incluir conexión a la base de datos
require 'db_connection.php';

// Verificar autenticación y permisos
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['is_admin'] ?? 0;

// Si no es administrador, redirigir a la lista de incidencias de usuario
if (!$is_admin) {
    header("Location: listar_incidencias.php?error=No tienes permisos para eliminar estas incidencias.");
    exit();
}

// Recuperar incidencias a eliminar desde la sesión
if (!isset($_SESSION['ids_a_eliminar']) || empty($_SESSION['ids_a_eliminar'])) {
    header("Location: listar_incidencias_admin.php?error=No hay incidencias para eliminar.");
    exit();
}

$ids = $_SESSION['ids_a_eliminar'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["confirmar"])) {
        // Sanitizar los IDs asegurando que sean enteros
        $ids = array_map('intval', $ids);

        // Construir los placeholders dinámicamente
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        // Preparar la consulta SQL con placeholders dinámicos
        $sql = "DELETE FROM INCIDENCIAS WHERE ID_INCIDENCIA IN ($placeholders)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            error_log("Error al preparar la consulta SQL: " . $conn->error);
            header("Location: listar_incidencias_admin.php?error=Error al preparar la consulta.");
            exit();
        }

        // Crear los tipos de parámetros dinámicamente
        $types = str_repeat("i", count($ids));

        // Usar call_user_func_array para asociar correctamente los parámetros con bind_param
        $stmt->bind_param($types, ...$ids);

        // Ejecutar la consulta y verificar si fue exitosa
        if ($stmt->execute()) {
            unset($_SESSION['ids_a_eliminar']);
            header("Location: listar_incidencias_admin.php?success=Incidencias eliminadas correctamente.");
            exit();
        } else {
            error_log("Error al ejecutar la consulta SQL: " . $stmt->error);
            header("Location: listar_incidencias_admin.php?error=Error al eliminar las incidencias.");
            exit();
        }
    } else {
        // Si el usuario cancela, limpiamos la sesión y redirigimos
        unset($_SESSION['ids_a_eliminar']);
        header("Location: listar_incidencias_admin.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Eliminación</title>
    <link rel="stylesheet" href="styles1.css">
</head>
<body>
    <div class="container">
        <h2>Confirmar Eliminación</h2>
        <p>⚠️ <strong>¡Atención!</strong> Estás a punto de eliminar <strong><?php echo count($ids); ?></strong> incidencias.</p>
        <p>Esta acción es irreversible. ¿Estás seguro de que deseas continuar?</p>

        <!-- Formulario con los botones de confirmación y cancelación -->
        <form method="POST">
            <button type="submit" name="confirmar" value="1">✅ Sí, eliminar</button>
            <button type="submit" name="cancelar" value="1">❌ Cancelar</button>
        </form>
    </div>
</body>
</html>
