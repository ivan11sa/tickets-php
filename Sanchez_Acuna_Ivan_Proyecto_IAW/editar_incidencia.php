<?php
ob_start();
// Este archivo permite a los usuarios editar incidencias si tienen permiso para hacerlo.

// Iniciar la sesión para manejar la autenticación del usuario.
session_start();

// Incluir los archivos necesarios para la conexión a la base de datos y el control de acceso.
require 'db_connection.php'; // Archivo que gestiona la conexión con la base de datos.
require 'control.php'; // Archivo que maneja los permisos y control de acceso.

// Verificar si el usuario ha iniciado sesión, si no, redirigirlo al login con un mensaje de error.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Debes iniciar sesión."); // Redirigir con un mensaje de error.
    exit();
}

// Obtener datos del usuario desde la sesión.
$user_id = $_SESSION['user_id']; // ID del usuario autenticado.
$is_admin = $_SESSION['is_admin'] ?? 0; // Si no está definido, asumir que no es admin.

// Obtener el ID de la incidencia desde POST o GET. Si no existe, asignar 0.
$id_incidencia = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

// Obtener el origen desde POST o GE. La definición de esta variable es muy importante para poder volver a la página desde la que hemos entrado ya que 
// estamos diferenciando entre usuario simple y usuario administrador. 
$origen = $_POST['origen'] ?? $_GET['origen'] ?? 'listar_incidencias.php';

// Si el ID de la incidencia no es válido (menor o igual a 0), redirigir con un mensaje de error.
if ($id_incidencia <= 0) {
    header("Location: $origen?error=Debes seleccionar una incidencia válida.");
    exit();
}

// Verificar permisos: los administradores pueden editar cualquier incidencia.
// Los usuarios normales solo pueden editar sus propias incidencias.
if ($is_admin) {
    $sql = "SELECT * FROM INCIDENCIAS WHERE ID_INCIDENCIA = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_incidencia);
} else {
    // Si no es administrador, solo puede ver las incidencias creadas por él mismo.
    $sql = "SELECT * FROM INCIDENCIAS WHERE ID_INCIDENCIA = ? AND ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_incidencia, $user_id);
}

// Ejecutar la consulta para obtener los datos de la incidencia.
$stmt->execute();
$incidencia = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Si la incidencia no existe o el usuario no tiene permisos, redirigir con error. Es muy importante para controlar que usuario puede realizar que funcion
if (!$incidencia) {
    header("Location: $origen?error=No tienes permisos para editar esta incidencia.");
    exit();
}

// Si el formulario fue enviado, procesar la actualización de los datos.
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['guardar_cambios'])) {
    // Obtener y limpiar los datos enviados
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $nivel_prioridad = $_POST['nivel_prioridad'] ?? '';
    $estado = $_POST['estado'] ?? '';
    $id_provincia = (int)($_POST['id_provincia'] ?? 0);

    // Armar el array para detectar campos vacíos
    $campos_vacios = [];
    if (empty($titulo)) {
        $campos_vacios[] = "título";
    }
    if (empty($descripcion)) {
        $campos_vacios[] = "descripción";
    }
    if (empty($nivel_prioridad)) {
        $campos_vacios[] = "nivel de prioridad";
    }
    if (empty($estado)) {
        $campos_vacios[] = "estado";
    }
    if ($id_provincia <= 0) {
        $campos_vacios[] = "provincia";
    }

    // Si existen campos vacíos, se genera el mensaje de error y se redirige
    if (!empty($campos_vacios)) {
        $error = "El campo " . implode(", ", $campos_vacios) . " " . (count($campos_vacios) === 1 ? "está" : "están") . " vacío" . (count($campos_vacios) === 1 ? "" : "s") . ".";
        header("Location: editar_incidencia.php?id=$id_incidencia&origen=$origen&error=" . urlencode($error));
        exit();
    }

    // Si todos los campos están completos, proceder a actualizar la incidencia
    $stmt_update = $conn->prepare("UPDATE INCIDENCIAS SET TITULO = ?, DESCRIPCION = ?, NIVEL_PRIORIDAD = ?, ESTADO = ?, ID_PROVINCIA = ? WHERE ID_INCIDENCIA = ?");
    $stmt_update->bind_param("ssssii", $titulo, $descripcion, $nivel_prioridad, $estado, $id_provincia, $id_incidencia);
    $stmt_update->execute();
    $stmt_update->close();

    header("Location: $origen?success=Incidencia actualizada correctamente.");
    exit();
}


