<?php
// Este archivo se usa en todo el proyecto para conectarse a la base de datos.
// Si la conexión falla, el usuario será redirigido a una página de error.

//  Definimos los datos de conexión a la base de datos.
$host = 'localhost';         // Servidor donde está la base de datos en XAMPP
$db = 'gestion_incidencias'; // Nombre de la base de datos que vamos a usar
$user = 'root';              // Usuario de la base de datos 
$password = '';              // Contraseña del usuario. En nuestro caso no la tenemos establecida. 

//  Creamos la conexión a la base de datos usando MySQL.
$conn = new mysqli($host, $user, $password, $db);

//  Configuramos el juego de caracteres a UTF-8 para evitar problemas con acentos y caracteres especiales.
$conn->set_charset("utf8mb4");

//  Verificamos si hubo algún error en la conexión.
if ($conn->connect_error) {
    // Si hay un error, redirigimos a una página de error personalizada.
    header("Location: error.php?error=db_connection");
    exit(); //  Importante: Esto detiene la ejecución para que no se siga cargando la página con un error.
}
?>
