<?php
$servername = "mysql-service";
$username = "root";
$password = "root";
$database = "poryecto";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
echo "Conexión exitosa a la base de datos";
?>
