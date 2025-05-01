<?php
session_start();
require 'db_connection.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_POST['usuario_id'] ?? '';
    $accion = $_POST['accion'] ?? '';
    
    if (empty($accion)) {
        die("Error: Todos los campos son obligatorios.");
    }
    
    if ($accion == 'crear') {
        $nombre = $_POST['nombre'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $password = $_POST['password'] ?? '';
        $rol = $_POST['rol'] ?? '';
        if (!empty($nombre) && !empty($correo) && !empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO USUARIOS (NOMBRE, CORREO, CONTRASENA, ADMIN) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $nombre, $correo, $hashed_password, $rol);
            $stmt->execute();
        }
    } elseif ($accion == 'eliminar') {
        $stmt = $conn->prepare("DELETE FROM USUARIOS WHERE ID = ?");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
    } elseif ($accion == 'cambiar_rol') {
        $nuevo_rol = $_POST['nuevo_rol'] ?? '';
        if ($nuevo_rol !== '' && ($nuevo_rol == '0' || $nuevo_rol == '1')) {
            $stmt = $conn->prepare("UPDATE USUARIOS SET ADMIN = ? WHERE ID = ?");
            $stmt->bind_param("ii", $nuevo_rol, $usuario_id);
            $stmt->execute();
        }
    } elseif ($accion == 'editar') {
        $nombre = $_POST['nombre'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $password = $_POST['password'] ?? '';
        $nuevo_rol = $_POST['nuevo_rol'] ?? '';
        
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE USUARIOS SET NOMBRE = ?, CORREO = ?, CONTRASENA = ?, ADMIN = ? WHERE ID = ?");
            $stmt->bind_param("sssii", $nombre, $correo, $hashed_password, $nuevo_rol, $usuario_id);
        } else {
            $stmt = $conn->prepare("UPDATE USUARIOS SET NOMBRE = ?, CORREO = ?, ADMIN = ? WHERE ID = ?");
            $stmt->bind_param("ssii", $nombre, $correo, $nuevo_rol, $usuario_id);
        }
        $stmt->execute();
    }
    header("Location: gestionar_usuarios.php");
    exit();
}

// Obtener la lista de usuarios desde la base de datos
$sql = "SELECT ID, NOMBRE, CORREO, ADMIN FROM USUARIOS ORDER BY NOMBRE ASC";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error en la consulta SQL: " . $conn->error);
}
$stmt->execute();
$result = $stmt->get_result();
$usuarios = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Usuarios</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .footer-bar {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #007BFF;
            padding: 10px;
            text-align: left;
            color: white;
        }
        .footer-bar a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            padding-left: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Gestión de Usuarios</h2>
        <form action="gestionar_usuarios.php" method="POST">
            <input type="hidden" name="accion" value="crear">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="email" name="correo" placeholder="Correo" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <select name="rol">
                <option value="1">Administrador</option>
                <option value="0">Usuario</option>
            </select>
            <button type="submit">Crear Usuario</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['NOMBRE']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['CORREO']); ?></td>
                    <td>
                        <form action="gestionar_usuarios.php" method="POST">
                            <input type="hidden" name="usuario_id" value="<?php echo $usuario['ID']; ?>">
                            <select name="nuevo_rol">
                                <option value="1" <?php echo $usuario['ADMIN'] ? 'selected' : ''; ?>>Administrador</option>
                                <option value="0" <?php echo !$usuario['ADMIN'] ? 'selected' : ''; ?>>Usuario</option>
                            </select>
                            <button type="submit" name="accion" value="cambiar_rol">Actualizar</button>
                        </form>
                    </td>
                    <td>
                        <form action="gestionar_usuarios.php" method="POST">
                            <input type="hidden" name="usuario_id" value="<?php echo $usuario['ID']; ?>">
                            <input type="hidden" name="accion" value="editar">
                            <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['NOMBRE']); ?>" required>
                            <input type="email" name="correo" value="<?php echo htmlspecialchars($usuario['CORREO']); ?>" required>
                            <input type="password" name="password" placeholder="Nueva contraseña (opcional)">
                            <button type="submit">Guardar Cambios</button>
                            <button type="submit" name="accion" value="eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="footer-bar">
        <a href="dashboard.php">Volver al Inicio</a>
    </div>
</body>
</html>

