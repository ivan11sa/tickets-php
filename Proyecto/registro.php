<?php
header('Content-Type: text/html; charset=UTF-8');
require_once 'db_connection.php';

// Verificar conexión a la base de datos
if ($conn->connect_error) {
    header("Location: registro.php?error=Error de conexión con la base de datos.");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);
    $rol = trim($_POST['rol']); // usuario_simple o responsable

    // Validaciones del lado del servidor
    if (empty($nombre) || empty($correo) || empty($password) || empty($rol)) {
        header("Location: registro.php?error=Por favor, complete todos los campos.");
        exit();
    }

    if (!in_array($rol, ['usuario_simple', 'responsable'])) {
        header("Location: registro.php?error=Rol inválido seleccionado.");
        exit();
    }

    // ✅ Validación de contraseña segura
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W]).{8,16}$/u', $password)) {
        header("Location: registro.php?error=La contraseña no cumple con los requisitos.");
        exit();
    }

    // Hashear la contraseña
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Asegurar que la tabla acepte el hash correctamente
    $sql_alter = "ALTER TABLE USUARIOS MODIFY CONTRASEÑA VARCHAR(255) COLLATE utf8mb4_bin;";
    $conn->query($sql_alter); // Se ejecuta sin detener el script si falla

    // Determinar el valor de ADMIN según el rol
    $is_admin = ($rol === 'responsable') ? 1 : 0;

    // Insertar en la base de datos
    $sql = "INSERT INTO USUARIOS (NOMBRE, CORREO, CONTRASENA, ADMIN) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        header("Location: registro.php?error=Error en la preparación de la consulta.");
        exit();
    }

    $stmt->bind_param("sssi", $nombre, $correo, $hashed_password, $is_admin);

    if ($stmt->execute()) {
        header("Location: login.php?success=Usuario registrado exitosamente.");
        exit();
    } else {
        header("Location: registro.php?error=Error al registrar usuario. Inténtelo de nuevo.");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
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
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }
        label {
            font-weight: bold;
            width: 100%;
            text-align: left;
        }
        input, select {
            width: 90%;
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
            margin-bottom: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        /* Estilo del botón de Volver */
        .btn-volver {
            background-color: #ccc;
            color: #333;
        }
        .btn-volver:hover {
            background-color: #bbb;
        }
        .error {
            color: red;
            text-align: center;
            font-size: 14px;
        }
        .requirements {
            margin-top: 10px;
            font-size: 14px;
            color: #333;
            text-align: left;
        }
        .requirements li {
            list-style-type: none;
            padding-left: 20px;
            position: relative;
        }
        .requirements li::before {
            content: "❌";
            position: absolute;
            left: 0;
        }
        .requirements li.valid::before {
            content: "✅";
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registro de Usuario</h1>

        <!-- Mensaje de error -->
        <?php if (isset($_GET['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <form action="registro.php" method="POST" id="registro-form">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required>

            <label for="correo">Correo:</label>
            <input type="email" name="correo" id="correo" required>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" required>

            <ul class="requirements" id="password-requirements">
                <li id="req-length">Entre 8 y 16 caracteres</li>
                <li id="req-uppercase">Al menos una letra mayúscula</li>
                <li id="req-lowercase">Al menos una letra minúscula</li>
                <li id="req-number">Al menos un número</li>
                <li id="req-special">Al menos un carácter especial</li>
            </ul>

            <label for="rol">Rol:</label>
            <select name="rol" id="rol" required>
                <option value="">Seleccione un rol</option>
                <option value="usuario_simple">Usuario Simple</option>
                <option value="responsable">Responsable</option>
            </select>

            <button type="submit">Registrar</button>
            <button type="button" class="btn-volver" onclick="window.location.href='login.php'">Volver al Inicio de Sesión</button>
        </form>
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
            let value = passwordInput.value.trim(); // Elimina espacios invisibles

            requirements.length.classList.toggle('valid', value.length >= 8 && value.length <= 16);
            requirements.uppercase.classList.toggle('valid', /[A-Z]/.test(value));
            requirements.lowercase.classList.toggle('valid', /[a-z]/.test(value));
            requirements.number.classList.toggle('valid', /\d/.test(value));

            // Verificar caracteres especiales en cualquier posición
            const specialCharRegex = /[\W]/;
            requirements.special.classList.toggle('valid', specialCharRegex.test(value));
        });
    });
</script>

</body>
</html>
