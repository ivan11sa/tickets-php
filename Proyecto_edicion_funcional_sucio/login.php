<?php
// Inicia la sesión para gestionar la autenticación del usuario
session_start();

// Incluye la conexión a la base de datos
require 'db_connection.php';

// Verifica si la solicitud proviene de un formulario enviado con el método POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Obtiene y limpia los valores del formulario para evitar espacios innecesarios
    $correo = trim($_POST['correo'] ?? ''); 
    $password = trim($_POST['password'] ?? '');

    // Verifica si los campos están vacíos y muestra un mensaje de error si es necesario
    if (empty($correo) || empty($password)) {
        header("Location: login.php?error=Error: Falta completar algún campo.");
        exit();
    }

    // Consulta SQL para obtener el usuario por su correo electrónico
    $sql = "SELECT ID, CONTRASENA, ADMIN FROM USUARIOS WHERE CORREO = ?";
    $stmt = $conn->prepare($sql);

    // Asigna el correo a la consulta preparada para evitar inyección SQL
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica si se encontró un usuario con el correo ingresado
    if ($result->num_rows > 0) {
        // Obtiene la información del usuario de la base de datos
        $user = $result->fetch_assoc();

        // Verifica si la contraseña ingresada coincide con la almacenada en la base de datos
        if (password_verify($password, $user['CONTRASENA'])) {
            // Guarda la sesión del usuario
            $_SESSION['user_id'] = $user['ID']; // ID del usuario
            $_SESSION['is_admin'] = isset($user['ADMIN']) ? (int) $user['ADMIN'] : 0; // Rol de administrador (1) o usuario normal (0)

            // Redirige al usuario al panel de control
            header("Location: dashboard.php");
            exit();
        } else {
            // Si la contraseña no coincide, muestra un mensaje de error
            header("Location: login.php?error=Error: Contraseña incorrecta.");
            exit();
        }
    } else {
        // Si el usuario no existe en la base de datos, muestra un mensaje de error
        header("Location: login.php?error=Error: Usuario no encontrado.");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="form-container">
        <h1>Iniciar Sesión</h1>
        
        <!-- Muestra un mensaje de error si hay problemas con el inicio de sesión -->
        <?php if (isset($_GET['error'])): ?>
            <p style="color:red;"> <?php echo htmlspecialchars($_GET['error']); ?> </p>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <p style="color:green;"> <?php echo htmlspecialchars($_GET['success']); ?> </p>
        <?php endif; ?>

        <!-- Formulario de inicio de sesión -->
        <form action="login.php" method="POST" novalidate>

            <label>Correo:</label>
            <input type="correo" name="correo" required>

            <label>Contraseña:</label>
            <input type="password" name="password" required>

            <button type="submit">Iniciar Sesión</button>
        </form>
        
        <p>¿Aún no estás registrado? <a href="registro.php">Crear usuario</a></p>
    </div>
</body>
</html>
