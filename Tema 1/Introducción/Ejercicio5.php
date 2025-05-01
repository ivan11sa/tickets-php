<?php
// Variables entero y numero
$texto = "El número es: ";
$numero = 42;

// Concatenación
$resultado = $texto . $numero;

// Resultado
echo $resultado . "<br>";

// Verificar el tipo de la variable 
$tipo = gettype($resultado);

echo "El tipo de la variable resultante es: " . $tipo;
?>
