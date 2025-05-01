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
        function multiplicar($numero) {
            echo "Tabla de multiplicar de $numero: </br>";
            for ($i = 1; $i <= 10; $i++) {
                $resultado = $numero * $i;
                echo "$numero x $i = $resultado </br>";
            }
        }

        $numero = 6; 
        multiplicar($numero);
    ?>

    <h1>Ejercicio 2</h1>

    <?php
        function rango($inicio, $fin){
            if ($inicio > $fin) {
                return;
            }
            for ($numero = $inicio; $numero <= $fin; $numero++){
                multiplicar($numero);
            }
        }

        rango(3, 7);

    ?>

    <h1>Ejercicio 3</h1>

    <?php
        function inicializar_array($n, $min, $max){
            $array=[];
            for ($i=0; $i < $n; $i++) {
                $array[$i] = rand($min, $max);
            }
            return $array;
        }

        $array = inicializar_array(5, 1, 10);
        echo implode (", ", $array);

    ?>

    <h1>Ejercicio 4</h1>

    <?php
        function arrayAleatorio ($cantidad, $min, $max){

            $array = [];
                for ($i = 0; $i < $cantidad; $i++ ){
                    $array[$i] = rand($min, $max);
                }
                return $array;
                }
        
        function mostrarArray ($array) {
        
            echo "Valores del array: </br>";
        
            foreach ($array as $clave => $valor) {
                echo "Clave: " . $clave . " - Valor: " . $valor;
                echo "<br>";
            }
        }

        function calcular_media ($array) {
            $suma = array_sum($array);
            $numero = count($array);
            return $suma / $numero;
        }

        function ejecutarEjercicio4() {

            $array = arrayAleatorio(10, 1, 30);
            mostrarArray($array);
            

            $media = calcular_media($array);
            echo "Valor medio: " . $media;
        }
        
        ejecutarEjercicio4();
        
        
    ?>

<h1>Ejercicio 5</h1>

<?php

    function calcularMaximo ($array) {
        $maximo = max($array);
        return $maximo;
    }

    /* function maximo ($array) {
        $maxi = 0;
        for ($k = 0; $k >count($array); $k++) {
            if ($array[$k] > $maxi)
                $maxi = $array[$k]
        }
        
        return $maxi

    }
        Igual se hace con el mínimo pero al contrario */

    function ejecutarEjercicio5() {

        $array = arrayAleatorio(10, 1, 30);
        mostrarArray($array);
        

        $maximo = calcularMaximo($array);
        echo "Valor máximo: " . $maximo;
    }
    
    ejecutarEjercicio5();

?>

<h1>Ejercicio 6</h1>

   <?php

    function calcularMinimo ($array) {
        $minimo = min($array);
        return $minimo;
    }

    function ejecutarEjercicio6() {

        $array = arrayAleatorio(10, 1, 30);
        mostrarArray($array);
        

        $minimo= calcularMinimo($array);
        echo "Valor mínimo: " . $minimo;
    }
    
    ejecutarEjercicio6();

?>

    <h1>Ejercicio 7</h1>

    <?php
        function imprimir_array($array) {
            echo "<table border='1'>";
            echo "<tr><th>Posición</th><th>Valor</th></tr>";
    
            foreach ($array as $clave => $valor) {
                echo "<tr><td>$clave</td><td>$valor</td></tr>";
            }
    
            echo "</table>";
        }


        $array = [3, 7, 1, 10, 4];
        imprimir_array($array);
    ?>
</body>
</html>