// Obtener la lista de provincias para mostrarlas en el formulario de edición.
$provincias = [];
$stmt_provincias = $conn->prepare("SELECT ID_PROVINCIA, NOMBRE_PROVINCIA FROM PROVINCIAS ORDER BY NOMBRE_PROVINCIA");
$stmt_provincias->execute();
$result_provincias = $stmt_provincias->get_result();

// Almacenar las provincias obtenidas en un array.
while ($row = $result_provincias->fetch_assoc()) {
    $provincias[] = $row;
}

$stmt_provincias->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Incidencia</title>
    <link rel="stylesheet" href="css/incidencia.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<?php if (!empty($_SESSION['show_warning'])): ?>
  <?php include 'session_warning.php'; ?>
<?php endif; ?>
    <div class="container">
        <h2>Editar Incidencia</h2>
        <form method="POST" novalidate>
            <input type="hidden" name="id" value="<?= $id_incidencia ?>">
            <input type="hidden" name="origen" value="<?= htmlspecialchars($origen) ?>">

         
        <!-- Muestra mensajes de error o éxito si existen -->
        <?php if (isset($_GET['error'])): ?>
            <p class="message error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <p class="message success"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php endif; ?>

            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($incidencia['TITULO']) ?>" required>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea name="descripcion" id="descripcion" required><?= htmlspecialchars($incidencia['DESCRIPCION']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="nivel_prioridad">Nivel de Prioridad:</label>
                <select id="nivel_prioridad" name="nivel_prioridad" required>
                    <option value="">Seleccione nivel</option>
                    <option value="Baja" <?= $incidencia['NIVEL_PRIORIDAD'] == 'Baja' ? 'selected' : '' ?>>Baja</option>
                    <option value="Media" <?= $incidencia['NIVEL_PRIORIDAD'] == 'Media' ? 'selected' : '' ?>>Media</option>
                    <option value="Alta" <?= $incidencia['NIVEL_PRIORIDAD'] == 'Alta' ? 'selected' : '' ?>>Alta</option>
                    <option value="Urgente" <?= $incidencia['NIVEL_PRIORIDAD'] == 'Urgente' ? 'selected' : '' ?>>Urgente</option>
                </select>

            </div>

            <div class="form-group">
                <label for="estado">Estado:</label>
                <select name="estado" id="estado" required>
                    <option value="Abierta" <?= $incidencia['ESTADO'] == 'Abierta' ? 'selected' : '' ?>>Abierta</option>
                    <option value="Cerrada" <?= $incidencia['ESTADO'] == 'Cerrada' ? 'selected' : '' ?>>Cerrada</option>
                </select>
            </div>

            <div class="form-group">
                <label for="id_provincia">Provincia:</label>
                <select name="id_provincia" id="id_provincia" required>
                    <?php foreach ($provincias as $provincia): ?>
                        <option value="<?= $provincia['ID_PROVINCIA'] ?>" <?= $incidencia['ID_PROVINCIA'] == $provincia['ID_PROVINCIA'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($provincia['NOMBRE_PROVINCIA']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button class="btn" type="submit" name="guardar_cambios">Guardar Cambios</button>
        </form>
    </div>
    <div class="fixed-bar">
        <span>Tiempo transcurrido desde inicio de sesión: <?php printf("%02d:%02d:%02d", $horas, $minutos, $segundos); ?></span>
        <button type="button" onclick="window.location.href='<?php echo htmlspecialchars($origen, ENT_QUOTES, 'UTF-8'); ?>'">
            <i class="fa-solid fa-arrow-left"></i> Volver 
        </button>
    </div>
</body>
</html>
