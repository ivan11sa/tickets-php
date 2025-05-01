<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<body>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
    Usuario: <input type="text" name="usuario" required>
    Contraseña: <input type="password" name="contrasena" required>
    <button type="submit">Iniciar sesión</button>
</form>

<?php

$usuario_correcto = 'Iván';
$contrasena_correcta = '12345';

$usuario = null;

if (isset($_COOKIE['usuario'])) {
    $usuario = $_COOKIE['usuario'];
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    if ($usuario === $usuario_correcto && $contrasena === $contrasena_correcta) {
        setcookie('usuario', $usuario, time() + 3600, '/');
    } else {
        echo "Usuario o contraseña incorrectos.";
        $usuario = null;
    }
}

if (isset($usuario)) {
    echo "Bienvenido, $usuario!<br>";
    echo '<form method="POST" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '">
            <button type="submit" name="logout">Cerrar sesión</button>
          </form>';
}

if (isset($_POST['logout'])) {
    setcookie('usuario', '', time() - 3600, '/');
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

?>

</body>
</html>
