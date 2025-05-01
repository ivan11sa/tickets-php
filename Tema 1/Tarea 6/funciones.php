<?php
$tareas = array();

function añadirTarea ($titulo, $estado) {
    global $tareas;
    $tarea = array (
        "titulo" => $titulo,
        "estado" => $estado
    );
    $tareas [] = $tarea;
}

function listarTareas () {
    global $tareas;
    if (count($tareas) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Tarea</th><th>Estado</th></tr>";
        foreach ($tareas as $tarea) {
            echo "<tr>";
            echo "<td>" . ($tarea['titulo']) . "</td>";
            echo "<td>" . ($tarea['estado']) . "</td>";
            echo "</tr>";
            
        }
        echo "</table>";
        
    } else {
        echo "No hay tareas";
    }
}

function eliminarTarea($titulo) {
    global $tareas; 
    foreach ($tareas as $i => $tarea) {
        if ($tarea['titulo'] == $titulo) {
            unset($tareas[$i]); 
            echo "</br>";
            echo "Tarea '$titulo' eliminada.<br>";
            return;
        }
    }
    echo "Tarea '$titulo' no encontrada.<br>";
}
?>