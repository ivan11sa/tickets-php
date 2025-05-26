<?php
ob_start();
// Este archivo es el que se encarga de mostrar los detalles de una incidencia y permitir que los usuarios agreguen comentarios.

// Primero, revisamos si la sesión ya ha sido iniciada. Si no está iniciada, la iniciamos.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Aquí estamos incluyendo los archivos necesarios para la conexión a la base de datos y para gestionar el control de acceso.
// db_connection.php contiene la configuración para conectarse a la base de datos.
// control.php se encarga de la lógica para controlar el acceso a la página.
require 'db_connection.php';
require 'control.php';


// Verificamos si el usuario está autenticado. Si no lo está, redirigimos a la página de login.
if (!isset($_SESSION['user_id'])) {
    // Si no está logueado, lo mandamos a la página de login para que inicie sesión.
    header("Location: login.php");
    exit(); // Detenemos la ejecución del código para evitar que el usuario acceda a la página sin estar logueado.
}

// Guardamos el ID del usuario actual y si es administrador (esto nos ayudará a hacer controles de permisos más adelante).
$user_id = intval($_SESSION['user_id']);
$is_admin = isset($_SESSION['is_admin']) ? intval($_SESSION['is_admin']) : 0; // Si no es admin, asignamos 0.

// Ahora obtenemos el ID de la incidencia que queremos mostrar, lo tomamos de la URL.
$id_incidencia = isset($_GET['id']) ? intval($_GET['id']) : 0;
// Si el ID es menor o igual a cero, es inválido, entonces redirigimos a la página de listado con un mensaje de error.
if ($id_incidencia <= 0) {
    header("Location: listar_incidencias.php?error=Incidencia no válida.");
    exit(); // Salimos de la ejecución del script si el ID no es válido.
}

// Hacemos una consulta para verificar si la incidencia existe en la base de datos. Esto es muy importante para no mostrar información que no existe.
$sql = "SELECT * FROM INCIDENCIAS WHERE ID_INCIDENCIA = ?";
$stmt = $conn->prepare($sql); // Preparamos la consulta para evitar SQL Injection.
$stmt->bind_param("i", $id_incidencia); // Enlazamos el parámetro del ID de la incidencia.
$stmt->execute(); // Ejecutamos la consulta.
$result = $stmt->get_result(); // Obtenemos el resultado.
$incidencia = $result->fetch_assoc(); // Si existe, lo traemos como un arreglo asociativo.
$stmt->close(); // Cerramos el statement después de usarlo.

if (!$incidencia) {
    // Si no existe la incidencia, redirigimos a la página de listado de incidencias con un mensaje de error.
    header("Location: listar_incidencias.php?error=Incidencia no encontrada.");
    exit(); // Salimos del script para evitar que se ejecute más código.
}

// Ahora obtenemos todos los comentarios que están asociados a esta incidencia.
// Los ordenamos de la fecha más reciente a la más antigua.
$sql_comentarios = "SELECT c.TEXTO, u.NOMBRE, c.FECHA_CREACION 
                    FROM COMENTARIOS c
                    JOIN USUARIOS u ON c.ID = u.ID
                    WHERE c.ID_INCIDENCIA = ? 
                    ORDER BY c.FECHA_CREACION DESC"; // Ordenamos por la fecha de creación de los comentarios.
$stmt_comentarios = $conn->prepare($sql_comentarios); // Preparamos la consulta para obtener los comentarios.
$stmt_comentarios->bind_param("i", $id_incidencia); // Enlazamos el ID de la incidencia.
$stmt_comentarios->execute(); // Ejecutamos la consulta.
$comentarios = $stmt_comentarios->get_result()->fetch_all(MYSQLI_ASSOC); // Traemos todos los comentarios en forma de un arreglo asociativo.
$stmt_comentarios->close(); // Cerramos el statement después de usarlo.

