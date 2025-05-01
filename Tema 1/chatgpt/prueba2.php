<?php
$alumnos = [
    "Manuel" => 4,
    "Victor" => 1, 
    "Veronica" => 6,
    "Maria" => 8,
];

function mostrarAlumnos($alumnos) {
    foreach ($alumnos as $alumno => $nota){
        echo "El alumno $alumno tiene de nota un $nota</br>";
    }
}

function añadirAlumno (&$alumnos, $alumno, $nota) {
    foreach ($alumnos as $nombre => $nota) {
        if ($nombre == $alumno) {
            echo "El alumno $alumno ya está registrado</br>";
            return;
        }
    }
    $alumnos[$alumno] =$nota;
    echo "El alumno $alumno ha sido registrado con nota $nota</br>";
}

function actualizarNota (&$alumnos, $alumno, $nota) {
    foreach ($alumnos as $nombre => $value){
        if ($nombre == $alumno ){
        $alumnos[$alumno] = $nota;
        echo "La nota de $alumno ha sido actualizada a $nota </br>";
        return;
        }    
    }
    echo "El estudiante $alumno no está registrado</br>";
}

function eliminarAlumno (&$alumnos, $alumno) {
    foreach ($alumnos as $nombre => $value){
        if ($nombre == $alumno ) {
            unset ($alumnos[$alumno]);
            echo "Estudiante $alumno eliminado del registro</br>";
            return;
        }
    }
    echo "El estudiante $alumno no está registrado </br>";
}

function mostrarAprobados ($alumnos) {
    echo "<h3> Los alumnos aprobado </h3>";
    foreach ($alumnos as $nombre => $nota) {
        if ($nota > 5) {
            echo "Estudiante: $nombre | Nota $nota</br>";
        }
    }
}

function calcularPromedio($alumnos) {
    $total = 0;
    $cantidad = 0;

    foreach ($alumnos as $nota) {
        $total += $nota;
        $cantidad++;
    }

    $promedio = $total / $cantidad;
    echo "<h3> El promedio de notas: </h3>";
    echo "El promedio es: $promedio";
}

mostrarAlumnos($alumnos);

// Registrar un nuevo estudiante
añadirAlumno($alumnos, "Pedro", 7);

// Intentar registrar un estudiante ya existente
añadirAlumno($alumnos, "Carlos", 8);

// Actualizar la nota de un estudiante
actualizarNota($alumnos, "Victor", 5);

// Eliminar un estudiante
eliminarAlumno($alumnos, "Veronica");

// Mostrar estudiantes aprobados
mostrarAprobados($alumnos);

// Calcular y mostrar el promedio de notas
calcularPromedio($alumnos);

// Mostrar estudiantes finales
mostrarAlumnos($alumnos);

?>