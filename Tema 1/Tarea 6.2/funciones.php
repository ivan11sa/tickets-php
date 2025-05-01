<?php
    $tareas = [];

    function añadirTarea (&$tareas, $titulo, $estado){
        $tareas[$titulo] = $estado;

    }

    function listarTarea ($tareas) {
        if (count($tareas) > 0) {
            echo "<table border='1'>";
            echo "<tr><th>Tarea</th><th>Estado</th></tr>";
            foreach ( $tareas as $titulo => $estado){
                echo "<tr>";
                echo "<td> $titulo </td>";
                echo "<td> $estado </td>";
                echo "</tr>";
            }
            echo "</table>";
        
        } else {
            echo "No se encuentran tareas";
        }
    }

    function eliminarTarea (&$tareas, $titulo) {
        
        if ($tareas[$titulo]) {
            unset($tareas[$titulo]);
            echo "La tarea $titulo ha sido eliminada";
            echo "</br>";
        } else {
            echo "La tarea $titulo no ha sido encontrada";
            echo "</br>";
        }
    }

?>