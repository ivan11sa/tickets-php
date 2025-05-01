<h1>Ejercicio 1</h1>
<?php
    $cookie_name = "Ivan";
    $cookie_value = "Ivan";
    setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
        if(!isset($_COOKIE[$cookie_name])) {
            echo "Cookie named '" . $cookie_name . "' is not set!";
          } else {
            echo "Hola," .$cookie_name . "!";
          }
    ?>

    <h1>Ejercicio 2</h1>

    <?php

        if (isset($_COOKIE['numero_visitas'])) {
            $visitas = $_COOKIE['numero_visitas'] + 1;
        } else {
            $visitas = 1; 
        }

        setcookie('numero_visitas', $visitas, time() + (86400 * 30), "/"); 


        echo "Bienvenido de nuevo, esta es tu " . $visitas . " visita.";
    ?>

    <h1>Ejercicio 3</h1>

    <?php

        session_start(); // 
        
        
        if (!isset($_SESSION['nombre_usuario'])) {
            $_SESSION['nombre_usuario'] = 'Iván'; 
        }
        
        echo "Hola, " . $_SESSION['nombre_usuario'] . "!";
        
    ?>

    <h1>Ejercicio 4</h1>

    <?php
        
        if (isset($_SESSION['numero_visitas'])) {
            $_SESSION['numero_visitas'] += 1;
        } else {
            $_SESSION['numero_visitas'] = 1; 
        }

        echo "Bienvenido de nuevo, esta es tu " . $_SESSION['numero_visitas'] . " visita.";
    ?>

</body>
</html>