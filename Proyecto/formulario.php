<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
</head>
<body>
    <h1>Registrar Usuario</h1>
    <form action="registro.php" method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" required>
        <br>
        <label for="correo">Correo:</label>
        <input type="email" name="correo" id="correo" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required>
        <br>
        <label for="is_admin">Es Administrador:</label>
        <input type="checkbox" name="is_admin" id="is_admin">
        <br>
        <button type="submit">Registrar</button>
    </form>
</body>
</html>
