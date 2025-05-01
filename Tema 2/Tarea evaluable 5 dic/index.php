<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de inicio de sesión</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #a8e6a1, #34a853); 
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        form {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
            border: 2px solid #34a853;
        }

        form input[type="text"],
        form input[type="password"] {
            width: 100%;
            padding: 0px;
            margin: 10px 0;
            border: 2px solid #34a853;
            border-radius: 4px;
            font-size: 14px;
            color: #555;
        }

        form input[type="text"]:focus,
        form input[type="password"]:focus {
            border-color: #1b9b51; 
            outline: none;
        }

        form button {
            width: 100%;
            padding: 10px;
            background-color: #34a853; 
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #1b9b51; 
        }

        form button:active {
            background-color: #2c8e3a; 
        }

        h2 {
            color: #34a853;
            font-size: 24px;
            margin-bottom: 20px;
        }

    </style>
</head>
<body>

        <?php
            if (isset($_GET['error']) && $_GET['error'] == 1) {
                echo '<div class="error">Usuario o contraseña incorrectos.</div>';
            }
        ?>

    <form method="POST" action="procesar.php">
        <h2>Iniciar sesión</h2>
        Usuario: <input type="text" name="usuario" required>
        Contraseña: <input type="password" name="clave" required>
        <button type="submit">Iniciar sesión</button>
    </form>
    
</body>
</html>
