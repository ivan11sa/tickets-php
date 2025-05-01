<?php
// Datos de conexión a la base de datos
$host = 'localhost';         // Servidor donde se encuentra la base de datos
$db = 'gestion_incidencias'; // Nombre de la base de datos
$user = 'root';              // Usuario de la base de datos
$password = '';              // Contraseña del usuario 

// Crea una nueva conexión a la base de datos utilizando MySQLi
$conn = new mysqli($host, $user, $password, $db);

// Configura el juego de caracteres a UTF-8 para evitar problemas con caracteres especiales
$conn->set_charset("utf8mb4");

// Verifica si hubo algún error en la conexión
if ($conn->connect_error) {
    exit("Error de conexión: " . $conn->connect_error); // Termina la ejecución si hay un error
}
?>
