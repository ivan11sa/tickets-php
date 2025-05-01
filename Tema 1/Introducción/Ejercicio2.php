<?php
// Para obtener la dirección IP. En este caso nos da error porque no estamos conectados a un servidor
$server_ip = $_SERVER['SERVER_ADDR'];

// Obtener el nombre del host del servidor 
$server_name = $_SERVER['SERVER_NAME'];

// Para obtener el software que se está utilizando
$server_software = $_SERVER['SERVER_SOFTWARE'];

// Para obtener información sobre el agente de usuario 
$user_agent = $_SERVER['HTTP_USER_AGENT'];

// Para obtener la dirección IP del cliente 
$client_ip = $_SERVER['REMOTE_ADDR'];

echo "Dirección IP del servidor: " . $server_ip . "<br>";
echo "Nombre del host del servidor: " . $server_name . "<br>";
echo "Software del servidor: " . $server_software . "<br>";
echo "Agente de usuario: " . $user_agent . "<br>";
echo "Dirección IP del cliente: " . $client_ip . "<br>";
?>
