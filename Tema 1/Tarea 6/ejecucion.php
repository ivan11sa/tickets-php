<?php

require "funciones.php";

// Añadir algunas tareas
añadirTarea("Comprar pan", "Pendiente");
añadirTarea("Estudiar PHP", "Completada");
añadirTarea("Hacer ejercicio", "Pendiente");
añadirTarea ("Limpiar la casa", "Pendiente");
añadirTarea ("Estudiar SRI", "Pendiente");

// Listar tareas antes de eliminar
echo "<h2>Tareas Iniciales:</h2>";
listarTareas();

// Eliminar una tarea
eliminarTarea("Estudiar PHP");

// Listar tareas después de eliminar
echo "<h2>Tareas después de eliminar:</h2>";
listarTareas();
?>
