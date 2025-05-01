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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lanzamiento de dados</title>
</head>
<body>
    <h1>Resultado del Lanzamiento de dado</h1>
    <p>Resultado: <?php echo $resultado1; ?> y <?php echo $resultado2; ?></p>
    <img src="<?php echo $imagenMostrar1; ?>" alt="<?php echo $resultado1; ?>" style="width: 200px; height: 200px;">
    <img src="<?php echo $imagenMostrar2; ?>" alt="<?php echo $resultado2; ?>" style="width: 200px; height: 200px;">
</body>
</html>