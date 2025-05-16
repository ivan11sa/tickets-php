<?php
// logout.php

// 1) Iniciamos la sesión (si no lo está)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2) Regeneramos ID para invalidar el antiguo
session_regenerate_id(true);

// 3) Limpiamos y destruimos
$_SESSION = [];
session_unset();
session_destroy();

// 4) Eliminamos cookie de sesión
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/', '', false, true);
}

// 5) Redirigimos sin haber emitido nada antes
header('Location: login.php?success=Cierre de sesión exitoso.');
exit;
