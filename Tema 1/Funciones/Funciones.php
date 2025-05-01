<?php

function multiplicar($numero) {
    echo "Tabla de multiplicar de $numero: </br>";
    for ($i = 1; $i <= 10; $i++) {
        $resultado = $numero * $i;
        echo "$numero x $i = $resultado </br>";
    }
}

function rango($inicio, $fin) {
    if ($inicio > $fin) {
        return;
    }
    for ($numero = $inicio; $numero <= $fin; $numero++) {
        multiplicar($numero);
    }
}

function inicializar_array($n, $min, $max) {
    $array = [];
    for ($i = 0; $i < $n; $i++) {
        $array[$i] = rand($min, $max);
    }
    return $array;
}

function arrayAleatorio($cantidad, $min, $max) {
    $array = [];
    for ($i = 0; $i < $cantidad; $i++) {
        $array[$i] = rand($min, $max);
    }
    return $array;
}

function mostrarArray($array) {
    echo "Valores del array: </br>";
    foreach ($array as $clave => $valor) {
        echo "Clave: " . $clave . " - Valor: " . $valor . "<br>";
    }
}

function calcular_media($array) {
    $suma = array_sum($array);
    $numero = count($array);
    return $suma / $numero;
}

function calcularMaximo($array) {
    return max($array);
}

function calcularMinimo($array) {
    return min($array);
}

function imprimir_array($array) {
    echo "<table border='1'>";
    echo "<tr><th>Posición</th><th>Valor</th></tr>";

    foreach ($array as $clave => $valor) {
        echo "<tr><td>$clave</td><td>$valor</td></tr>";
    }

    echo "</table>";
}

/* Las diferencias entre include y el require. Tanto include como require puedes incluir la ruta 
que va a usar, pero en caso de fallo producirá un error fatal con include marcando este error */
?>


