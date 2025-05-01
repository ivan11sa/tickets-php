<?php
//  Inicia la sesión para verificar la autenticación del usuario
session_start();

//  Incluye la conexión a la base de datos
require 'db_connection.php';

//  Verifica si el usuario ha iniciado sesión, si no, lo redirige a la página de inicio de sesión con un mensaje de error
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Debes iniciar sesión.");
    exit();
}

//  Obtiene el ID de la incidencia desde la URL y lo convierte a un número entero para evitar inyecciones SQL
$id_incidencia = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$usuario_id = $_SESSION['user_id']; // Obtiene el ID del usuario en sesión

//  Verifica si la conexión a la base de datos está disponible
if (!$conn) {
    die("Error de conexión a la base de datos.");
}

//  Consulta SQL para obtener la incidencia si pertenece al usuario actual
$sql = "SELECT * FROM INCIDENCIAS WHERE ID_INCIDENCIA = ? AND ID = ?";
$stmt = $conn->prepare($sql);

//  Verifica si la consulta se preparó correctamente
if (!$stmt) {
    die("Error en la consulta SQL: " . $conn->error);
}

//  Asigna los valores a la consulta preparada para evitar inyección SQL
$stmt->bind_param("ii", $id_incidencia, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$incidencia = $result->fetch_assoc();

//  Si no se encuentra la incidencia, redirige con un mensaje de error
if (!$incidencia) {
    header("Location: listar_incidencias.php?error=No tienes acceso a esta incidencia.");
    exit();
}

//  Consulta SQL para obtener los comentarios asociados a la incidencia
$sqlComentarios = "SELECT TEXTO, FECHA_CREACION FROM COMENTARIOS WHERE ID_INCIDENCIA = ? AND ID = ? ORDER BY FECHA_CREACION DESC";
$stmtComentarios = $conn->prepare($sqlComentarios);

//  Verifica si la consulta de comentarios se preparó correctamente
if (!$stmtComentarios) {
    die("Error en la consulta de comentarios: " . $conn->error);
}

//  Asigna los valores a la consulta preparada
$stmtComentarios->bind_param("ii", $id_incidencia, $usuario_id);
$stmtComentarios->execute();
$comentarios = $stmtComentarios->get_result();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Incidencia</title>
    <link rel="stylesheet" href="ver.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2>Detalles de la Incidencia</h2>
        <div class="details">
            <p><strong>Título:</strong> <?= htmlspecialchars($incidencia['TITULO']) ?></p>
            <p><strong>Descripción:</strong> <?= htmlspecialchars($incidencia['DESCRIPCION']) ?></p>
            <p><strong>Prioridad:</strong> <?= htmlspecialchars($incidencia['NIVEL_PRIORIDAD']) ?></p>
            <p><strong>Estado:</strong> <?= htmlspecialchars($incidencia['ESTADO']) ?></p>
        </div>

        <!--  Formulario para agregar un nuevo comentario a la incidencia -->
        <h3>Comentarios</h3>
        <form method="POST" action="agregar_comentario.php">
            <input type="hidden" name="id_incidencia" value="<?= $id_incidencia ?>">
            <textarea name="comentario" placeholder="Escribe tu comentario..." required></textarea>
            <button type="submit"><i class="fa-solid fa-comment"></i> Agregar Comentario</button>
        </form>

        <!--  Muestra el historial de comentarios asociados a la incidencia -->
        <h3>Historial de Comentarios</h3>
        <div class="comments">
            <?php while ($comentario = $comentarios->fetch_assoc()): ?>
                <div class="comment-box">
                    <p><strong><?= htmlspecialchars($comentario['FECHA_CREACION']) ?>:</strong> <?= htmlspecialchars($comentario['TEXTO']) ?></p>
                </div>
            <?php endwhile; ?>
        </div>

        <!--  Botón para volver a la lista de incidencias -->
        <a href="listar_incidencias.php" class="back-button"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>
</body>
</html>
