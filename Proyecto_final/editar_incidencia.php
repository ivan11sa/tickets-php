<?php
// Inicia la sesión para manejar variables de sesión
session_start();

// Incluye los archivos de conexión a la base de datos y control de acceso
require 'db_connection.php';
require 'control.php';

// Verifica si el usuario tiene sesión activa y si es administrador
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: listar_incidencias_admin.php?error=Acceso denegado");
    exit();
}

// Obtiene el ID de la incidencia desde el formulario o la URL
$id_incidencia = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

// Si no se proporciona un ID válido, redirige con un mensaje de error
if ($id_incidencia <= 0) {
    header("Location: listar_incidencias_admin.php?error=Debes seleccionar una incidencia para editar.");
    exit();
}

// Procesa la actualización de la incidencia si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar_cambios'])) {
    // Obtiene y limpia los datos enviados por el usuario
    $titulo = htmlspecialchars(trim($_POST['titulo'] ?? ''));
    $descripcion = htmlspecialchars(trim($_POST['descripcion'] ?? ''));
    $nivel_prioridad = $_POST['nivel_prioridad'] ?? '';
    $estado = $_POST['estado'] ?? '';
    $id_provincia = (int) ($_POST['id_provincia'] ?? 0);

    // Verifica que todos los campos sean válidos
    if (empty($titulo) || empty($descripcion) || empty($nivel_prioridad) || empty($estado) || $id_provincia <= 0) {
        header("Location: listar_incidencias_admin.php?error=Todos los campos son obligatorios.");
        exit();
    }

    
    // Prepara la consulta SQL para actualizar la incidencia en la base de datos
    $stmt_update = $conn->prepare("UPDATE INCIDENCIAS SET TITULO = ?, DESCRIPCION = ?, NIVEL_PRIORIDAD = ?, ESTADO = ?, ID_PROVINCIA = ? WHERE ID_INCIDENCIA = ?");
    $stmt_update->bind_param("ssssii", $titulo, $descripcion, $nivel_prioridad, $estado, $id_provincia, $id_incidencia);
    $stmt_update->execute();
    $stmt_update->close();

    // Redirige con un mensaje de éxito si la actualización fue correcta
    header("Location: listar_incidencias_admin.php?success=Incidencia actualizada correctamente");
    exit();

}


// Obtiene los datos actuales de la incidencia para mostrarlos en el formulario
$stmt = $conn->prepare("SELECT * FROM INCIDENCIAS WHERE ID_INCIDENCIA = ?");
$stmt->bind_param("i", $id_incidencia);
$stmt->execute();
$incidencia = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Si la incidencia no existe, redirige con un mensaje de error
if (!$incidencia) {
    header("Location: listar_incidencias_admin.php?error=Incidencia no encontrada");
    exit();
}


// Obtiene la lista de provincias disponibles en la base de datos
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
    <link rel="stylesheet" href="incidencia.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2>Editar Incidencia</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $id_incidencia ?>">

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
        <button type="button" onclick="window.location.href='listar_incidencias_admin.php'">Volver al Inicio</button>
    </div>
</body>
</html>
