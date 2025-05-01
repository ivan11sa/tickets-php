<?php
session_start();
require 'db_connection.php';

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id']) || !isset($_POST['usuario_id'])) {
    header("Location: gestionar_usuarios.php");
    exit();
}

$usuario_id = $_POST['usuario_id'];
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
        <p>¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.</p>

        <form action="eliminar_usuario.php" method="POST">
            <input type="hidden" name="usuario_id" value="<?php echo htmlspecialchars($usuario_id); ?>">
            <button type="submit" name="confirmar" value="si">Sí, eliminar</button>
            <button type="submit" name="confirmar" value="no">Cancelar</button>
        </form>
    </div>
</body>
</html>
