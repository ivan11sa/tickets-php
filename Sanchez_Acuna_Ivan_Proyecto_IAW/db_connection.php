<?php
// Este archivo se usa en todo el proyecto para conectarse a la base de datos.
// Si la conexión falla, el usuario será redirigido a una página de error.

//  Definimos los datos de conexión a la base de datos.
$host = getenv('DB_HOST') ?: 'mysql-service';       // Servidor donde está la base de datos en XAMPP
$user = getenv('DB_USER') ?: 'root'; // Nombre de la base de datos que vamos a usar
$pass = getenv('DB_PASSWORD') ?: 'root';              // Usuario de la base de datos 
$name = getenv('DB_NAME') ?: 'gestion_incidencias';             // Contraseña del usuario. En nuestro caso no la tenemos establecida. 

//  Creamos la conexión a la base de datos usando MySQL.
$conn = new mysqli($host, $user, $pass, $name);
if ($conn->connect_error) {
    die("Error de conexión (".$conn->connect_errno."): ".$conn->connect_error);
}

//  Configuramos el juego de caracteres a UTF-8 para evitar problemas con acentos y caracteres especiales.
$conn->set_charset("utf8mb4");


?>
