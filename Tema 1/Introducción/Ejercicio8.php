<?php
// Variables
$numero_entero = 10;
$numero_decimal = 5.5;
$cadena = "20";
$booleano = true;

// Operaciones
$resultado_suma = $numero_entero + $numero_decimal;
$resultado_resta = $resultado_suma - $cadena;
$resultado_multiplicacion = $resultado_resta * $booleano;
$resultado_division = $resultado_multiplicacion / $numero_entero;

// Resultados y tipos
echo "Resultado de la suma: " . $resultado_suma . ", Tipo: " . gettype($resultado_suma) . "<br>";
echo "Resultado de la resta: " . $resultado_resta . ", Tipo: " . gettype($resultado_resta) . "<br>";
echo "Resultado de la multiplicación: " . $resultado_multiplicacion . ", Tipo: " . gettype($resultado_multiplicacion) . "<br>";
echo "Resultado de la división: " . $resultado_division . ", Tipo: " . gettype($resultado_division) . "<br>";
?>
