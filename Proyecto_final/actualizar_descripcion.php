<?php
// Inicia la sesión para manejar variables de sesión en el sistema
session_start();

// Incluye el archivo de conexión a la base de datos
require 'db_connection.php'; 

// Verifica si el usuario ha iniciado sesión, si no, lo redirige a la página de inicio de sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit(); // Detiene la ejecución del script
}

// Verifica si se ha recibido el formulario correctamente con el campo 'nueva_descripcion' en formato de array
if (!isset($_POST['nueva_descripcion']) || !is_array($_POST['nueva_descripcion'])) {
    exit("Error: Datos inválidos recibidos.");
}


// Recorre todas las incidencias enviadas en el formulario
foreach ($_POST['nueva_descripcion'] as $id_incidencia => $nueva_descripcion) {
    $nueva_descripcion = trim($nueva_descripcion); // Elimina espacios en blanco al inicio y al final
    
    // Prepara la consulta SQL para actualizar la descripción de la incidencia
    $sql = "UPDATE INCIDENCIAS SET DESCRIPCION = ? WHERE ID_INCIDENCIA = ?";
    $stmt = $conn->prepare($sql); // Prepara la consulta SQL para evitar inyecciones SQL

    // Asigna los valores a los marcadores de posición en la consulta SQL
    $stmt->bind_param("si", $nueva_descripcion, $id_incidencia);
    
    // Ejecuta la consulta
    $stmt->execute();
    
    // Cierra la consulta preparada para liberar recursos
    $stmt->close(); 
}

// Redirige al usuario a la página de listado de incidencias con un mensaje de éxito
header("Location: listar_incidencias.php?success=1");
exit();
?>
