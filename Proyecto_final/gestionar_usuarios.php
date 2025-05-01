<?php
// Inicia la sesión para gestionar la autenticación del usuario
session_start();

// Incluye los archivos necesarios para la conexión a la base de datos y control de permisos
require 'db_connection.php';
require 'control.php';

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    // Si no ha iniciado sesión, lo redirige a la página de inicio de sesión
    header("Location: login.php");
    exit();
}

// Comprueba si el formulario fue enviado por método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene los datos enviados desde el formulario
    $usuario_id = $_POST['usuario_id'] ?? '';
    $accion = $_POST['accion'] ?? '';
    
    // Verifica que se haya seleccionado una acción
    if (empty($accion)) {
        die("Error: Todos los campos son obligatorios.");
    }
    
    // Acción para crear un nuevo usuario
    if ($accion == 'crear') {
        $nombre = $_POST['nombre'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $password = $_POST['password'] ?? '';
        $rol = $_POST['rol'] ?? '';
        
        // Verifica que los campos obligatorios no estén vacíos
        if (!empty($nombre) && !empty($correo) && !empty($password)) {
            // Hashea la contraseña antes de guardarla en la base de datos
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Prepara la consulta SQL para insertar el usuario
            $stmt = $conn->prepare("INSERT INTO USUARIOS (NOMBRE, CORREO, CONTRASENA, ADMIN) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $nombre, $correo, $hashed_password, $rol);
            $stmt->execute();
        }
    
    // Acción para eliminar un usuario
    } elseif ($accion == 'eliminar') {
        $stmt = $conn->prepare("DELETE FROM USUARIOS WHERE ID = ?");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
    
    // Acción para cambiar el rol de un usuario (Administrador o Usuario)
    } elseif ($accion == 'cambiar_rol') {
        $nuevo_rol = $_POST['nuevo_rol'] ?? '';
        
        // Verifica que el rol sea válido (0 = usuario, 1 = administrador)
        if ($nuevo_rol !== '' && ($nuevo_rol == '0' || $nuevo_rol == '1')) {
            $stmt = $conn->prepare("UPDATE USUARIOS SET ADMIN = ? WHERE ID = ?");
            $stmt->bind_param("ii", $nuevo_rol, $usuario_id);
            $stmt->execute();
        }
    
    // Acción para editar los datos de un usuario
    } elseif ($accion == 'editar') {
        $nombre = $_POST['nombre'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $password = $_POST['password'] ?? '';
        $nuevo_rol = $_POST['nuevo_rol'] ?? '';
        
        // Si se proporciona una nueva contraseña, la actualiza con un hash
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE USUARIOS SET NOMBRE = ?, CORREO = ?, CONTRASENA = ?, ADMIN = ? WHERE ID = ?");
            $stmt->bind_param("sssii", $nombre, $correo, $hashed_password, $nuevo_rol, $usuario_id);
        } else {
            // Si no se proporciona una nueva contraseña, solo actualiza los otros campos
            $stmt = $conn->prepare("UPDATE USUARIOS SET NOMBRE = ?, CORREO = ?, ADMIN = ? WHERE ID = ?");
            $stmt->bind_param("ssii", $nombre, $correo, $nuevo_rol, $usuario_id);
        }
        $stmt->execute();
    }
    
    // Redirige de vuelta a la página de gestión de usuarios después de realizar una acción
    header("Location: gestionar_usuarios.php");
    exit();
}

// Consulta para obtener la lista de usuarios
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2>Gestión de Usuarios</h2>

        <!-- Formulario para crear un nuevo usuario -->
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

        <!-- Tabla con la lista de usuarios existentes -->
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
                        <!-- Formulario para actualizar el rol del usuario -->
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
                        <!-- Formulario para editar o eliminar usuario -->
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

        <!-- Barra fija para sesión -->
        <div class="fixed-bar">
            <span>Tiempo transcurrido desde inicio de sesión: <?php printf("%02d:%02d:%02d", $horas, $minutos, $segundos); ?></span>
            <button type="button" onclick="window.location.href='dashboard.php'">Volver al Inicio</button>
        </div>
    </div>
</body>
</html>
