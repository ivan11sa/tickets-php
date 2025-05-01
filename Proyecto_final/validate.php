<?php
//  Inicia la sesión para gestionar la autenticación del usuario
session_start();

//  Incluye la conexión a la base de datos
require_once 'db_connection.php';

//  Verifica si la solicitud proviene de un formulario enviado con el método POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    //  Debug: Muestra los datos enviados en la solicitud (Para pruebas, debe eliminarse en producción)
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    //  Obtiene y limpia los valores del formulario para evitar espacios innecesarios
    $email = trim($_POST['correo'] ?? ''); 
    $password = trim($_POST['password'] ?? '');

    //  Verifica si los campos están vacíos y muestra un mensaje de error si es necesario
    if (empty($email) || empty($password)) {
        die("Error: Falta completar algún campo.");
    }

    //  Debug: Muestra el correo recibido (Para pruebas, debe eliminarse en producción)
    echo "Correo recibido: " . htmlspecialchars($email) . "<br>";

    //  Consulta SQL para obtener el usuario por su correo electrónico
    $sql = "SELECT ID, CONTRASENA, ADMIN FROM USUARIOS WHERE CORREO = ?";
    $stmt = $conn->prepare($sql);
    
    //  Verifica si la consulta se preparó correctamente
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    //  Asigna el correo a la consulta preparada para evitar inyección SQL
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    //  Debug: Muestra cuántos resultados se encontraron (Para pruebas, debe eliminarse en producción)
    echo "Número de resultados encontrados: " . $result->num_rows . "<br>";

    //  Verifica si se encontró un usuario con el correo ingresado
    if ($result->num_rows > 0) {
        //  Obtiene la información del usuario de la base de datos
        $user = $result->fetch_assoc();

        //  Debug: Muestra los datos del usuario (Para pruebas, debe eliminarse en producción)
        var_dump($user);

        //  Verifica si la contraseña ingresada coincide con la almacenada en la base de datos
        if (password_verify($password, $user['CONTRASENA'])) {
            //  Guarda la sesión del usuario
            $_SESSION['user_id'] = $user['ID']; // ID del usuario
            $_SESSION['is_admin'] = isset($user['ADMIN']) ? (int) $user['ADMIN'] : 0; // Rol de administrador (1) o usuario normal (0)

            //  Redirige al usuario al panel de control
            header("Location: dashboard.php");
            exit();
        } else {
            //  Si la contraseña no coincide, muestra un mensaje de error
            die("Error: Contraseña incorrecta.");
        }
    } else {
        //  Si el usuario no existe en la base de datos, muestra un mensaje de error
        die("Error: Usuario no encontrado.");
    }
} else {
    //  Si el usuario intenta acceder a este archivo sin usar el formulario, muestra un error
    die("Error: Método incorrecto.");
}
?>
