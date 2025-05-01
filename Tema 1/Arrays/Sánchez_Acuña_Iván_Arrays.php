<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Ejercicio 1</h1>

    <?php
$array = [];
for ($i = 0; $i < 10; $i++) {
    $array[] = rand(1, 30);
}

echo "Valores del array: </br>";


        foreach ($array as $clave => $valor) {
            echo "Clave: " . $clave . " - Valor: " . $valor;
            echo "<br>";
        }

    ?>

<h1>Ejercicio 2</h1>

<?php
$array = [];
for ($i = 0; $i < 10; $i++) {
    $array[] = rand(1, 30);
}

echo "Valores del array: </br>";


    foreach ($array as $clave => $valor) {
        echo "Clave: " . $clave . " - Valor: " . $valor;
        echo "<br>";

    }
    $suma = array_sum($array);
    $numero = count($array);
    $media = $suma / $numero;

        echo "Valor medio: " . $media;

?>

<h1>Ejercicio 3</h1>

<?php
$array = [];
for ($i = 0; $i < 10; $i++) {
    $array[] = rand(1, 30);
}

    foreach ($array as $clave => $valor) {
        echo "Clave: " . $clave . " - Valor: " . $valor;
        echo "<br>";

    }
    $maximo = max($array);
        echo "Valor máximo: " . $maximo;

?>

<h1>Ejercicio 4</h1>

<?php
$array = [];
for ($i = 0; $i < 10; $i++) {
    $array[] = rand(1, 30);
}

echo "Valores del array: </br>";


    foreach ($array as $clave => $valor) {
        echo "Clave: " . $clave . " - Valor: " . $valor;
        echo "<br>";

    }
    $minimo = min($array);
        echo "Valor mínimo: " . $minimo;

?>

<h1>Ejercicio temperaturas con for </h1>

<?php

$array = [];
for ($i = 0; $i < 10; $i++) {
    $array[] = rand(1, 30);
}

echo "Valores del array: </br>";

    foreach ($array as $clave => $valor) {
        echo "Clave: " . $clave . " - Valor: " . $valor;
        echo "<br>";

    }

    $suma = array_sum($array);
    $numero = count($array);
    $media = $suma / $numero;

        echo "Valor medio: " . $media . "</br>";

    $maximo = max($array);
        echo "Valor máximo: " . $maximo . "</br>";

    $minimo = min($array);
        echo "Valor mínimo: " . $minimo . "</br>";

?>

<h1>Ejercicio temperaturas con while </h1>

<?php

$array = [];
$i=0;
while ($i < 10) {
    $array[] = rand(1, 30);
    $i++;
}

echo "Valores del array: </br>";

    foreach ($array as $clave => $valor) {
        echo "Clave: " . $clave . " - Valor: " . $valor;
        echo "<br>";

    }

    $suma = array_sum($array);
    $numero = count($array);
    $media = $suma / $numero;

        echo "Valor medio: " . $media . "</br>";

    $maximo = max($array);
        echo "Valor máximo: " . $maximo . "</br>";

    $minimo = min($array);
        echo "Valor mínimo: " . $minimo . "</br>";

?>

<h1>Ejercicio temperaturas con do-while </h1>

<?php

$array = [];
$i=0;
 do {
    $array[] = rand(1, 30);
    $i++;
}while ($i < 10);

echo "Valores del array: </br>";

    foreach ($array as $clave => $valor) {
        echo "Clave: " . $clave . " - Valor: " . $valor;
        echo "<br>";

    }

    $suma = array_sum($array);
    $numero = count($array);
    $media = $suma / $numero;

        echo "Valor medio: " . $media . "</br>";

    $maximo = max($array);
        echo "Valor máximo: " . $maximo . "</br>";

    $minimo = min($array);
        echo "Valor mínimo: " . $minimo . "</br>";

?>

<h1>Ejercicio 8</h1>
 <?php

