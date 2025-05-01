<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Ejercicio 6</h1>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required>
        <br>
        <button type="submit">Iniciar sesión</button>
    </form>

    <?php
        
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nombre = $_POST['nombre'];
            $password = $_POST['password'];

            if ($nombre === 'Iván' && $password === '1234') {
                setcookie('nombre_usuario', $nombre, time() + (86400 * 30), "/"); // Guardar en cookie por 30 días
                echo "Bienvenido a la sesión $nombre";
            } else {
                echo "Nombre de usuario o contraseña incorrectos.";
            }
        }

    ?>


    
</body>
</html>