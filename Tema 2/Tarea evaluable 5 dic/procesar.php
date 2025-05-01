<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        /* Estilo general del cuerpo */
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

        /* Contenedor central */
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

        /* Estilo para el círculo de carga */
        .loading {
            margin: 20px auto;
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3; /* Color de fondo */
            border-top: 5px solid #3498db; /* Color del círculo */
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 9999; /* Asegura que esté por encima de otros contenidos */
        }

        /* Animación del círculo */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Estilo para ocultar el contenido mientras carga */
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <?php

        $usuario_correcto = 'Iván';
        $contraseña_correcta = '12345';
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = $_POST['usuario'];
            $clave = $_POST['clave'];

            if ($usuario === $usuario_correcto && $clave === $contraseña_correcta) {
                setcookie('usuario', $usuario, time() + 3600, '/');
                $_SESSION['clave'] = $clave;
                echo "<div id='loading' class='loading'></div>";
                                echo "<script>
                                    setTimeout(function() {
                                        window.location.href = 'bienvenida.php';
                                    }, 5000); // Redirige después de 5 segundos
                                </script>";
                exit();
            } else {
                header("Location: index.php?error=1");
                exit();
            }
        }

        function visitas() {
        
        if (isset($_COOKIE['numero_visitas'])) {
            $visitas = $_COOKIE['numero_visitas'] + 1;
        } else {
            $visitas = 1; 
        }

        setcookie('numero_visitas', $visitas, time() + (86400 * 30), "/"); 
        return $visitas;
        }
    ?>
</body>
</html>