<?php
// Arrancamos la sesión porque necesitamos saber quién está logueado.
session_start();

// Metemos los archivos que necesitamos para conectarnos a la base de datos y controlar accesos.
require 'db_connection.php';
require 'control.php';

// Verificamos si el usuario está logueado Y si es administrador.
// Si no cumple, lo mandamos de vuelta con un error.
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: listar_incidencias_admin.php?error=Acceso denegado.");
    exit();
}

// Pillamos el ID del usuario que queremos eliminar. Puede venir por GET o POST.
$usuario_id = $_GET['id'] ?? $_POST['usuario_id'] ?? null;

// Si no nos pasan un ID, no podemos hacer nada, así que redirigimos con un error.
if (!$usuario_id) {
    header("Location: gestionar_usuarios.php?error=No se especificó ningún usuario.");
    exit();
}

// Si el admin intenta borrarse a sí mismo... NOPE. Le avisamos y lo sacamos.
if ($usuario_id == $_SESSION['user_id']) {
    header("Location: gestionar_usuarios.php?error=No puedes eliminar tu propia cuenta.");
    exit();
}

// Si el admin ya confirmó la eliminación, procedemos a eliminar al usuario de la base de datos.
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirmar"])) {
    // Preparamos la consulta para eliminar al usuario.
    $stmt = $conn->prepare("DELETE FROM USUARIOS WHERE ID = ?");
    $stmt->bind_param("i", $usuario_id);

    // Ejecutamos la consulta y comprobamos si se eliminó correctamente.
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: gestionar_usuarios.php?success=Usuario eliminado correctamente.");
        exit();
    } else {
        $stmt->close();
        header("Location: gestionar_usuarios.php?error=Error al eliminar el usuario.");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Eliminación</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Confirmar Eliminación</h2>
        <p>⚠️ <strong>¡Atención!</strong> Estás a punto de eliminar este usuario.</p>
        <p>Esta acción es irreversible. ¿Estás seguro de que deseas continuar?</p>

        <form method="POST">
            <!-- Metemos el ID del usuario en un campo oculto para que se pase en el formulario -->
            <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario_id) ?>">
            <button type="submit" name="confirmar">✅ Sí, eliminar</button>
            <button type="button" onclick="window.location.href='gestionar_usuarios.php'">❌ Cancelar</button>
        </form>
    </div>
</body>
</html>

