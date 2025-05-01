<?php
require 'funciones.php';

// Manejar la solicitud POST para añadir tareas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion']) && $_POST['accion'] === 'añadir') {
        $titulo = $_POST['titulo'];
        $estado = $_POST['estado'];
        añadirTarea($titulo, $estado);
    }

    // Manejar la solicitud POST para eliminar tareas
    if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
        $titulo = $_POST['titulo_eliminar'];
        eliminarTarea($titulo);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tareas</title>
</head>
<body>
    <h1>Gestión de Tareas</h1>

    <h2>Añadir Tarea</h2>
    <form method="POST" action="">
        <label for="titulo">Título:</label>
        <input type="text" name="titulo" id="titulo" required>
        <br>
        <label for="estado">Estado:</label>
        <select name="estado" id="estado">
            <option value="pendiente">Pendiente</option>
            <option value="completada">Completada</option>
        </select>
        <br>
        <input type="hidden" name="accion" value="añadir">
        <button type="submit">Añadir Tarea</button>
    </form>

    <h2>Eliminar Tarea</h2>
    <form method="POST" action="">
        <label for="titulo_eliminar">Título de la Tarea:</label>
        <input type="text" name="titulo_eliminar" id="titulo_eliminar" required>
        <br>
        <input type="hidden" name="accion" value="eliminar">
        <button type="submit">Eliminar Tarea</button>
    </form>

    <h2>Lista de Tareas</h2>
    <?php listarTareas(); ?>
</body>
</html>
