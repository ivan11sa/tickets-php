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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lanzamiento de Moneda</title>
</head>
<body>
    <h1>Resultado del Lanzamiento de Moneda</h1>
    <p>Resultado: <?php echo $resultado; ?></p>
    <img src="<?php echo $imagenMostrar; ?>" alt="<?php echo $resultado; ?>" style="width: 200px; height: 200px;">
</body>
</html>