<?php
$host = 'localhost';
$db = 'gestion_incidencias';
$user = 'root';
$password = '';

$conn = new mysqli($host, $user, $password, $db);
if (!$conn || $conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
