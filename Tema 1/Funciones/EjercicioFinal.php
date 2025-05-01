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
        include 'funciones.php';
        
        $numero = 6; 
        multiplicar($numero);
    ?>  

    <h1>Ejercicio 2</h1>
    <?php
        rango(3, 7);
    ?>

    <h1>Ejercicio 3</h1>
    <?php
        $array = inicializar_array(5, 1, 10);
        print_r($array);
    ?>

    <h1>Ejercicio 4</h1>
    <?php
        $array = arrayAleatorio(10, 1, 30);
        mostrarArray($array);
        
        $media = calcular_media($array);
        echo "Valor medio: " . $media;
    ?>

    <h1>Ejercicio 5</h1>
    <?php
        $array = arrayAleatorio(10, 1, 30);
        mostrarArray($array);
        
        $maximo = calcularMaximo($array);
        echo "Valor máximo: " . $maximo;
    ?>

    <h1>Ejercicio 6</h1>
    <?php
        $array = arrayAleatorio(10, 1, 30);
        mostrarArray($array);
        
        $minimo = calcularMinimo($array);
        echo "Valor mínimo: " . $minimo;
    ?>

    <h1>Ejercicio 7</h1>
    <?php
        $array = [3, 7, 1, 10, 4];
        imprimir_array($array);
    ?>
</body>
</html>
