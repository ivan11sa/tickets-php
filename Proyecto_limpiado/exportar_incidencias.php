<?php
// Inicia la sesión para gestionar la autenticación del usuario
session_start();

// Activa el almacenamiento en búfer de salida para manejar redirecciones y descargas
ob_start();

// Incluye el archivo de conexión a la base de datos
require 'db_connection.php';

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Lista de formatos válidos para la exportación
$formatos_validos = ['csv', 'pdf'];
$formato = $_GET['formato'] ?? 'csv';

// Verifica si el formato seleccionado es válido
if (!in_array($formato, $formatos_validos)) {
    header("Location: listar_incidencias_admin.php?error=Formato no válido");
    exit();
}

// Variables para almacenar los filtros aplicados por el usuario
$provincia_filter = $_GET['provincia'] ?? ''; // Filtro por provincia
$prioridad_filter = $_GET['prioridad'] ?? ''; // Filtro por prioridad
$busqueda = $_GET['busqueda'] ?? ''; // Filtro por búsqueda en título o usuario
$proxima_cierre = isset($_GET['proxima_cierre']) ? true : false; // Filtro para incidencias próximas a cerrarse

// Construcción de la consulta SQL base para obtener las incidencias
$sql = "SELECT i.ID_INCIDENCIA, i.TITULO, i.FECHA_CREACION, i.NIVEL_PRIORIDAD, 
               i.ESTADO, COALESCE(u.NOMBRE, 'Sin usuario') AS USUARIO, 
               COALESCE(p.NOMBRE_PROVINCIA, 'Sin provincia') AS PROVINCIA
        FROM INCIDENCIAS i
        LEFT JOIN USUARIOS u ON i.ID = u.ID
        LEFT JOIN PROVINCIAS p ON i.ID_PROVINCIA = p.ID_PROVINCIA
        WHERE 1=1"; // WHERE 1=1 permite añadir condiciones dinámicamente

// Array para almacenar parámetros y tipos de datos en la consulta preparada
$params = [];
$types = "";

// Aplicar filtro por provincia si el usuario ha seleccionado una
if (!empty($provincia_filter)) {
    $sql .= " AND p.NOMBRE_PROVINCIA = ?";
    $params[] = $provincia_filter;
    $types .= "s";
}

// Aplicar filtro por nivel de prioridad si el usuario ha seleccionado uno
if (!empty($prioridad_filter)) {
    $sql .= " AND i.NIVEL_PRIORIDAD = ?";
    $params[] = $prioridad_filter;
    $types .= "s";
}

// Aplicar filtro por búsqueda en título o nombre de usuario
if (!empty($busqueda)) {
    $sql .= " AND (i.TITULO LIKE ? OR u.NOMBRE LIKE ?)";
    $busqueda_param = "%{$busqueda}%";
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $types .= "ss";
}

// Prepara la consulta SQL
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

// Vincula parámetros a la consulta si existen filtros aplicados
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

// Ejecuta la consulta SQL
$stmt->execute();
$result = $stmt->get_result();

// Almacena los resultados en un array
$incidencias = [];
while ($row = $result->fetch_assoc()) {
    $incidencias[] = $row;
}

// Si no hay incidencias encontradas, muestra un mensaje
if (empty($incidencias)) {
    die("<h3>No se encontraron incidencias con los filtros aplicados.</h3>");
}

// Si el usuario ha seleccionado el formato CSV, genera el archivo CSV
if ($formato == 'csv') {
    ob_clean(); // Limpia el búfer de salida para evitar contenido inesperado en la descarga
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="incidencias.csv"');

    $output = fopen('php://output', 'w'); // Abre la salida estándar para la descarga
    $delimiter = ";"; // Define el delimitador del CSV

    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // Agrega BOM para compatibilidad con Excel

    // Escribe la fila de encabezados en el archivo CSV
    fputcsv($output, ['ID', 'Título', 'Fecha de Creación', 'Prioridad', 'Estado', 'Usuario', 'Provincia'], $delimiter);

    // Escribe los datos de las incidencias en el archivo CSV
    foreach ($incidencias as $incidencia) {
        fputcsv($output, [
            $incidencia['ID_INCIDENCIA'],
            $incidencia['TITULO'],
            $incidencia['FECHA_CREACION'],
            $incidencia['NIVEL_PRIORIDAD'],
            $incidencia['ESTADO'],
            $incidencia['USUARIO'],
            $incidencia['PROVINCIA']
        ], $delimiter);
    }

    fclose($output);
    exit();
}

// Si el usuario ha seleccionado el formato PDF, genera el archivo PDF
elseif ($formato == 'pdf') {
    ob_end_clean(); // Limpia el búfer de salida antes de generar el PDF
    require __DIR__ . '/fpdf186/fpdf.php'; // Incluye la biblioteca FPDF para generar el PDF
    
    $pdf = new FPDF('L', 'mm', 'A4'); // Define la orientación y tamaño del PDF
    $pdf->AddPage(); // Agrega una nueva página
    $pdf->SetFont('Arial', 'B', 10); // Define la fuente y tamaño de texto

    // Define los encabezados de la tabla en el PDF
    $headers = ['ID', 'Título', 'Fecha Creación', 'Prioridad', 'Estado', 'Usuario', 'Provincia'];
    $widths = [15, 60, 50, 30, 30, 40, 35]; // Define los anchos de columna

    // Agrega los encabezados a la tabla del PDF
    foreach ($headers as $i => $header) {
        $pdf->Cell($widths[$i], 10, utf8_decode($header), 1, 0, 'C');
    }
    $pdf->Ln(); // Salto de línea

    // Define el estilo de texto normal
    $pdf->SetFont('Arial', '', 9);

    // Recorre las incidencias y las agrega al PDF
    foreach ($incidencias as $incidencia) {
        $pdf->Cell($widths[0], 10, utf8_decode($incidencia['ID_INCIDENCIA']), 1, 0, 'C');
        $pdf->Cell($widths[1], 10, utf8_decode($incidencia['TITULO']), 1, 0, 'L');
        $pdf->Cell($widths[2], 10, utf8_decode($incidencia['FECHA_CREACION']), 1, 0, 'C');
        $pdf->Cell($widths[3], 10, utf8_decode($incidencia['NIVEL_PRIORIDAD']), 1, 0, 'C');
        $pdf->Cell($widths[4], 10, utf8_decode($incidencia['ESTADO']), 1, 0, 'C');
        $pdf->Cell($widths[5], 10, utf8_decode($incidencia['USUARIO']), 1, 0, 'L');
        $pdf->Cell($widths[6], 10, utf8_decode($incidencia['PROVINCIA']), 1, 0, 'C');
        $pdf->Ln(); // Salto de línea
    }

    // Genera el archivo PDF y lo muestra al usuario
    $pdf->Output();
    exit();
}

// Si el formato de exportación no es válido, redirige con un mensaje de error
header("Location: listar_incidencias_admin.php?error=Formato de exportación no válido");
exit();
?>
