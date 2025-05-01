<?php
$numeros = []; // Array inicial vacío

// Generar y mostrar los números
function mostrarNumeros($numeros) {
    foreach ($numeros as $clave => $valor) {
        echo "La posición $clave tiene el valor $valor<br>";
    }
}

// Llenar el array con 10 números aleatorios
function generarNumeros(&$numeros) {
    for ($i = 0; $i < 10; $i++) {
        $numeros[] = rand(1, 30);
    }
}

// Ordenar los números en orden ascendente o descendente
function ordenarNumeros(&$numeros, $orden) {
    if ($orden === "asc") {
        sort($numeros);
    } else {
        rsort($numeros);
    }
    return $numeros;
}

// Filtrar los números pares
function filtrarPares($numeros) {
    $pares = [];
    foreach ($numeros as $numero) {
        if ($numero % 2 == 0) {
            $pares[] = $numero;
        }
    }
    return $pares;
}

// Calcular la suma de los números
function calcularSuma($numeros) {
    $suma = 0;
    foreach ($numeros as $numero) {
        $suma += $numero;
    }
    return $suma;
}

// 3. Ejecución del programa
echo "<h3>Números iniciales:</h3>";
generarNumeros($numeros); // Llenar el array con números
mostrarNumeros($numeros); // Mostrar los números generados

echo "<h3>Números ordenados (descendente):</h3>";
$numerosOrdenados = ordenarNumeros($numeros, 'desc'); // Ordenar los números
mostrarNumeros($numerosOrdenados); // Mostrar los números ordenados

echo "<h3>Números pares:</h3>";
$numerosPares = filtrarPares($numeros); // Filtrar los números pares
mostrarNumeros($numerosPares); // Mostrar los números pares

echo "<h3>Suma de los números:</h3>";
$suma = calcularSuma($numeros); // Calcular la suma
echo "La suma es: $suma<br>";

if ($suma > 50) {
    echo "La suma es alta.";
} else {
    echo "La suma es baja.";
}
?>
