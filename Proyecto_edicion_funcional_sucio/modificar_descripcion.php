<?php
session_start();
require 'db_connection.php';
require 'control.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verificar si hay incidencias almacenadas en sesión
if (!isset($_SESSION['ids_a_modificar']) || empty($_SESSION['ids_a_modificar'])) {
    echo "<p style='color:red; text-align:center;'>No se seleccionaron incidencias.</p>";
    exit();
}

// Obtener los IDs de sesión
$ids = $_SESSION['ids_a_modificar'];

// Obtener las incidencias seleccionadas
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$sql = "SELECT ID_INCIDENCIA, TITULO, DESCRIPCION FROM INCIDENCIAS WHERE ID_INCIDENCIA IN ($placeholders)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
$stmt->execute();
$result = $stmt->get_result();
$incidencias = $result->fetch_all(MYSQLI_ASSOC);

// Si no hay incidencias, mostrar mensaje de error
if (empty($incidencias)) {
    echo "<p style='color:red; text-align:center;'>No se encontraron incidencias.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Descripción</title>
    <link rel="stylesheet" href="css/descripcion.css">
</head>
<body>
<div class="container">
        <h2>Modificar Descripción</h2>

        <form action="procesar_acciones.php" method="POST">
            <input type="hidden" name="accion" value="guardar_descripcion">
            <input type="hidden" name="origen" value="listar_incidencias.php">
            <?php foreach ($incidencias as $incidencia): ?>
                <div class="recuadro">
                    <h3><?php echo htmlspecialchars($incidencia['TITULO']); ?></h3>
                    <textarea name="nueva_descripcion[<?php echo $incidencia['ID_INCIDENCIA']; ?>]" rows="5" cols="50"><?php echo trim(htmlspecialchars_decode($incidencia['DESCRIPCION'])); ?></textarea>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn-actualizar">Actualizar Información</button>
        </form>
</div>
</body>
</html>


