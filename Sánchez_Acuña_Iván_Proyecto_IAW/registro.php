<?php
header('Content-Type: text/html; charset=UTF-8');
require 'db_connection.php';

// Verifica si hay una conexión activa con la base de datos
if ($conn->connect_error) {
    die("Error de conexión con la base de datos: " . $conn->connect_error);
}

// Variable para errores o mensajes de éxito
$error = $success = "";

// Si el formulario se envió por método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitiza y obtiene los valores del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Verifica si hay algún campo vacío
    if (empty($nombre) || empty($correo) || empty($password)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        // Validar el formato del correo con expresión regular
        // Permite letras y números en el correo antes del @, seguido de un dominio con letras y puntos
        // Asegura que al menos haya una letra en la parte local del correo (antes del @)
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9._%+-]*@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $correo)) {
            $error = "Formato de correo inválido. El correo debe contener al menos una letra en la parte local.";
        } else {
            // Validar la contraseña con requisitos mínimos
            if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,16}$/', $password)) {
                $error = "La contraseña debe tener entre 8 y 16 caracteres, incluir mayúsculas, minúsculas, números y símbolos.";
            } else {
                // Verificar si el correo ya está registrado
                $stmt_check = $conn->prepare("SELECT ID FROM USUARIOS WHERE CORREO = ?");
                $stmt_check->bind_param("s", $correo);
                $stmt_check->execute();
                $stmt_check->store_result();

                if ($stmt_check->num_rows > 0) {
                    $error = "El correo ya está registrado.";
                } else {
                    // Cifrar la contraseña antes de guardarla
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    
                    // Insertar el usuario en la base de datos (siempre como usuario simple)
                    $stmt = $conn->prepare("INSERT INTO USUARIOS (NOMBRE, CORREO, CONTRASENA, ADMIN) VALUES (?, ?, ?, 0)");
                    $stmt->bind_param("sss", $nombre, $correo, $hashed_password);

                    if ($stmt->execute()) {
                        $success = "Usuario registrado exitosamente.";
                    } else {
                        $error = "Error al registrar el usuario.";
                    }
                    $stmt->close();
                }
                $stmt_check->close();
            }
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1>Registro de Usuario</h1>

        <!-- Mensaje de éxito o error -->
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <!-- Formulario de Registro -->
        <form action="registro.php" method="POST" id="registro-form" novalidate>
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required>

            <label for="correo">Correo:</label>
            <input type="email" name="correo" id="correo" required>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" required>

            <!--  Lista con los requisitos de la contraseña -->
            <ul class="requirements" id="password-requirements">
                <li id="req-length">Entre 8 y 16 caracteres</li>
                <li id="req-uppercase">Al menos una letra mayúscula</li>
                <li id="req-lowercase">Al menos una letra minúscula</li>
                <li id="req-number">Al menos un número</li>
                <li id="req-special">Al menos un carácter especial</li>
            </ul>

            <button type="submit">Registrar</button>
        </form>
        <p>¿Ya tienes cuenta? <a href="login.php">Iniciar Sesión</a></p>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const passwordInput = document.getElementById('password');
            const requirements = {
                length: document.getElementById('req-length'),
                uppercase: document.getElementById('req-uppercase'),
                lowercase: document.getElementById('req-lowercase'),
                number: document.getElementById('req-number'),
                special: document.getElementById('req-special')
            };

            passwordInput.addEventListener('input', function () {
                let value = passwordInput.value.trim();

                requirements.length.classList.toggle('valid', value.length >= 8 && value.length <= 16);
                requirements.length.classList.toggle('invalid', !(value.length >= 8 && value.length <= 16));

                requirements.uppercase.classList.toggle('valid', /[A-Z]/.test(value));
                requirements.uppercase.classList.toggle('invalid', !/[A-Z]/.test(value));

                requirements.lowercase.classList.toggle('valid', /[a-z]/.test(value));
                requirements.lowercase.classList.toggle('invalid', !/[a-z]/.test(value));

                requirements.number.classList.toggle('valid', /\d/.test(value));
                requirements.number.classList.toggle('invalid', !/\d/.test(value));

                requirements.special.classList.toggle('valid', /[\W]/.test(value));
                requirements.special.classList.toggle('invalid', !/[\W]/.test(value));
            });
        });
    </script>
</body>
</html>
