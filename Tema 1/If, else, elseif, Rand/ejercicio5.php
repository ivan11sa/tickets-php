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

