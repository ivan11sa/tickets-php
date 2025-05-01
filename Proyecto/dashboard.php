<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'db_connection.php';

$user_id = $_SESSION['user_id'];

// Preparar consulta para obtener los datos del usuario
$sql = "SELECT NOMBRE, ADMIN FROM USUARIOS WHERE ID = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error en la preparación del SQL: " . $conn->error);
}

$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $isAdmin = $user['ADMIN'];
} else {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Incidencias</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        h1, h2 {
            text-align: center;
            color: #333;
        }
        .menu {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
        .menu-item {
            background-color: #007BFF;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 18px;
            transition: 0.3s;
        }
        .menu-item:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenido a la Gestión de Incidencias, <?php echo htmlspecialchars($user['NOMBRE']); ?></h1>
        
        <?php if ($isAdmin == 1): ?>
            <h2>Panel de Administrador</h2>
            <p>Como administrador, puedes acceder a las siguientes opciones:</p>
            <div class="menu">
                <a href="crear_incidencia.php" class="menu-item"><i class="fa-solid fa-plus"></i> Crear Incidencia</a>
                <a href="listar_incidencias.php" class="menu-item"><i class="fa-solid fa-list"></i> Listar Incidencias</a>
                <a href="gestionar_usuarios.php" class="menu-item"><i class="fa-solid fa-users"></i> Gestionar Usuarios</a>
            </div>
        <?php else: ?>
            <h2>Panel de Usuario</h2>
            <p>Como usuario regular, puedes realizar las siguientes acciones:</p>
            <div class="menu">
                <a href="crear_incidencia.php" class="menu-item"><i class="fa-solid fa-plus"></i> Crear Incidencia</a>
                <a href="listar_incidencias.php" class="menu-item"><i class="fa-solid fa-list"></i> Listar Incidencias</a>
            </div>
        <?php endif; ?>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="logout.php" class="menu-item" style="background-color: #dc3545;"><i class="fa-solid fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
    </div>
</body>
</html>
