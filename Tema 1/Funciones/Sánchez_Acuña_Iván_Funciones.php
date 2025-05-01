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

        function ejecutarEjercicio1() {
            $array = arrayAleatorio(10, 1, 30);
            mostrarArray($array);
        }

        ejecutarEjercicio1();

    ?> 

    <h1>Ejercicio 2</h1>

    <?php

        function calcularMedia($array) {
            $suma = array_sum($array);
            $numero = count($array);
            return $suma / $numero;
        }

        function ejecutarEjercicio2() {

            $array = arrayAleatorio(10, 1, 30);
            mostrarArray($array);
            

            $media = calcularMedia($array);
            echo "Valor medio: " . $media;
        }
        
        ejecutarEjercicio2();
        
    ?>

    <h1>Ejercicio 3</h1>

    <?php

        function calcularMaximo ($array) {
            $maximo = max($array);
            return $maximo;
        }

        function ejecutarEjercicio3() {

            $array = arrayAleatorio(10, 1, 30);
            mostrarArray($array);
            

            $maximo = calcularMaximo($array);
            echo "Valor máximo: " . $maximo;
        }
        
        ejecutarEjercicio3();

    ?>

    <h1>Ejercicio 4</h1>

       <?php

        function calcularMinimo ($array) {
            $minimo = min($array);
            return $minimo;
        }

        function ejecutarEjercicio4() {

            $array = arrayAleatorio(10, 1, 30);
            mostrarArray($array);
            

            $minimo= calcularMinimo($array);
            echo "Valor mínimo: " . $minimo;
        }
        
        ejecutarEjercicio4();

    ?>

</body>
</html>