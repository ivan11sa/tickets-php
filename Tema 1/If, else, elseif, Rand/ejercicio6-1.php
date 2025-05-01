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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lanzamiento de dados</title>
</head>
<body>
    <h1>Resultado del Lanzamiento de dado</h1>
    <p>Resultado: <?php echo $dado; ?></p>
    <img src="<?php echo $imagenes[$dado]; ?>" alt="<?php echo $dado; ?>" style="width: 200px; height: 200px;">
</body>
</html>