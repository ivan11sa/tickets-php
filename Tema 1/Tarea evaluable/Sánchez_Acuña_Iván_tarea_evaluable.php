<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<h1><b>Ejercicio 1: Numeros pares e impares</b></h1>
<h3>Crea un script que imprima los números del 1 al 20 y determine si cada número es par o impar</h3>
    <?php
        for ($i=1; $i <= 20; $i++) {
            if ($i % 2 == 0){
                echo "El número $i es par</br>";
            } else {
                echo "El número $i es impar</br>";
            }

        }

    ?>

<h1><b>Ejercicio 2: Números Primos</b></h1>
<h3>Crea un script que imprima todos los números primos entre 1 y 200 utilizando bucles y estructuras
de control</h3>

<?php
        for ($num = 1; $num <= 200; $num++) {
            $primo = true;

        for ($i=2; $i <= $num / 2; $i++) {
            if ($num % $i == 0 ) {
                $primo = false;
                break;
                
            }
        }

        if($primo) {
            echo "El número $num es un número primo </br>";
        }

    }

    ?>
</body>
</html>