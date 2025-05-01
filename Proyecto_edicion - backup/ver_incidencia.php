<?php
// Inicia la sesión
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

// Verificar si la consulta se preparó correctamente
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

// Obtener los comentarios de la incidencia con los nombres correctos de las columnas
$sql_comentarios = "SELECT c.TEXTO, u.NOMBRE, c.FECHA_CREACION 
                    FROM COMENTARIOS c
                    INNER JOIN USUARIOS u ON c.ID = u.ID
                    WHERE c.ID_INCIDENCIA = ? 
                    ORDER BY c.FECHA_CREACION DESC";
$stmt_comentarios = $conn->prepare($sql_comentarios);


// Verificar si la consulta se preparó correctamente
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
        $sql_insert = "INSERT INTO COMENTARIOS (ID_INCIDENCIA, ID_USUARIO, TEXTO, FECHA_COMENTARIO) VALUES (?, ?, ?, NOW())";
        $stmt_insert = $conn->prepare($sql_insert);

        // Verificar si la consulta se preparó correctamente
        if (!$stmt_insert) {
            die("Error en la consulta SQL (insertar comentario): " . $conn->error);
        }

        $stmt_insert->bind_param("iis", $id_incidencia, $user_id, $comentario);
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
    <link rel="stylesheet" href="ver.css">
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
        <form method="POST" action="agregar_comentario.php">
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

        <!-- Botón para volver a la lista de incidencias -->
        <a href="listar_incidencias.php" class="back-button"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>
</body>

</html>
