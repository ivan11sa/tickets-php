<?php
// Establece la codificación del contenido para evitar problemas con caracteres especiales
header('Content-Type: text/html; charset=UTF-8');

// Incluye el archivo de conexión a la base de datos
require_once 'db_connection.php';

// Verifica si hay un error en la conexión con la base de datos
if ($conn->connect_error) {
    $_SESSION['error'] = 'Error de conexión con la base de datos.';
    header("Location: registro.php");
    exit();
}

// Comprueba si el formulario ha sido enviado con el método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene y limpia los valores del formulario para evitar inyección de datos
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);
    $rol = trim($_POST['rol']);

    // Verifica si algún campo está vacío y muestra un mensaje de error
    if (empty($nombre) || empty($correo) || empty($password) || empty($rol)) {
        $_SESSION['error'] = 'Por favor, complete todos los campos.';
        header("Location: registro.php");
        exit();
    }

    // Valida si el rol seleccionado es válido (Usuario Simple o Responsable)
    if (!in_array($rol, ['usuario_simple', 'responsable'])) {
        $_SESSION['error'] = 'Rol inválido seleccionado.';
        header("Location: registro.php");
        exit();
    }

    // Validación de la contraseña con una expresión regular
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W]).{8,16}$/u', $password)) {
        $_SESSION['error'] = 'La contraseña no cumple con los requisitos.';
        header("Location: registro.php");
        exit();
    }

    // Verifica que el correo tenga un formato válido
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'El correo no tiene un formato válido.';
        header("Location: registro.php");
        exit();
    }

    // Verificar si el correo ya existe en la base de datos
    $sql_check = "SELECT ID FROM USUARIOS WHERE CORREO = ?";
    $stmt_check = $conn->prepare($sql_check);
    if (!$stmt_check) {
        error_log("Error al preparar la consulta: " . $conn->error);
        $_SESSION['error'] = 'Error en la preparación de la consulta.';
        header("Location: registro.php");
        exit();
    }
    $stmt_check->bind_param("s", $correo);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        // Si el correo ya está registrado, muestra un mensaje de error
        $_SESSION['error'] = 'El correo ya está registrado.';
        header("Location: registro.php");
        exit();
    }

    $stmt_check->close();

    // Se cifra la contraseña antes de almacenarla en la base de datos
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Asigna el rol (0 = Usuario Simple, 1 = Administrador)
    $is_admin = ($rol === 'responsable') ? 1 : 0;

    // Consulta SQL para insertar un nuevo usuario en la base de datos
    $sql = "INSERT INTO USUARIOS (NOMBRE, CORREO, CONTRASENA, ADMIN) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Verifica si la consulta se preparó correctamente
    if (!$stmt) {
        $_SESSION['error'] = 'Error en la preparación de la consulta.';
        header("Location: registro.php");
        exit();
    }

    // Asigna los valores a la consulta preparada para evitar inyección SQL
    $stmt->bind_param("sssi", $nombre, $correo, $hashed_password, $is_admin);

    // Ejecuta la consulta y verifica si el usuario fue registrado correctamente
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Usuario registrado exitosamente.';
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = 'Error al registrar usuario. Inténtelo de nuevo.';
        header("Location: registro.php");
        exit();
    }

    // Cierra la consulta preparada
    $stmt->close();
}

// Cierra la conexión a la base de datos
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .valid {
            color: green;
        }
        .invalid {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registro de Usuario</h1>
        
        <!--  Muestra un mensaje de error si hay problemas con el registro -->
        <?php if (isset($_GET['error'])): ?>
            <p style="color:red;"> <?php echo htmlspecialchars($_GET['error']); ?> </p>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <p style="color:green;"> <?php echo htmlspecialchars($_GET['success']); ?> </p>
        <?php endif; ?>

        <!--  Formulario para registrarse -->
        <form action="registro.php" method="POST" id="registro-form">
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

            <label for="rol">Rol:</label>
            <select name="rol" id="rol" required>
                <option value="">Seleccione un rol</option>
                <option value="usuario_simple">Usuario Simple</option>
            </select>

            <button type="submit">Registrar</button>
            <div class="link">
                <p>¿Ya tienes cuenta? <a href="login.php">Iniciar Sesión</a></p>
            </div>
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
                let value = passwordInput.value.trim();

                // Verifica si la contraseña cumple cada requisito y marca en verde si es válido
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

