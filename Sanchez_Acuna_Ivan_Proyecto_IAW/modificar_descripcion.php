<?php
// Este archivo permite modificar la descripción de incidencias seleccionadas.

session_start(); // Iniciar sesión si no está iniciada.

//  Incluir conexión a la base de datos y control de acceso.
require 'db_connection.php';
require 'control.php';

// Verificar si el usuario ha iniciado sesión, si no, redirigir al login.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Debes iniciar sesión.");
    exit();
}

// Verificar si hay incidencias almacenadas en sesión para modificar.
if (!isset($_SESSION['ids_a_modificar']) || empty($_SESSION['ids_a_modificar'])) {
    header("Location: listar_incidencias.php?error=No se seleccionaron incidencias.");
    exit();
}

// Obtener los IDs de las incidencias desde la sesión.
$ids = $_SESSION['ids_a_modificar'];

// Construir una consulta segura con `?` como marcadores de posición.
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$sql = "SELECT ID_INCIDENCIA, TITULO, DESCRIPCION FROM INCIDENCIAS WHERE ID_INCIDENCIA IN ($placeholders)";

// Preparar la consulta SQL.
$stmt = $conn->prepare($sql);

// Verificar si la preparación fue exitosa.
if (!$stmt) {
    header("Location: listar_incidencias.php?error=Error en la consulta.");
    exit();
}

// Generar los tipos de datos para `bind_param()`.
$types = str_repeat('i', count($ids));

// Asociar los parámetros a la consulta.
$stmt->bind_param($types, ...array_map('intval', $ids));

// Ejecutar la consulta.
$stmt->execute();
$result = $stmt->get_result();
$incidencias = $result->fetch_all(MYSQLI_ASSOC);

// Si no hay incidencias encontradas, redirigir con mensaje de error.
if (empty($incidencias)) {
    header("Location: listar_incidencias.php?error=No se encontraron incidencias.");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Descripción</title>
    <link rel="stylesheet" href="css/descripcion.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<?php include 'session_warning.php'; ?>
    <div class="container">
        <h2>Modificar Descripción</h2>

        <!-- Formulario para actualizar la descripción de incidencias -->
        <form action="procesar_acciones.php" method="POST">
            <input type="hidden" name="accion" value="guardar_descripcion">
            <input type="hidden" name="origen" value="listar_incidencias.php">
            
            <?php foreach ($incidencias as $incidencia): ?>
                <div class="recuadro">
                    <h3><?php echo htmlspecialchars($incidencia['TITULO']); ?></h3>
                    <textarea 
                        name="nueva_descripcion[<?php echo $incidencia['ID_INCIDENCIA']; ?>]" 
                        rows="5" 
                        cols="50"><?php echo htmlspecialchars($incidencia['DESCRIPCION']); ?></textarea>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn-actualizar">Actualizar Información</button>
        </form>
    </div>

    <div class="fixed-bar">
        <span>Tiempo transcurrido desde inicio de sesión: <?php printf("%02d:%02d:%02d", $horas, $minutos, $segundos); ?></span> <!-- Mostramos el tiempo que lleva el usuario logueado. -->
        <button type="button" onclick="window.location.href='listar_incidencias.php'">
            <i class="fa-solid fa-arrow-left"></i> Volver 
        </button>
    </div>

</body>
</html>



