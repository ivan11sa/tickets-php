<?php
// Verifica si la sesión no ha sido iniciada y, en ese caso, la inicia
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Comprueba si el usuario ha iniciado sesión; si no, lo redirige a la página de inicio de sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Incluye los archivos necesarios para la conexión a la base de datos y control de acceso
require 'db_connection.php';
require 'control.php';

// Verifica si la conexión con la base de datos está establecida
if (!isset($conn)) {
    error_log("Error: No se pudo establecer la conexión con la base de datos en " . __FILE__);
    header("Location: error.php?msg=Error en la base de datos");
    exit();
}

// Obtiene el ID del usuario desde la sesión
$user_id = $_SESSION['user_id'];

// Prepara la consulta para obtener el nombre y el rol de administrador del usuario
$sql = "SELECT NOMBRE, ADMIN FROM USUARIOS WHERE ID = ?";
$stmt = $conn->prepare($sql);

// Verifica si la consulta se preparó correctamente
if (!$stmt) {
    error_log("Error en la preparación del SQL: " . $conn->error . " en " . __FILE__ . " línea " . __LINE__);
    header("Location: error.php?msg=Error en la base de datos");
    exit();
}

// Asigna el ID del usuario a la consulta y la ejecuta
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Verifica si se encontró el usuario en la base de datos
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc(); // Obtiene los datos del usuario
    $isAdmin = (int) $user['ADMIN']; // Convierte el valor de ADMIN en un entero (0 = usuario, 1 = administrador)
} else {
    // Si no se encuentra el usuario, lo redirige a la página de inicio de sesión con un mensaje de error
    header("Location: login.php?error=Acceso denegado");
    exit();
}

// Cierra la consulta para liberar recursos
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Incidencias</title>
    <link rel="stylesheet" href="css/inicio.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Mensaje de bienvenida con el nombre del usuario -->
        <h1>Bienvenido a la Gestión de Incidencias, <?php echo htmlspecialchars($user['NOMBRE']); ?></h1>
        
        <!-- Si el usuario es administrador, muestra opciones adicionales -->
        <?php if ($isAdmin == 1): ?>
            <h2>Panel de Administrador</h2>
            <p>Como administrador, puedes acceder a las siguientes opciones:</p>
            <div class="menu">
                <a href="crear_incidencia.php" class="menu-item"><i class="fa-solid fa-plus"></i> Crear Incidencia</a>
                <a href="listar_incidencias.php" class="menu-item"><i class="fa-solid fa-list"></i> Listar mis Incidencia</a>
                <a href="listar_incidencias_admin.php" class="menu-item"><i class="fa-solid fa-list"></i> Listar Incidencias</a>
                <a href="gestionar_usuarios.php" class="menu-item"><i class="fa-solid fa-users"></i> Gestionar Usuarios</a>
            </div>
        <?php else: ?>
            <!-- Si el usuario no es administrador, muestra opciones restringidas -->
            <h2>Panel de Usuario</h2>
            <p>Como usuario regular, puedes realizar las siguientes acciones:</p>
            <div class="menu">
                <a href="crear_incidencia.php" class="menu-item"><i class="fa-solid fa-plus"></i> Crear Incidencia</a>
                <a href="listar_incidencias.php" class="menu-item"><i class="fa-solid fa-list"></i> Listar mis Incidencias</a>
            </div>
        <?php endif; ?>
        
        <!-- Opción para cerrar sesión -->
        <div>
            <a href="logout.php" class="cerrar-sesion"><i class="fa-solid fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>

        <!-- Barra fija que muestra el tiempo restante de sesión -->
        <div class="fixed-bar">
            <span>Tiempo transcurrido desde inicio de sesión: <?php printf("%02d:%02d:%02d", $horas, $minutos, $segundos); ?></span>
        </div>
    </div>


</body>
</html>
