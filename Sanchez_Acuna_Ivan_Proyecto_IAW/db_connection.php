<?php
// Este archivo se usa en todo el proyecto para conectarse a la base de datos.
// Si la conexión falla, el usuario será redirigido a una página de error.

//  Definimos los datos de conexión a la base de datos.
$host = getenv('DB_HOST') ?: 'mysql-service';       // Servidor donde está la base de datos en XAMPP
$user = getenv('DB_USER') ?: 'root'; // Nombre de la base de datos que vamos a usar
$password = getenv('DB_PASSWORD') ?: 'root';              // Usuario de la base de datos 
$db = getenv('DB_NAME') ?: 'gestion_incidencias';             // Contraseña del usuario. En nuestro caso no la tenemos establecida. 

//  Creamos la conexión a la base de datos usando MySQL.
$conn = new mysqli($host, $user, $password, $db);
//  Configuramos el juego de caracteres a UTF-8 para evitar problemas con acentos y caracteres especiales.
$conn->set_charset("utf8mb4");
if ($conn->connect_error) {
    header('Location: error.php?error=db_connection');
    exit();
    }

?>