// Verificamos si el formulario fue enviado mediante el método POST, es decir, si el usuario quiere agregar un comentario.
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['comentario'])) {
    $comentario = trim($_POST['comentario']); // Obtenemos el comentario enviado y eliminamos espacios innecesarios al principio y al final.

    // Validamos que el comentario no esté vacío y que tenga al menos 5 caracteres (esto es para evitar comentarios muy cortos).
    if (empty($comentario) || strlen($comentario) < 5) {
        // Si el comentario no es válido, redirigimos de vuelta con un mensaje de error.
        header("Location: ver_incidencia.php?id=$id_incidencia&error=El comentario debe tener al menos 5 caracteres.");
        exit(); // Terminamos la ejecución del script para evitar que se procese el formulario de forma incorrecta.
    }

    // Verificamos si el usuario tiene permiso para comentar en esta incidencia.
    // El usuario puede comentar si es el propietario de la incidencia o un administrador y si la incidencia está abierta.
    $stmt = $conn->prepare("SELECT ID_INCIDENCIA FROM INCIDENCIAS WHERE ID_INCIDENCIA = ? AND (ID = ? OR ? = 1) AND ESTADO = 'Abierta'");
    $stmt->bind_param("iii", $id_incidencia, $user_id, $is_admin); // Enlazamos los parámetros: ID de la incidencia, ID del usuario y si es admin.
    $stmt->execute(); // Ejecutamos la consulta.
    $result = $stmt->get_result(); // Obtenemos el resultado.

    // Si no encontramos ninguna coincidencia, significa que el usuario no tiene permisos para comentar.
    if ($result->num_rows === 0) {
        // Redirigimos al usuario con un mensaje de error.
        header("Location: ver_incidencia.php?id=$id_incidencia&error=No puedes comentar en esta incidencia.");
        exit(); // Terminamos la ejecución aquí.
    }

    // Si todo está bien, insertamos el nuevo comentario en la base de datos.
    $stmt_insert = $conn->prepare("INSERT INTO COMENTARIOS (TEXTO, ID_INCIDENCIA, ID) VALUES (?, ?, ?)");
    $stmt_insert->bind_param("sii", $comentario, $id_incidencia, $user_id); // Enlazamos los datos del comentario, ID de la incidencia y ID del usuario.
    $stmt_insert->execute(); // Ejecutamos la consulta para insertar el comentario.
    $stmt_insert->close(); // Cerramos el statement después de insertarlo.

    // Redirigimos a la misma página para evitar el reenvío del formulario y mostramos un mensaje de éxito.
    header("Location: ver_incidencia.php?id=$id_incidencia&success=Comentario agregado.");
    exit(); // Terminamos el script aquí para no continuar ejecutando más código innecesario.
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles de la Incidencia</title>
    <link rel="stylesheet" href="css/ver.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    
<?php if (!empty($_SESSION['show_warning'])): ?>
  <?php include 'session_warning.php'; ?>
<?php endif; ?>
    <div class="container">
        <h2>Detalles de la Incidencia</h2>
        <div class="details">
            <p><strong>Título:</strong> <?= htmlspecialchars($incidencia['TITULO']) ?></p> <!-- Mostramos el título de la incidencia. -->
            <p><strong>Descripción:</strong> <?= htmlspecialchars($incidencia['DESCRIPCION']) ?></p> <!-- Mostramos la descripción de la incidencia. -->
            <p><strong>Prioridad:</strong> <?= htmlspecialchars($incidencia['NIVEL_PRIORIDAD']) ?></p> <!-- Mostramos el nivel de prioridad de la incidencia. -->
            <p><strong>Estado:</strong> <?= htmlspecialchars($incidencia['ESTADO']) ?></p> <!-- Mostramos el estado de la incidencia. -->
        </div>

        <!-- Si existe algún mensaje de error, lo mostramos. -->
        <?php if (isset($_GET['error'])): ?>
            <p class="message error"><?php echo htmlspecialchars($_GET['error']); ?></p> <!-- Mostramos el error si existe. -->
        <?php endif; ?>

        <!-- Si existe algún mensaje de éxito, lo mostramos. -->
        <?php if (isset($_GET['success'])): ?>
            <p class="message success"><?php echo htmlspecialchars($_GET['success']); ?></p> <!-- Mostramos el éxito si todo salió bien. -->
        <?php endif; ?>

        <!-- Formulario para que el usuario agregue un comentario. -->
        <h3>Comentarios</h3>
        <form method="POST" novalidate> <!-- Usamos el método POST para enviar el formulario. -->
            <input type="hidden" name="id_incidencia" value="<?= htmlspecialchars($id_incidencia) ?>"> <!-- Enviamos el ID de la incidencia de forma oculta. -->
            <textarea name="comentario" placeholder="Escribe tu comentario..." required minlength="5"></textarea> <!-- Área de texto para el comentario con validación de longitud mínima. -->
            <button type="submit"><i class="fa-solid fa-comment"></i> Agregar Comentario</button> <!-- Botón para enviar el comentario. -->
        </form>

        <!-- Muestra todos los comentarios asociados a la incidencia. -->
        <h3>Historial de Comentarios</h3>
        <div class="comments">
            <?php if (!empty($comentarios)): ?>
                <?php foreach ($comentarios as $comentario): ?>
                    <div class="comment-box">
                        <p><strong><?= htmlspecialchars($comentario['NOMBRE']) ?> (<?= htmlspecialchars($comentario['FECHA_CREACION']) ?>):</strong></p> <!-- Nombre del usuario que hizo el comentario y la fecha de creación. -->
                        <p><?= htmlspecialchars($comentario['TEXTO']) ?></p> <!-- Texto del comentario. -->
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay comentarios aún.</p> <!-- Si no hay comentarios, mostramos un mensaje diciendo que aún no hay. -->
            <?php endif; ?>
        </div>
    </div>

    <div class="fixed-bar">
        <span>Tiempo transcurrido desde inicio de sesión: <?php printf("%02d:%02d:%02d", $horas, $minutos, $segundos); ?></span> <!-- Mostramos el tiempo que lleva el usuario logueado. -->
        <button type="button" onclick="window.location.href='listar_incidencias.php'">
            <i class="fa-solid fa-arrow-left"></i> Volver 
        </button>
    </div>
</body>
</html>

