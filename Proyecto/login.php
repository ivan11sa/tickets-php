<?php
session_start();

// Redirigir al dashboard si el usuario ya está logueado
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Manejo de mensajes de error o éxito
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <style>
        body {
            background: url('background.webp') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Arial', sans-serif;
            color: #333;
        }
        .container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px;
            max-width: 400px;
            margin: 50px auto;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);
            text-align: center; /* Centra todo el contenido del contenedor */
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            text-align: center;
            color: #007BFF;
        }
        label {
            font-weight: bold;
            width: 100%;
            text-align: left; /* Asegura que los labels estén alineados a la izquierda */
        }

        input {
            width: 90%; /* Ajusta el ancho del input */
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 95%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .link {
            text-align: center;
            margin-top: 10px;
        }
        .link a {
            text-decoration: none;
            color: #007BFF;
        }
        .link a:hover {
            text-decoration: underline;
        }
        .error, .success {
            text-align: center;
            font-size: 14px;
            margin-top: 10px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Iniciar Sesión</h1>
        
        <!-- Mensajes de error o éxito -->
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Formulario de inicio de sesión -->
        <form action="validate.php" method="POST">
            <label for="email">Correo:</label>
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <label for="password">Contraseña:</label>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Iniciar Sesión</button>
        </form>

        <!-- Enlace para registrar usuario -->
        <div class="link">
            <p>¿Aún no estás registrado? <a href="registro.php">Crear usuario</a></p>
        </div>
    </div>
</body>
</html>
