<?php
// ===================================================
// Archivo: eliminar_incidencias.php
// Descripción: Permite a los usuarios eliminar incidencias
// y sus comentarios asociados, siempre que el usuario haya
// iniciado sesión.
// ===================================================

// 1) Bufferizar para que header() funcione pese a salidas
if (!headers_sent()) {
    ob_start();
}

// 2) Iniciar sesión y cargar dependencias
session_start();
require 'db_connection.php';
require 'control.php';

// 3) Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Debes iniciar sesión.");
    exit();
}

// 4) Obtener datos del usuario
$user_id = $_SESSION['user_id'];
$stmtUser = $conn->prepare("SELECT * FROM USUARIOS WHERE ID = ?");
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$user = $stmtUser->get_result()->fetch_assoc();
$stmtUser->close();

if (!$user) {
    header("Location: login.php?error=Usuario no encontrado en la BD.");
    exit();
}

// 5) Preparar origen de retorno
$origen = $_POST['origen'] ?? $_SESSION['origen'] ?? "listar_incidencias.php";

// 6) Recoger IDs a eliminar
if (isset($_POST['ids']) && is_array($_POST['ids']) && !empty($_POST['ids'])) {
    $_SESSION['ids_a_eliminar'] = array_map('intval', $_POST['ids']);
}

if (empty($_SESSION['ids_a_eliminar'])) {
    header("Location: $origen?error=No hay incidencias para eliminar.");
    exit();
}

$ids = $_SESSION['ids_a_eliminar'];
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$types = str_repeat('i', count($ids));

// 7) Si confirman eliminación
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirmar"])) {

    // 7.1) Eliminar comentarios asociados
    $sql_delete_comments = "DELETE FROM COMENTARIOS WHERE ID_INCIDENCIA IN ($placeholders)";
    $stmt_comments = $conn->prepare($sql_delete_comments);
    if (!$stmt_comments) {
        die("Error preparando la consulta de comentarios: " . $conn->error);
    }
    $stmt_comments->bind_param($types, ...$ids);
    $stmt_comments->execute();
    $stmt_comments->close();

    // 7.2) Eliminar las propias incidencias
    $sql_delete_incidencias = "DELETE FROM INCIDENCIAS WHERE ID_INCIDENCIA IN ($placeholders)";
    $stmt_incidencias = $conn->prepare($sql_delete_incidencias);
    if (!$stmt_incidencias) {
        die("Error preparando la consulta de incidencias: " . $conn->error);
    }
    $stmt_incidencias->bind_param($types, ...$ids);
    $stmt_incidencias->execute();
    $stmt_incidencias->close();

    // 7.3) Limpiar y redirigir con éxito
    unset($_SESSION['ids_a_eliminar']);
    header("Location: $origen?success=Incidencias eliminadas correctamente.");
    // 7.4) Enviar buffer y salir
    if (ob_get_length()) {
        ob_end_flush();
    }
    exit();
}

// 8) Si cancelan eliminación
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["cancelar"])) {
    unset($_SESSION['ids_a_eliminar']);
    header("Location: $origen");
    if (ob_get_length()) {
        ob_end_flush();
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Eliminación</title>
    <link rel="stylesheet" href="css/styles1.css"> <!-- Ajusta tu CSS -->
</head>
<body>

<?php if (!empty($_SESSION['show_warning'])): ?>
  <?php include 'session_warning.php'; ?>
<?php endif; ?>
    <div class="container">
        <h2>Confirmar Eliminación</h2>
        <p>
            ⚠️ <strong>¡Atención!</strong>
            <?php echo htmlspecialchars($user['NOMBRE'] ?? ''); ?>,
            estás a punto de eliminar <strong><?php echo count($ids); ?></strong> incidencias.
        </p>
        <p>Esta acción es irreversible. ¿Estás seguro de que deseas continuar?</p>

        <!-- Formulario con botones para confirmar o cancelar la eliminación -->
        <form method="POST">
            <button type="submit" name="confirmar" value="1">✅ Sí, eliminar</button>
            <button type="submit" name="cancelar" value="1">❌ Cancelar</button>
        </form>
    </div>

    <!-- Barra fija con el tiempo transcurrido desde el inicio de sesión -->
    <div class="fixed-bar">
        <span>Tiempo transcurrido desde inicio de sesión: <?php printf("%02d:%02d:%02d", $horas, $minutos, $segundos); ?></span>
    </div>
</body>
</html>
