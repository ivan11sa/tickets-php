<?php
ob_start();
// Este archivo gestiona el acceso de los usuarios y muestra opciones según su rol.

// Iniciar la sesión si no está iniciada.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//  Verificar si el usuario ha iniciado sesión. Si no, lo redirigimos al login.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Debes iniciar sesión.");
    exit();
}

//  Incluir los archivos necesarios para la conexión a la base de datos y control de sesión.
require 'db_connection.php';
require 'control.php';


//  Verificar que la conexión a la base de datos esté establecida.
if (!isset($conn)) {
    header("Location: error.php?msg=Error en la base de datos");
    exit();
}

//  Obtener el ID del usuario desde la sesión.
$user_id = $_SESSION['user_id'];

//  Preparar la consulta para obtener el nombre y el rol del usuario. Muy importante para poder clasificar al usuario y poder asignarle las funcionalidades.
$sql = "SELECT NOMBRE, ADMIN FROM USUARIOS WHERE ID = ?";
$stmt = $conn->prepare($sql);


//  Asignamos el ID del usuario a la consulta y la ejecutamos.
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

//  Verificar si se encontró el usuario en la base de datos.
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc(); // Obtenemos los datos del usuario.
    $isAdmin = (int) $user['ADMIN']; // Convertimos el valor de ADMIN en un número (0 = usuario, 1 = administrador).
} else {
    //  Si el usuario no existe, cerramos la sesión y lo redirigimos al login.
    session_unset();
    session_destroy();
    header("Location: login.php?error=Acceso denegado.");
    exit();
}

//  Cerrar la consulta para liberar memoria.
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

<?php if (!empty($_SESSION['show_warning'])): ?>
  <?php include 'session_warning.php'; ?>
<?php endif; ?>
    <div class="container">
        <!-- Esta es una de las partes mas importantes del codigo, donde diferenciamos entre las funcionalidades a las que puede acceder el administrador
         o un usuario simple -->
        <h1>Bienvenid@ a la Gestión de Incidencias, <?php echo htmlspecialchars($user['NOMBRE']); ?></h1>
        <?php if ($isAdmin == 1): ?>
            <h2>Panel de Administrador</h2>
            <p>Como administrador, puedes acceder a las siguientes opciones:</p>
            <div class="menu">
                <a href="crear_incidencia.php" class="menu-item"><i class="fa-solid fa-plus"></i> Crear Incidencia</a>
                <a href="listar_incidencias.php" class="menu-item"><i class="fa-solid fa-list"></i> Listar Mis Incidencias</a>
                <a href="listar_incidencias_admin.php" class="menu-item"><i class="fa-solid fa-list"></i> Listar Todas las Incidencias</a>
                <a href="gestionar_usuarios.php" class="menu-item"><i class="fa-solid fa-users"></i> Gestionar Usuarios</a>
            </div>
        <?php else: ?>
            <h2>Panel de Usuario</h2>
            <p>Como usuario regular, puedes realizar las siguientes acciones:</p>
            <div class="menu">
                <a href="crear_incidencia.php" class="menu-item"><i class="fa-solid fa-plus"></i> Crear Incidencia</a>
                <a href="listar_incidencias.php" class="menu-item"><i class="fa-solid fa-list"></i> Listar Mis Incidencias</a>
            </div>
        <?php endif; ?>
        <div>
            <a href="logout.php" class="cerrar-sesion"><i class="fa-solid fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
        <div class="fixed-bar">
            <span>Tiempo transcurrido desde inicio de sesión: <?php printf("%02d:%02d:%02d", $horas, $minutos, $segundos); ?></span>
        </div>
    </div>

</body>
</html>
