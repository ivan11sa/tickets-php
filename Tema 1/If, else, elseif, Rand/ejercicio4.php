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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lanzamiento de dados</title>
</head>
<body>
    <h1>Resultado del Lanzamiento de dado</h1>
    <p>Resultado: <?php echo $resultado; ?></p>
    <img src="<?php echo $imagenMostrar; ?>" alt="<?php echo $resultado; ?>" style="width: 200px; height: 200px;">
</body>
</html>