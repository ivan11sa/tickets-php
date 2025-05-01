<?php
session_start();
require 'db_connection.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verificar si se enviaron datos
if (!isset($_POST['nueva_descripcion']) || empty($_POST['nueva_descripcion'])) {
    exit("No se enviaron datos.");
}

try {
    foreach ($_POST['nueva_descripcion'] as $id_incidencia => $nueva_descripcion) {
        $sql = "UPDATE INCIDENCIAS SET DESCRIPCION = ? WHERE ID_INCIDENCIA = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nueva_descripcion, $id_incidencia);
        $stmt->execute();
    }
    
    header("Location: listar_incidencias.php?success=1");
    exit();
} catch (Exception $e) {
    error_log("Error al actualizar: " . $e->getMessage());
    exit("Error al actualizar la descripción.");
}
?>
