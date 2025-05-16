<?php
// ===================================================
// Archivo: eliminar_incidencias.php
// Descripción: Permite a los usuarios eliminar incidencias
// y sus comentarios asociados, siempre que el usuario haya
// iniciado sesión. Incluye depuración para detectar
// problemas en la consulta SQL.
// ===================================================

session_start();                  // Inicia la sesión para usar $_SESSION
require 'db_connection.php';      // Conexión a la base de datos
require 'control.php';            // Control de accesos/funciones extra

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Debes iniciar sesión.");
    exit();
}

// Obtener datos del usuario (ajusta nombres de tabla/campos según tu BD)
$user_id = $_SESSION['user_id'];
$stmtUser = $conn->prepare("SELECT * FROM USUARIOS WHERE ID = ?");
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$user = $resultUser->fetch_assoc();
$stmtUser->close();

// Si no se encontró el usuario, redirigimos
if (!$user) {
    header("Location: login.php?error=Usuario no encontrado en la BD.");
    exit();
}

// Determinar si es admin (opcional)
$is_admin = $_SESSION['is_admin'] ?? 0;

// Página de origen (a dónde regresar tras eliminar)
$origen = $_POST['origen'] ?? $_SESSION['origen'] ?? "listar_incidencias.php";

// Manejar las incidencias a eliminar
if (isset($_POST['ids']) && is_array($_POST['ids']) && !empty($_POST['ids'])) {
    // Convertir cada ID a entero y guardarlos en la sesión
    $_SESSION['ids_a_eliminar'] = array_map('intval', $_POST['ids']);
}

// Si no hay incidencias en la sesión, no podemos eliminar nada
if (!isset($_SESSION['ids_a_eliminar']) || empty($_SESSION['ids_a_eliminar'])) {
    header("Location: $origen?error=No hay incidencias para eliminar.");
    exit();
}

// Guardamos los IDs en una variable para usarlos más adelante
$ids = $_SESSION['ids_a_eliminar'];

// Si el usuario confirma la eliminación
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirmar"])) {

    // ================ Eliminar comentarios asociados ================
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql_delete_comments = "DELETE FROM COMENTARIOS WHERE ID_INCIDENCIA IN ($placeholders)";

    // -- DEPURACIÓN: imprime la consulta y los IDs --
    echo "<pre>Consulta para comentarios: $sql_delete_comments</pre>";
    echo "<pre>IDs a eliminar (comentarios): " . print_r($ids, true) . "</pre>";

    $stmt_comments = $conn->prepare($sql_delete_comments);
    if (!$stmt_comments) {
        die("Error preparando la consulta de comentarios: " . $conn->error);
    }

    // Vinculamos parámetros (todos enteros)
    $types = str_repeat("i", count($ids));
    $stmt_comments->bind_param($types, ...$ids);

    // Ejecutamos
    if (!$stmt_comments->execute()) {
        die("Error al eliminar comentarios: " . $stmt_comments->error);
    }
    $stmt_comments->close();

    // ================ Eliminar las incidencias ================
    $sql_delete_incidencias = "DELETE FROM INCIDENCIAS WHERE ID_INCIDENCIA IN ($placeholders)";

    // -- DEPURACIÓN: imprime la consulta y los IDs --
    echo "<pre>Consulta para incidencias: $sql_delete_incidencias</pre>";
    echo "<pre>IDs a eliminar (incidencias): " . print_r($ids, true) . "</pre>";

    $stmt_incidencias = $conn->prepare($sql_delete_incidencias);
    if (!$stmt_incidencias) {
        die("Error preparando la consulta de incidencias: " . $conn->error);
    }

    $stmt_incidencias->bind_param($types, ...$ids);

    if (!$stmt_incidencias->execute()) {
        die("Error al eliminar incidencias: " . $stmt_incidencias->error);
    }
    $stmt_incidencias->close();

    // Limpiamos la sesión para que no se repita la eliminación
    unset($_SESSION['ids_a_eliminar']);

    // Redirigimos con mensaje de éxito
    header("Location: $origen?success=Incidencias eliminadas correctamente.");
    exit();
}

// Si el usuario cancela la eliminación
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["cancelar"])) {
    unset($_SESSION['ids_a_eliminar']);
    header("Location: $origen");
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
