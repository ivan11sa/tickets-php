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
    Contraseña: <input type="password" name="clave" required>
    <button type="submit">Iniciar sesión</button>
</form>

<?php
session_start();

$nombre = "Iván";
$contraseña = "12345";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    if ($usuario === $nombre && $clave === $contraseña) {
        $_SESSION['usuario'] = $usuario; 
        echo "Bienvenido, $usuario!</br>";
    } else {
        echo "Usuario o contraseña incorrectos.";
    }
}


if (isset($_SESSION['usuario'])) {
    echo '<form method="POST" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '">
            <button type="submit" name="logout">Cerrar sesión</button>
            </form>'; 
} else {
    exit();
}

if (isset($_POST['logout'])) {
    session_unset(); 
    session_destroy(); 
    header("Location: " . $_SERVER['PHP_SELF'], '?'); 
    exit();
}
?>

</body>
</html>




</body>
</html>
