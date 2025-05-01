<?php
// Iniciar la sesión
session_start();

// Incluir la conexión a la base de datos
require 'db_connection.php';
require 'control.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Obtener el ID de la incidencia desde la URL
$id_incidencia = $_GET['id'] ?? 0;
$id_incidencia = intval($id_incidencia);

if ($id_incidencia <= 0) {
    header("Location: listar_incidencias.php?error=Incidencia no válida.");
    exit();
}

// Verificar la conexión a la base de datos
if (!$conn) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Obtener la información de la incidencia
$sql = "SELECT * FROM INCIDENCIAS WHERE ID_INCIDENCIA = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error en la consulta SQL (incidencia): " . $conn->error);
}

$stmt->bind_param("i", $id_incidencia);
$stmt->execute();
$result = $stmt->get_result();
$incidencia = $result->fetch_assoc();
$stmt->close();

if (!$incidencia) {
    header("Location: listar_incidencias.php?error=Incidencia no encontrada.");
    exit();
}

// Obtener los comentarios de la incidencia
$sql_comentarios = "SELECT c.TEXTO, u.NOMBRE, c.FECHA_CREACION 
                    FROM COMENTARIOS c
                    INNER JOIN USUARIOS u ON c.ID = u.ID
                    WHERE c.ID_INCIDENCIA = ? 
                    ORDER BY c.FECHA_CREACION DESC";
$stmt_comentarios = $conn->prepare($sql_comentarios);

if (!$stmt_comentarios) {
    die("Error en la consulta SQL (comentarios): " . $conn->error);
}

$stmt_comentarios->bind_param("i", $id_incidencia);
$stmt_comentarios->execute();
$comentarios = $stmt_comentarios->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_comentarios->close();

// Procesar la adición de un comentario
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['comentario'])) {
    $comentario = trim($_POST['comentario']);

    if (!empty($comentario)) {
        // Verificar si la incidencia pertenece al usuario y está abierta
        $stmt = $conn->prepare("SELECT ID_INCIDENCIA FROM INCIDENCIAS WHERE ID_INCIDENCIA = ? AND ID = ? AND ESTADO = 'Abierta'");
        $stmt->bind_param("ii", $id_incidencia, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            header("Location: ver_incidencia.php?id=$id_incidencia&error=No puedes comentar en esta incidencia.");
            exit();
        }

        // Insertar el comentario en la base de datos
        $stmt_insert = $conn->prepare("INSERT INTO COMENTARIOS (TEXTO, ID_INCIDENCIA, ID) VALUES (?, ?, ?)");
        if (!$stmt_insert) {
            die("Error en la consulta SQL (insertar comentario): " . $conn->error);
        }
        $stmt_insert->bind_param("sii", $comentario, $id_incidencia, $user_id);
        $stmt_insert->execute();
        $stmt_insert->close();
        
        // Recargar la página para ver el nuevo comentario
        header("Location: ver_incidencia.php?id=$id_incidencia&success=Comentario agregado.");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles de la Incidencia</title>
    <link rel="stylesheet" href="css/ver.css">
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

        <!-- Formulario para agregar un nuevo comentario -->
        <h3>Comentarios</h3>
        <form method="POST">
            <input type="hidden" name="id_incidencia" value="<?= htmlspecialchars($id_incidencia) ?>">
            <textarea name="comentario" placeholder="Escribe tu comentario..." required></textarea>
            <button type="submit"><i class="fa-solid fa-comment"></i> Agregar Comentario</button>
        </form>

        <!-- Muestra el historial de comentarios asociados a la incidencia -->
        <h3>Historial de Comentarios</h3>
        <div class="comments">
            <?php foreach ($comentarios as $comentario): ?>
                <div class="comment-box">
                    <p><strong><?= htmlspecialchars($comentario['FECHA_CREACION']) ?>:</strong> <?= htmlspecialchars($comentario['TEXTO']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="fixed-bar">
        <span>Tiempo transcurrido desde inicio de sesión: <?php printf("%02d:%02d:%02d", $horas, $minutos, $segundos); ?></span>
        <button type="button" onclick="window.location.href='listar_incidencias.php'">Volver</button>
    </div>
</body>
</html>
