<?php
// Este archivo gestiona el inicio de sesión de los usuarios.

// Iniciar la sesión para autenticación del usuario.
session_start();

// Incluir conexión a la base de datos.
require 'db_connection.php';


// Verifica si la solicitud proviene de un formulario enviado con POST.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Obtener y limpiar valores del formulario para evitar espacios innecesarios.
    $correo = trim($_POST['correo'] ?? ''); 
    $password = trim($_POST['password'] ?? '');

    // Validar que los campos no estén vacíos.
    if (empty($correo) || empty($password)) {
        header("Location: login.php?error=Falta completar algún campo.");
        exit();
    }

    // Validar formato de correo electrónico.
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("Location: login.php?error=Formato de correo inválido.");
        exit();
    }

    // Consulta SQL para obtener el usuario por su correo electrónico.
    $sql = "SELECT ID, CONTRASENA, ADMIN FROM USUARIOS WHERE CORREO = ?";
    $stmt = $conn->prepare($sql);

    // Verificar si la consulta fue preparada correctamente.
    if (!$stmt) {
        header("Location: login.php?error=Error en la consulta.");
        exit();
    }

    // Asignar el correo a la consulta preparada para evitar inyección SQL.
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si se encontró un usuario con el correo ingresado.
    if ($result->num_rows > 0) {
        // Obtener la información del usuario de la base de datos.
        $user = $result->fetch_assoc();

        // Verificar si la contraseña ingresada coincide con la almacenada.
        if (password_verify($password, $user['CONTRASENA'])) {
            // Regenerar el ID de sesión para evitar que se reutilice la sesión anterior.
            session_regenerate_id(true);
            // Iniciar sesión del usuario.
            $_SESSION['user_id'] = $user['ID']; // Guardar ID del usuario.
            $_SESSION['is_admin'] = (int) $user['ADMIN']; // Guardar rol del usuario.

            // Redirigir al panel de control.
            header("Location: dashboard.php");
            exit();
        } else {
            // Contraseña incorrecta.
            header("Location: login.php?error=Contraseña incorrecta.");
            exit();
        }
    } else {
        // Usuario no encontrado.
        header("Location: login.php?error=Usuario no registrado.");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="form-container">
        <h1>Iniciar Sesión</h1>
        
        <!-- Muestra mensajes de error o éxito si existen -->
        <?php if (isset($_GET['error'])): ?>
            <p class="message error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <p class="message success"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php endif; ?>

        <!-- Formulario de inicio de sesión -->
        <form action="login.php" method="POST" novalidate>

            <label>Correo:</label>
            <input type="email" name="correo" required autocomplete="email">

            <label>Contraseña:</label>
            <input type="password" name="password" required autocomplete="off">

            <button type="submit">Iniciar Sesión</button>
        </form>
        
        <p>¿Aún no estás registrado? <a href="registro.php">Crear usuario</a></p>
    </div>
</body>
</html>
