<?php
// Este archivo permite a los administradores gestionar usuarios (crear, editar, cambiar roles, eliminar).

// Iniciar la sesión para gestionar la autenticación del usuario.
session_start(); // Necesario para manejar las variables de sesión.

// Incluir los archivos necesarios para la conexión a la base de datos y el control de permisos.
require 'db_connection.php'; // Archivo para conectar con la base de datos.
require 'control.php'; // Archivo para manejar el control de acceso y permisos.

// Verificar si el usuario tiene sesión activa y si es administrador. Solo el administrador puede hacer uso de esta función. 
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: listar_incidencias_admin.php?error=Acceso denegado."); // Si no es admin, lo redirigimos.
    exit();
}

// Verificar si el formulario fue enviado por POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Obtener los datos enviados desde el formulario.
    $usuario_id = $_POST['usuario_id'] ?? null; // ID del usuario sobre el que se hará la acción.
    $accion = $_POST['accion'] ?? ''; // Acción a realizar (crear, cambiar rol, editar, eliminar).

    // Si no se seleccionó una acción, redirigir con error.
    if (empty($accion)) {
        header("Location: gestionar_usuarios.php?error=Debe seleccionarse una acción.");
        exit();
    }

    // Caso en que se quiera crear un nuevo usuario.
    if ($accion == 'crear') {
        $nombre = trim($_POST['nombre'] ?? ''); // Nombre del usuario.
        $correo = trim($_POST['correo'] ?? ''); // Correo del usuario.
        $password = $_POST['password'] ?? ''; // Contraseña del usuario.
        $rol = $_POST['rol'] ?? '0'; // 0 = Usuario normal, 1 = Administrador.

        // Verificar que los campos obligatorios no estén vacíos.
        if (empty($nombre) || empty($correo) || empty($password)) {
            header("Location: gestionar_usuarios.php?error=Todos los campos son obligatorios.");
            exit();
        }

        // Hashear la contraseña antes de guardarla en la base de datos para mayor seguridad.
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Preparar la consulta SQL para insertar el usuario en la base de datos.
        $stmt = $conn->prepare("INSERT INTO USUARIOS (NOMBRE, CORREO, CONTRASENA, ADMIN) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            header("Location: gestionar_usuarios.php?error=Error en la consulta.");
            exit();
        }
        $stmt->bind_param("sssi", $nombre, $correo, $hashed_password, $rol);
        $stmt->execute();
        $stmt->close();
    }

    // Caso en que se quiere cambiar el rol de un usuario.
    elseif ($accion == 'cambiar_rol') {
        $nuevo_rol = $_POST['nuevo_rol'] ?? '';

        // Verificar que el rol sea válido (0 = usuario, 1 = administrador).
        if ($nuevo_rol !== '' && ($nuevo_rol == '0' || $nuevo_rol == '1')) {
            $stmt = $conn->prepare("UPDATE USUARIOS SET ADMIN = ? WHERE ID = ?");
            $stmt->bind_param("ii", $nuevo_rol, $usuario_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Caso en que se quiere editar la información de un usuario.
    elseif ($accion == 'editar') {
        $nombre = trim($_POST['nombre'] ?? ''); // Nuevo nombre del usuario.
        $correo = trim($_POST['correo'] ?? ''); // Nuevo correo del usuario.
        $password = $_POST['password'] ?? ''; // Nueva contraseña (si se cambia).
        $nuevo_rol = $_POST['nuevo_rol'] ?? ''; // Nuevo rol asignado.

        // Preparar la consulta dependiendo de si se cambia la contraseña o no.
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE USUARIOS SET NOMBRE = ?, CORREO = ?, CONTRASENA = ?, ADMIN = ? WHERE ID = ?");
            $stmt->bind_param("sssii", $nombre, $correo, $hashed_password, $nuevo_rol, $usuario_id);
        } else {
            $stmt = $conn->prepare("UPDATE USUARIOS SET NOMBRE = ?, CORREO = ?, ADMIN = ? WHERE ID = ?");
            $stmt->bind_param("ssii", $nombre, $correo, $nuevo_rol, $usuario_id);
        }
        $stmt->execute();
        $stmt->close();
    }

    // Redirigir a la gestión de usuarios con éxito.
    header("Location: gestionar_usuarios.php?success=Usuario agregado correctamente.");
    exit();
}

// Consulta para obtener la lista de usuarios registrados en el sistema.
$sql = "SELECT ID, NOMBRE, CORREO, ADMIN FROM USUARIOS ORDER BY NOMBRE ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result(); // Obtener los resultados de la consulta.
$usuarios = $result->fetch_all(MYSQLI_ASSOC); // Guardar los usuarios en un array asociativo.
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Usuarios</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2>Gestión de Usuarios</h2>

        <!-- Muestra mensajes de error o éxito si existen -->
        <?php if (isset($_GET['error'])): ?>
            <p class="message error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <p class="message success"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php endif; ?>

        <!-- Formulario para Crear un Nuevo Usuario -->
        <div class="form-container">
            <form action="gestionar_usuarios.php" method="POST" novalidate>
                <input type="hidden" name="accion" value="crear">
                
                <div class="form-group">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="nombre" placeholder="Nombre" required>
                
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="correo" placeholder="Correo" required>

                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="Contraseña" required>

                    <i class="fa-solid fa-user-shield"></i>
                    <select name="rol">
                        <option value="1">Administrador</option>
                        <option value="0">Usuario</option>
                    </select>
                </div>
                <div class="btn-container">
                    <button type="submit"><i class="fa-solid fa-plus"></i> Crear Usuario</button>
                </div>            
            </form>
        </div>
        <div class="table-container">
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
                                <button type="submit" name="accion" value="cambiar_rol">
                                    <i class="fa-solid fa-sync"></i> Actualizar
                                </button>
                            </form>
                        </td>
                        <td class="acciones">
                            <form action="gestionar_usuarios.php" method="POST">
                                <input type="hidden" name="usuario_id" value="<?php echo $usuario['ID']; ?>">
                                <input type="hidden" name="accion" value="editar">
                                <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['NOMBRE']); ?>" required>
                                <input type="email" name="correo" value="<?php echo htmlspecialchars($usuario['CORREO']); ?>" required>
                                <input type="password" name="password" placeholder="Nueva contraseña (opcional)">
                                <button type="submit">Guardar Cambios</button>
                            </form>

                            <form action="eliminar_usuario.php" method="GET">
                                <input type="hidden" name="id" value="<?php echo $usuario['ID']; ?>">
                                <button type="submit" class="btn-delete">
                                    <i class="fa-solid fa-trash"></i> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="fixed-bar">
            <span>Tiempo de sesión: <?php printf("%02d:%02d:%02d", $horas, $minutos, $segundos); ?></span>
            <button type="button" onclick="window.location.href='dashboard.php'">
                <i class="fa-solid fa-arrow-left"></i> Volver al Inicio
            </button>
        </div>
    </div>
</body>
</html>
