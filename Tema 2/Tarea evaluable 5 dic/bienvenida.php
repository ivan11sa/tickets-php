<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #a8e6a1, #34a853); 
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #fff;
            text-align: center;
        }

        .container {
            background-color: #ffffff;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
        }

        .container h1 {
            color: #34a853;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .message {
            color: #34a853;
            font-size: 18px;
            margin: 10px 0;
        }

        .button-container {
            margin-top: 20px;
        }

        .button-container button {
            padding: 10px 20px;
            background-color: #34a853;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .button-container button:hover {
            background-color: #1b9b51; 
        }

        .button-container button:active {
            background-color: #2c8e3a; 
        }

    </style>
</head>
<body>

    <div class="container">
        <?php
            include 'procesar.php';
            

            $usuario = $_COOKIE['usuario'];
            $contraseña_cifrada = md5($contraseña);
            echo "<div class='message'>Bienvenido " .  $usuario . ", has entrado utilizando la contraseña cifrada $contraseña_cifrada</div>";

            $visitas= visitas(); 
            echo "<div class='message'>Has visitado esta página $visitas veces.</div>";

        ?>

        <div class="button-container">
            <form method="POST" action="index.php">
                <button type="submit" name="logout">Cerrar sesión</button>
            </form>
        </div>

        <?php
            if (isset($_POST['logout'])) {
                setcookie('usuario', '', time() - 3600, '/');
                session_destroy();  
                header("Location: index.php");
                exit();
            }

        ?>


    </div>
    
</body>
</html>
