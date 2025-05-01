<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=10">
    <title>Document</title>
</head>
<body>
<h1> Ejercicio 1 </h1>
 <?php

//calculos

$a = 2 * 3 + 5;
$b = 5 + 2 * 3;
$c = 1 + 4 * 4 + 6;
$d = 8 / 2 * 3 / 6; 
$e = 6 / 3 * 8 / 2;
$f = 5 + 6 / 2 + 3;
$g = (3 + 2) * (8 + 2);
$h = 5 + (3 * 8) + 1;
$i = 3 + 4 * (8 + 2);

// Muestra de resultados
echo "a) El resultado es $a <br>"; 
echo "b) El resultado es $b <br>";
echo "c) El resultado es $c <br>";
echo "d) El resultado es $d <br>";
echo "e) El resultado es $e <br>";
echo "f) El resultado es $f <br>";
echo "g) El resultado es $g <br>";
echo "h) El resultado es $h <br>";
echo "i) El resultado es $i <br>";

?>   

<h1>Ejercicio 2</h1>

<?php

$a = 6;
$b = 8;
$PVP = 7;
$resultado_a = ($a + $b) / $PVP + 2;
echo "a) El resultado es: $resultado_a <br>";

$IVA = 7;
$Tasa = 3;
$PVP = 2;
$resultado_b = $IVA * $IVA - 4 * $Tasa * $PVP;
echo "b) El resultado es: $resultado_b <br>";

$a = 3;
$b = 6;
$c = 4;
$resultado_c = ($a + 7 + $c) / ($b + 2 - $a) + 2 * $b;
echo "c) El resultado es: $resultado_c <br>";

$a = 3;
$b = 6;
$resultado_d = ($a + 5) + 3 / 2 * $b - $b;
echo "d) El resultado es: $resultado_d <br>";
?>

<h1>Ejercicio 3</h1>

<?php

$resultado_a = 6 * (25 - 3);

echo "a) El resultado es: $resultado_a <br>";

$resultado_b = ((320 + 8) / 8 )/ 8;

echo "b) El resultado es: $resultado_b <br>";

$resultado_c = (9 * 15) - (24 / 4);

echo "c) El resultado es: $resultado_c <br>";

$resultado_d = (12 - 5) / (2 * 3);

#Otro resultado para redondear puede ser el siguiente $resultado-d = round((12 - 5) / (2 * 3),2) el último 2 señala la cantidad de decimales. 

echo "d) El resultado es: $resultado_d <br>";


?>

<h1>Ejercicio 4</h1>

<?php

$resultado_a = (3 + (8-2)-4)/6;

echo "a) El resultado es: $resultado_a <br>";

$resultado_b = 5*(75/15)+4*(4-1)+2*(7+4);

echo "b) El resultado es: $resultado_b <br>";

$resultado_c = (15/(8-3)+4)*(6+2)*2;

echo "c) El resultado es: $resultado_c <br>";

$resultado_d = (8+3)*(40-(7*4));

echo "d) El resultado es: $resultado_d <br>";


?>

<h1>Ejercicio 8</h1>

<?php

// Definimos las variables

$A = 3;
$B = -4;
$C = 8;

// Realizamos las operaciones

$resultado_a = $A + $B * $C;

echo "a) El resultado es: $resultado_a <br>";

$resultado_b = (pow($B, (6+$B)))/($C+$A);

echo "a) El resultado es: $resultado_b <br>";

?>

<h1>Ejercicio 9</h1>

<?php

// Definimos las variables

$A = 2;
$B = 5;
$C = 2;

// Realizamos las operaciones

$resultado_a = 3 * $A - 4 * $B / pow($A, 2);

echo "a) El resultado es: $resultado_a <br>";

$resultado_b = pow ($B, 2) - 4 * $A * $C;

echo "a) El resultado es: $resultado_b <br>";

$resultado_c = ( -$B + pow((pow($B, 2) -4 * $A * $C), (1/2)))/2 * $A;

echo "a) El resultado es: $resultado_c <br>";

?>

<h1>Ejercicio 10</h1>

<?php

$A = 15;
$B = -3;
$C = 8;


$suma = $A + $B;


$division_C = $C / 2;


$resultado = $suma > $division_C;


echo "A + B:  $suma <br>";
echo "C / 2: $division_C <br>";
echo "¿A + B es mayor que C / 2? $resultado ";
?>

<h1>Ejercicio 11</h1>

<?php

$resultado_a = -25 % 7;
$resultado_b = 25 % 7;
$resultado_c = 5 % 7;
$resultado_d = -45 % 7;

echo "-25 % 7 =  $resultado_a <br>"; 
echo "25 % 7 =  $resultado_b <br>"; 
echo "5 % 7 =  $resultado_c <br>";   
echo "-45 % 7 =  $resultado_d <br>"; 

?>

<h1>Ejercicio 12</h1>

<?php

$A = 6;
$B = 2;
$C = 3;

$resultado_a = $A - $B + $C;
$resultado_b = ($A * $B) % $C;
$resultado_c = $A + ($B % $C);
$resultado_d = ($A % $B) * $C;

echo "A - B + C =   $resultado_a  <br>";
echo "A * B % C =   $resultado_b  <br>";
echo "A + B % C =  $resultado_c  <br>";
echo "A % B * C =   $resultado_d  <br>";

?>

<h1>Ejercicio 13</h1>

<?php
$A = 5;
$B = $A + 6;
$A = $A + 1;
$B = $A - 5;

echo "Valor de A:  $A <br>";
echo "Valor de B:  $B <br>";
?>

<h1>Ejercicio 14</h1>

<?php
$A = 3;
$B = 20;
$C = $A + $B;
$B = $A + $B;
$A = $B;

echo "Valor de A: $A <br>";
echo "Valor de B: $B <br>";
echo "Valor de C: $C <br>";
?>

<h1>Ejercicio 15</h1>

<?php

$A = 10;
$B = 5;
$A = $B;
$B = $A;

echo "Valor de A: $A <br>";
echo "Valor de B: $B <br>";
?>

<h1>Ejercicio 16</h1>

<?php

$expresion_a = ($m + 4) / 4;
$expresion_b = ($m + $n) / ($p - $q);
$expresion_c = (sin($x) + cos($x)) / tan($x) ;
$expresion_d = ($m + 4) / ($p - $q);
$expresion_e = ($m + $n / $p) / ($q - $r / 5);
$expresion_f = (-$b + pow($b, 2) - (4 * $a * $c))/(2 * $a);

 
?>


</body>
</html>
