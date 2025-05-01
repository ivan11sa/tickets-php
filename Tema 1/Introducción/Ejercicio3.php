<?php
echo "<h2>Contenido de \$_GET</h2>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

echo "<h2>Contenido de \$_POST</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<h2>Contenido de \$_REQUEST</h2>";
echo "<pre>";
print_r($_REQUEST);
echo "</pre>";

echo "<h2>Contenido de \$_SERVER</h2>";
echo "<pre>";
print_r($_SERVER);
echo "</pre>";

echo "<h2>Contenido de \$_FILES</h2>";
echo "<pre>";
print_r($_FILES);
echo "</pre>";

echo "<h2>Contenido de \$_ENV</h2>";
echo "<pre>";
print_r($_ENV);
echo "</pre>";

echo "<h2>Contenido de \$_COOKIE</h2>";
echo "<pre>";
print_r($_COOKIE);
echo "</pre>";

echo "<h2>Contenido de \$_SESSION</h2>";
echo "<pre>";
session_start(); // Asegúrate de iniciar la sesión para poder acceder a $_SESSION
print_r($_SESSION);
echo "</pre>";

echo "<h2>Contenido de \$GLOBALS</h2>";
echo "<pre>";
print_r($GLOBALS);
echo "</pre>";
?>
