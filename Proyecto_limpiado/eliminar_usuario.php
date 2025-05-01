<?php
session_start();
require 'db_connection.php';

// Verifica si el usuario ha iniciado sesión y ha enviado el formulario correctamente
if (!isset($_SESSION['user_id']) || !isset($_POST['usuario_id']) || !isset($_POST['confirmar'])) {
    header("Location: gestionar_usuarios.php");
    exit();
}

$usuario_id = $_POST['usuario_id'];
$confirmar = $_POST['confirmar'];

if ($confirmar === "si") {
    // Ejecuta la eliminación del usuario
    $stmt = $conn->prepare("DELETE FROM USUARIOS WHERE ID = ?");
    $stmt->bind_param("i", $usuario_id);

    if ($stmt->execute()) {
        header("Location: gestionar_usuarios.php?success=Usuario eliminado correctamente");
        exit();
    } else {
        header("Location: gestionar_usuarios.php?error=No se pudo eliminar el usuario");
        exit();
    }
} else {
    // Si el usuario cancela, regresa a la lista de usuarios
    header("Location: gestionar_usuarios.php");
    exit();
}
?>