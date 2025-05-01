<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lanzamiento de Moneda</title>
</head>
<body>
    <h1>Ejercicio 1</h1>
    
    <?php

        $n = rand (0, 1);
        $cara = "Cara.jpg";
        $cruz = 'cruz.jpg';

        if  ($n == 0 ) {
            $imagenMostrar = $cara;
            $resultado = "Cara";
        }else {   
            $imagenMostrar = $cruz;
            $resultado = "Cruz";
        }


    ?>
    <p>Resultado: <?php echo $resultado; ?></p>
    <img src="<?php echo $imagenMostrar; ?>" alt="<?php echo $resultado; ?>" style="width: 200px; height: 200px;">

    <h1>Ejercicio 2</h1>
    <?php

        $n = rand (0, 10);

            if ( $n < 5 ) {
                echo "La nota es $n , por lo tanto el resultado es insuficiente";
            } elseif ( $n < 6 ) {
                echo "La nota es $n , por lo tanto el resultado es suficiente";
    
            } elseif ( $n < 7 ) {
                echo "La nota es $n , por lo tanto el resultado es bien";
    
            } elseif ( $n < 9 ) {
                echo "La nota es $n , por lo tanto el resultado es notable";
    
            } else {
                echo "La nota es $n, por lo tanto el resultado es sobresaliente";
            }           


    ?>

    <h1>Ejercicio 3</h1>

    <?php

        $n = rand(1, 7);


            switch ($n) {
                case 1:
                    echo "Estamos a Lunes";
                    break;
                case 2:
                    echo "Estamos a Martes";
                    break;
                case 3:
                    echo "Estamos a Miércoles";
                    break;
                case 4:
                    echo "Estamos a Jueves";
                    break;
                case 5:
                    echo "Estamos a Viernes";
                    break;
                case 6:
                    echo "Estamos a Sábado";
                    break;
                default:
                    echo "Estamos a Domingo";
            }

    ?>

    <h1>Ejercicio 4</h1>

    <?php

        $n = rand (1, 6);
        $dado1 = "dado1.png";
        $dado2 = "dado2.png";
        $dado3 = "dado3.png";
        $dado4 = "dado4.png";
        $dado5 = "dado5.png";
        $dado6 = "dado6.png";

        if  ($n == 1 ) {
            $imagenMostrar = $dado1;
            $resultado = "1";
        }elseif ( $n == 2) {   
            $imagenMostrar = $dado2;
            $resultado = "2";
        } elseif ( $n == 3) {   
            $imagenMostrar = $dado3;
            $resultado = "3";
        } elseif ( $n == 4) {   
            $imagenMostrar = $dado4;
            $resultado = "4";
        } elseif ( $n == 5) {   
            $imagenMostrar = $dado5;
            $resultado = "5";
        } else {   
            $imagenMostrar = $dado6;
            $resultado = "6";
        }


    ?>

    <p>Resultado del lanzamiento del dado: <?php echo $resultado; ?></p>
    <img src="<?php echo $imagenMostrar; ?>" alt="<?php echo $resultado; ?>" style="width: 200px; height: 200px;">

    <h1>Ejercicio 5</h1>

    <?php

        $n = rand(1, 6);
        $dado1 = "dado1.png";
        $dado2 = "dado2.png";
        $dado3 = "dado3.png";
        $dado4 = "dado4.png";
        $dado5 = "dado5.png";
        $dado6 = "dado6.png";


        switch ($n) {
            case 1:
                $imagenMostrar = $dado1;
                $resultado = 1;
                break;
            case 2:
                $imagenMostrar = $dado2;
                $resultado = 2;
                break;
            case 3:
                $imagenMostrar = $dado3;
                $resultado = 3;
                break;
            case 4:
                $imagenMostrar = $dado4;
                $resultado = 4;
                break;
            case 5:
                $imagenMostrar = $dado5;
                $resultado = 5;
                break;
            case 6:
                $imagenMostrar = $dado6;
                $resultado = 6;
                break;

        }

    ?>

    <p>Resultado del lanzamiento del dado: <?php echo $resultado; ?></p>
    <img src="<?php echo $imagenMostrar; ?>" alt="<?php echo $resultado; ?>" style="width: 200px; height: 200px;">

    <h1>Ejercicio 6</h1>

    <?php

        $dado = rand(1, 6);

        $imagenes = [
            1 => 'dado1.png',
            2 => 'dado2.png',
            3 => 'dado3.png',
            4 => 'dado4.png',
            5 => 'dado5.png',
            6 => 'dado6.png'
        ];

    ?>


    <p>Resultado del lanzamiento del dado: <?php echo $dado; ?></p>
    <img src="<?php echo $imagenes[$dado]; ?>" alt="<?php echo $dado; ?>" style="width: 200px; height: 200px;">


    <h1>Ejercicio 7</h1>

    <?php

        $n = rand(1, 6);
        $m = rand(1, 6);
        $dado1 = "dado1.png";
        $dado2 = "dado2.png";
        $dado3 = "dado3.png";
        $dado4 = "dado4.png";
        $dado5 = "dado5.png";
        $dado6 = "dado6.png";


        switch ($n) {
            case 1:
                $imagenMostrar1 = $dado1;
                $resultado1 = 1;
                break;
            case 2:
                $imagenMostrar1 = $dado2;
                $resultado1 = 2;
                break;
            case 3:
                $imagenMostrar1 = $dado3;
                $resultado1 = 3;
                break;
            case 4:
                $imagenMostrar1 = $dado4;
                $resultado1 = 4;
                break;
            case 5:
                $imagenMostrar1 = $dado5;
                $resultado1 = 5;
                break;
            case 6:
                $imagenMostrar1 = $dado6;
                $resultado1 = 6;
                break;

        }

        switch ($m) {
            case 1:
                $imagenMostrar2 = $dado1;
                $resultado2 = 1;
                break;
            case 2:
                $imagenMostrar2 = $dado2;
                $resultado2 = 2;
                break;
            case 3:
                $imagenMostrar2 = $dado3;
                $resultado2 = 3;
                break;
            case 4:
                $imagenMostrar2 = $dado4;
                $resultado2 = 4;
                break;
            case 5:
                $imagenMostrar2 = $dado5;
                $resultado2 = 5;
                break;
            case 6:
                $imagenMostrar2 = $dado6;
                $resultado2 = 6;
                break;

        }

    ?>

    <p>Resultado del lanzamiento de los dados: <?php echo $resultado1; ?> y <?php echo $resultado2; ?></p>
    <img src="<?php echo $imagenMostrar1; ?>" alt="<?php echo $resultado1; ?>" style="width: 200px; height: 200px;">
    <img src="<?php echo $imagenMostrar2; ?>" alt="<?php echo $resultado2; ?>" style="width: 200px; height: 200px;">


</body>
</html>

