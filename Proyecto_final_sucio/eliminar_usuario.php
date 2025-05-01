<?php
// Inicia la sesión
session_start();

// Incluye la conexión a la base de datos y control de permisos
require 'db_connection.php';
require 'control.php';

// Verifica si el usuario tiene permisos de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: listar_incidencias_admin.php?error=Acceso denegado");
    exit();
}

// Verifica si se recibió un ID de usuario para eliminar
if (!isset($_GET['id']) && !isset($_POST['usuario_id'])) {
    header("Location: gestionar_usuarios.php?error=No se especificó ningún usuario.");
    exit();
}

// Obtener el ID del usuario a eliminar
$usuario_id = $_GET['id'] ?? $_POST['usuario_id'];

// Si el formulario de confirmación fue enviado, eliminar el usuario
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirmar"])) {
    $stmt = $conn->prepare("DELETE FROM USUARIOS WHERE ID = ?");
    $stmt->bind_param("i", $usuario_id);

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

// Si el usuario aún no ha confirmado, mostrar la página de confirmación
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Eliminación</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Confirmar Eliminación</h2>
        <p>⚠️ <strong>¡Atención!</strong> Estás a punto de eliminar este usuario.</p>
        <p>Esta acción es irreversible. ¿Estás seguro de que deseas continuar?</p>

        <form method="POST">
            <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario_id) ?>">
            <button type="submit" name="confirmar">✅ Sí, eliminar</button>
            <button type="button" onclick="window.location.href='gestionar_usuarios.php'">❌ Cancelar</button>
        </form>
    </div>
</body>
</html>


