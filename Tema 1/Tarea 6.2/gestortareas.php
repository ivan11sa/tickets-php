<?php
    require ("funciones.php");

    echo "<h1>Añado 3 tareas</h1>"; 
    echo "</br>";

    añadirTarea($tareas, "IAW", "Pendiente");
    añadirTarea($tareas, "SRI", "Completada");
    añadirTarea($tareas, "ASO", "Pendiente"); 

    echo "<h1>Listo tareas</h1>"; 
    echo "</br>";

    listarTarea($tareas);

    echo "Elimino la tarea con clave SRI";
    echo "</br>";

    eliminarTarea($tareas, "SRI");

    echo "<h1>Listo tareas</h1>";
    echo "</br>";

    listarTarea($tareas);
?>