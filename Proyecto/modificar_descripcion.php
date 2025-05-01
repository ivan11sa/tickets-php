<?php
session_start();
require 'db_connection.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener las incidencias seleccionadas
if (!isset($_GET['ids']) || empty($_GET['ids'])) {
    exit("No se seleccionaron incidencias.");
}

$ids = explode(',', $_GET['ids']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

try {
    // Obtener las incidencias seleccionadas con su descripción
    $sql = "SELECT ID_INCIDENCIA, TITULO, DESCRIPCION FROM INCIDENCIAS WHERE ID_INCIDENCIA IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    $incidencias = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    error_log("Error en la consulta: " . $e->getMessage());
    exit("Error al cargar las incidencias. Detalles en el log.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Descripción</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
        }
        h2 {
            color: #333;
        }
        .recuadro {
            margin-top: 20px;
            text-align: left;
        }
        textarea {
            width: 100%;
            height: 150px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: none;
        }
        .btn-actualizar {
            background: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            margin-top: 10px;
            width: 100%;
        }
        .btn-actualizar:hover {
            background: #0056b3;
        }
        .fixed-bar {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: #007BFF;
            padding: 15px;
            text-align: center;
            font-size: 18px;
            color: white;
        }
        .btn-volver {
            background: white;
            color: #007BFF;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Modificar Descripción</h2>
        <form action="actualizar_descripcion.php" method="POST">
            <?php foreach ($incidencias as $incidencia): ?>
                <div class="recuadro">
                    <h3><?php echo htmlspecialchars($incidencia['TITULO']); ?></h3>
                    <textarea name="nueva_descripcion[<?php echo $incidencia['ID_INCIDENCIA']; ?>]"><?php echo htmlspecialchars($incidencia['DESCRIPCION']); ?></textarea>
                    <button type="submit" class="btn-actualizar">Actualizar Información</button>
                </div>
            <?php endforeach; ?>
        </form>
    </div>

    <div class="fixed-bar">
        <button class="btn-volver" onclick="window.location.href='dashboard.php'">Volver al Inicio</button>
    </div>
</body>
</html>
