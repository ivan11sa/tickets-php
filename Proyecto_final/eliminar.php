<?php
// Iniciar la sesión para acceder a los datos guardados temporalmente
session_start();

// Incluir la conexión a la base de datos
require 'db_connection.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    // Si no es administrador, lo enviamos de vuelta al inicio de sesión
    header("Location: login.php");
    exit();
}

// Verificamos si hay incidencias almacenadas en la sesión
if (!isset($_SESSION['ids_a_eliminar']) || empty($_SESSION['ids_a_eliminar'])) {
    // Si no hay incidencias para eliminar, regresamos al listado con un mensaje de error
    header("Location: listar_incidencias_admin.php?error=No hay incidencias para eliminar.");
    exit();
}

// Recuperamos los IDs de las incidencias seleccionadas desde la sesión
$ids = $_SESSION['ids_a_eliminar'];

// Si el usuario ha enviado el formulario de confirmación
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Si el usuario hizo clic en "Sí, eliminar"
    if (isset($_POST["confirmar"])) {        
        // Convertir los IDs en números enteros para evitar errores de seguridad
        $ids = array_map('intval', $ids);

        // Creamos una lista de signos de interrogación "?" para la consulta SQL
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        // Preparamos la consulta SQL para eliminar las incidencias seleccionadas
        $sql = "DELETE FROM INCIDENCIAS WHERE ID_INCIDENCIA IN ($placeholders)";
        $stmt = $conn->prepare($sql);

        // Si hay un error al preparar la consulta, lo registramos en el log y mostramos un error
        if (!$stmt) {
            error_log("Error al preparar la consulta: " . $conn->error);
            header("Location: listar_incidencias_admin.php?error=Error al preparar la consulta.");
            exit();
        }

        // Definir los tipos de los valores que vamos a enviar (todos son números enteros)
        $types = str_repeat("i", count($ids));
        $stmt->bind_param($types, ...$ids);

        // Ejecutar la consulta para eliminar todas las incidencias seleccionadas
        if ($stmt->execute()) {
            $stmt->close(); // Cerramos la consulta para liberar recursos
            unset($_SESSION['ids_a_eliminar']); // Limpiamos la sesión para evitar problemas

            // Redirigir a la lista de incidencias con un mensaje de éxito
            header("Location: listar_incidencias_admin.php?success=Incidencias eliminadas correctamente.");
            exit();
        } else {
            // Si hay un error al ejecutar la consulta, lo registramos y mostramos un mensaje de error
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            header("Location: listar_incidencias_admin.php?error=Error al eliminar las incidencias.");
            exit();
        }

    } else {
        // Si el usuario hizo clic en "Cancelar", volvemos al listado sin eliminar nada
        unset($_SESSION['ids_a_eliminar']); // Limpiamos la sesión
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