$temperaturas = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtenemos el número de elementos del formulario
    $numTemperaturas = (int)$_POST['numTemperaturas'];

    // Generamos el array de temperaturas aleatorias
    for ($i = 0; $i < $numTemperaturas; $i++) {
        $temperaturas[] = rand(1, 30); // Temperaturas entre 1 y 30
    }

    // Ordenamos el array de mayor a menor
    $temperaturasMayorAMenor = $temperaturas;
    rsort($temperaturasMayorAMenor);

    // Ordenamos el array de menor a mayor
    $temperaturasMenorAMayor = $temperaturas;
    sort($temperaturasMenorAMayor);
}


 ?>

<h1>Generador de Temperaturas Aleatorias</h1>
    <form method="post">
        <label for="numTemperaturas">Número de Temperaturas:</label>
        <input type="number" id="numTemperaturas" name="numTemperaturas" min="1" required>
        <input type="submit" value="Generar">
    </form>

    <?php if (!empty($temperaturas)): ?>
        <h2>Temperaturas Generadas:</h2>
        <p><?php echo implode(", ", $temperaturas); ?></p>

        <h2>Listado Ordenado de Mayor a Menor:</h2>
        <p><?php echo implode(", ", $temperaturasMayorAMenor); ?></p>

        <h2>Listado Ordenado de Menor a Mayor:</h2>
        <p><?php echo implode(", ", $temperaturasMenorAMayor); ?></p>
    <?php endif; ?>


<h1>Ejercicio 9</h1>

<h4>Personas ordenadas de menor a mayor por su edad </h4>
<?php

$personas = array("Antonio"=>"31", "María"=>"28", "Juan"=>"29", "Pepe"=>"27");
asort($personas);
foreach ($personas as $clave => $valor) {
   echo  "La persona es: " . $clave . " y tienen " . $valor . " </br>";

}

?>

<h4>Personas ordenadas de mayor a menor por su edad </h4>
<?php

$personas = array("Antonio"=>"31", "María"=>"28", "Juan"=>"29", "Pepe"=>"27");
arsort($personas);
foreach ($personas as $clave => $valor) {
   echo  "La persona es: " . $clave . " y tienen " . $valor . " </br>";

}

?>

<h4>Personas ordenadas alfabeticamente de forma ascendente </h4>
<?php

$personas = array("Antonio"=>"31", "María"=>"28", "Juan"=>"29", "Pepe"=>"27");
ksort($personas);
foreach ($personas as $clave => $valor) {
   echo  "La persona es: " . $clave . " y tienen " . $valor . " </br>";

}

?>

<h4>Personas ordenadas alfabeticamente de forma descendente </h4>
<?php

$personas = array("Antonio"=>"31", "María"=>"28", "Juan"=>"29", "Pepe"=>"27");
krsort($personas);
foreach ($personas as $clave => $valor) {
   echo  "La persona es: " . $clave . " y tienen " . $valor . " </br>";

}

?>

<h1>Ejercicio 10</h1>

<?php

$ciudades = array("Italy"=>"Rome", "Luxembourg"=>"Luxembourg", "Belgium"=> "Brussels",
"Denmark"=>"Copenhagen", "Finland"=>"Helsinki", "France" => "Paris",
"Slovakia"=>"Bratislava", "Slovenia"=>"Ljubljana", "Germany" => "Berlin",
"Greece" => "Athens", "Ireland"=>"Dublin", "Netherlands"=>"Amsterdam",
"Portugal"=>"Lisbon", "Spain"=>"Madrid", "Sweden"=>"Stockholm", "United
Kingdom"=>"London", "Cyprus"=>"Nicosia", "Lithuania"=>"Vilnius", "Czech
Republic"=>"Prague", "Estonia"=>"Tallin", "Hungary"=>"Budapest",
"Latvia"=>"Riga", "Malta"=>"Valetta", "Austria" => "Vienna",
"Poland"=>"Warsaw");

ksort($ciudades);
foreach ($ciudades as $clave => $valor) {
    $ciudadm = strtoupper($clave);
    $capitalm = strtoupper($valor);
    echo  "La capital de " . $ciudadm . " es " . $capitalm . " </br>";

}



?>
</body>
</html>