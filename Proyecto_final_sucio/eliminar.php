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



// Recuperar incidencias a eliminar desde la sesión
if (!isset($_SESSION['ids_a_eliminar']) || empty($_SESSION['ids_a_eliminar'])) {
    header("Location: listar_incidencias_admin.php?error=No hay incidencias para eliminar.");
    exit();
}

$ids = $_SESSION['ids_a_eliminar'];
$ids = array_map('intval', $ids); // Asegurar que sean enteros
$origen = $_SESSION['origen'] ?? "listar_incidencias_admin.php"; // Asegurar la redirección correcta

error_log("IDs en eliminar.php antes de ejecutar la consulta: " . print_r($ids, true));

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["confirmar"])) {
        // Eliminar primero los comentarios asociados a las incidencias
        $sql_delete_comments = "DELETE FROM COMENTARIOS WHERE ID_INCIDENCIA IN (" . implode(',', array_fill(0, count($ids), '?')) . ")";
        $stmt_comments = $conn->prepare($sql_delete_comments);
        if (!$stmt_comments) {
            die("Error en prepare (comentarios): " . $conn->error);
        }
        $types = str_repeat("i", count($ids));
        $stmt_comments->bind_param($types, ...$ids);
        if (!$stmt_comments->execute()) {
            die("Error al eliminar comentarios: " . $stmt_comments->error);
        }
        $stmt_comments->close();

        // Ahora eliminamos las incidencias
        $sql = "DELETE FROM INCIDENCIAS WHERE ID_INCIDENCIA IN (" . implode(',', array_fill(0, count($ids), '?')) . ")";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Error en prepare (incidencias): " . $conn->error);
        }
        $stmt->bind_param($types, ...$ids);
        if (!$stmt->execute()) {
            die("Error al eliminar incidencias: " . $stmt->error);
        }
        $stmt->close();

        error_log("Incidencias eliminadas correctamente.");
        unset($_SESSION['ids_a_eliminar']);
        header("Location: $origen?success=Incidencias eliminadas correctamente.");
        exit();
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
