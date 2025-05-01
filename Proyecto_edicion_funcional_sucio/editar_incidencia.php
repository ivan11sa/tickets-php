<?php
// Inicia la sesión para autenticación
session_start();

// Incluye la conexión a la base de datos
require 'db_connection.php';
require 'control.php';

// Verifica si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Debes iniciar sesión.");
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['is_admin'] ?? 0;

// Capturar el ID de la incidencia desde el formulario o la URL
$id_incidencia = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

// Capturar el origen desde GET o POST (por defecto, redirigir a listar_incidencias.php)
$origen = $_POST['origen'] ?? $_GET['origen'] ?? 'listar_incidencias.php';

// Si no se proporciona un ID válido, redirigir con error al origen correcto
if ($id_incidencia <= 0) {
    header("Location: $origen?error=Debes seleccionar una incidencia para editar.");
    exit();
}

// Verificar permisos: los usuarios solo pueden editar sus propias incidencias, los admin todas
if ($is_admin) {
    $sql = "SELECT * FROM INCIDENCIAS WHERE ID_INCIDENCIA = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_incidencia);
} else {
    $sql = "SELECT * FROM INCIDENCIAS WHERE ID_INCIDENCIA = ? AND ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_incidencia, $user_id);
}

$stmt->execute();
$incidencia = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Si no encuentra la incidencia, redirigir al origen con error
if (!$incidencia) {
    header("Location: $origen?error=No tienes permisos para editar esta incidencia.");
    exit();
}

// Procesar la actualización si se envía el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['guardar_cambios'])) {
    $titulo = htmlspecialchars(trim($_POST['titulo'] ?? ''));
    $descripcion = htmlspecialchars(trim($_POST['descripcion'] ?? ''));
    $nivel_prioridad = $_POST['nivel_prioridad'] ?? '';
    $estado = $_POST['estado'] ?? '';
    $id_provincia = (int) ($_POST['id_provincia'] ?? 0);

    if (empty($titulo) || empty($descripcion) || empty($nivel_prioridad) || empty($estado) || $id_provincia <= 0) {
        header("Location: editar_incidencia.php?id=$id_incidencia&origen=$origen&error=Todos los campos son obligatorios.");
        exit();
    }

    $stmt_update = $conn->prepare("UPDATE INCIDENCIAS SET TITULO = ?, DESCRIPCION = ?, NIVEL_PRIORIDAD = ?, ESTADO = ?, ID_PROVINCIA = ? WHERE ID_INCIDENCIA = ?");
    $stmt_update->bind_param("ssssii", $titulo, $descripcion, $nivel_prioridad, $estado, $id_provincia, $id_incidencia);
    $stmt_update->execute();
    $stmt_update->close();

    // Redirigir a la página de origen después de actualizar
    header("Location: $origen?success=Incidencia actualizada correctamente.");
    exit();
}

// Obtener lista de provincias para el formulario
$provincias = [];
$stmt_provincias = $conn->prepare("SELECT ID_PROVINCIA, NOMBRE_PROVINCIA FROM PROVINCIAS ORDER BY NOMBRE_PROVINCIA");
$stmt_provincias->execute();
$result_provincias = $stmt_provincias->get_result();

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
    <div class="container">
        <h2>Editar Incidencia</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $id_incidencia ?>">
            <input type="hidden" name="origen" value="<?= htmlspecialchars($origen) ?>"> <!-- Guardar el origen -->

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
                <select name="nivel_prioridad" id="nivel_prioridad" required>
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
        <button type="button" onclick="window.location.href='<?= htmlspecialchars($origen) ?>'">Volver al Inicio</button>
    </div>
</body>
</html>
