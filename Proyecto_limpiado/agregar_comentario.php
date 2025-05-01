<?php
// Inicia la sesión para poder acceder a las variables de sesión
session_start();

// Incluye el archivo de conexión a la base de datos
require 'db_connection.php';


// Verifica si se ha recibido un comentario mediante el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene el ID de la incidencia desde el formulario
    $id_incidencia = isset($_POST['id_incidencia']) ? (int) $_POST['id_incidencia'] : 0;
    
    // Obtiene el ID del usuario de la sesión
    $usuario_id = $_SESSION['user_id'];

    // Elimina espacios en blanco al inicio y al final del comentario
    $comentario = trim($_POST['comentario']);

    // Verifica si el comentario está vacío, si lo está, redirige con un mensaje de error
    if (empty($comentario)) {
        header("Location: ver_incidencia.php?id=$id_incidencia&error=El comentario no puede estar vacío.");
        exit();
    }

    // Verifica si el ID de la incidencia es válido
    if ($id_incidencia <= 0) {
        header("Location: listar_incidencias.php?error=ID de incidencia inválido.");
        exit();
    }

    // Prepara una consulta para verificar si la incidencia pertenece al usuario y si está abierta
    $stmt = $conn->prepare("SELECT ID_INCIDENCIA FROM INCIDENCIAS WHERE ID_INCIDENCIA = ? AND ID = ? AND ESTADO = 'Abierta'");

    // Asigna los valores a la consulta preparada para evitar inyección SQL
    $stmt->bind_param("ii", $id_incidencia, $usuario_id);
    
    // Ejecuta la consulta
    $stmt->execute();
    
    // Obtiene el resultado de la consulta
    $result = $stmt->get_result();

    // Si no se encuentra la incidencia o no está abierta, redirige con un mensaje de error
    if ($result->num_rows === 0) {
        header("Location: ver_incidencia.php?id=$id_incidencia&error=No tienes permiso para comentar o la incidencia no está abierta.");
        exit();
    }

    // Prepara la consulta SQL para insertar el nuevo comentario en la base de datos
    $stmt = $conn->prepare("INSERT INTO COMENTARIOS (TEXTO, ID_INCIDENCIA, ID) VALUES (?, ?, ?)");

    // Asigna los valores a los marcadores de posición en la consulta preparada
    $stmt->bind_param("sii", $comentario, $id_incidencia, $usuario_id);
    
    // Ejecuta la consulta para guardar el comentario
    $stmt->execute();

    // Redirige al usuario a la página de la incidencia con un mensaje de éxito
    header("Location: ver_incidencia.php?id=$id_incidencia&success=Comentario agregado.");
    exit();
}

// Si el usuario accede al script sin enviar datos por POST, lo redirige a la lista de incidencias
header("Location: listar_incidencias.php");
exit();
?>
