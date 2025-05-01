<?php
// Inicia la sesión para gestionar la autenticación del usuario
session_start();

// Incluye el archivo de conexión a la base de datos
require 'db_connection.php';
require 'control.php';

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    // Si el usuario no ha iniciado sesión, lo redirige a la página de inicio de sesión
    header("Location: login.php");
    exit();
}

// Verifica si se han recibido incidencias seleccionadas
if (!isset($_GET['ids']) || empty($_GET['ids'])) {
    exit("No se seleccionaron incidencias.");
}

// Convierte la cadena de IDs en un array filtrando solo valores numéricos para evitar inyección SQL
$ids = array_filter(explode(',', $_GET['ids']), 'is_numeric');
if (empty($ids)) {
    exit("Error: IDs de incidencia inválidos.");
}


// Genera placeholders dinámicos para la consulta preparada
$placeholders = implode(',', array_fill(0, count($ids), '?'));

// Consulta SQL para obtener las incidencias seleccionadas
$sql = "SELECT ID_INCIDENCIA, TITULO, DESCRIPCION FROM INCIDENCIAS WHERE ID_INCIDENCIA IN ($placeholders)";
$stmt = $conn->prepare($sql);

// Asigna los valores de los IDs a la consulta
$stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
$stmt->execute();
$result = $stmt->get_result();
$incidencias = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Descripción</title>
    <link rel="stylesheet" href="descripcion.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2>Modificar Descripción</h2>

        <!-- Formulario para actualizar la descripción de las incidencias -->
        <form action="actualizar_descripcion.php" method="POST">
            <?php foreach ($incidencias as $incidencia): ?>
                <div class="recuadro">
                    <!-- Muestra el título de la incidencia -->
                    <h3><?php echo htmlspecialchars($incidencia['TITULO']); ?></h3>

                    <!-- Campo de texto para modificar la descripción -->
                    <textarea name="nueva_descripcion[<?php echo $incidencia['ID_INCIDENCIA']; ?>]" rows="5" cols="50"><?php echo trim(htmlspecialchars_decode($incidencia['DESCRIPCION'])); ?></textarea>

                    <!-- Botón para enviar los cambios -->
                    <button type="submit" class="btn-actualizar">Actualizar Información</button>
                </div>
            <?php endforeach; ?>
        </form>
    </div>

    <!-- Botón para volver al dashboard -->
    <div class="fixed-bar">
        <span>Tiempo transcurrido desde inicio de sesión: <?php printf("%02d:%02d:%02d", $horas, $minutos, $segundos); ?></span>
        <button class="btn-volver" onclick="window.location.href='dashboard.php'">Volver al Inicio</button>
    </div>
</body>
</html>
