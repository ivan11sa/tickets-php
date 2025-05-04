<?php
// Este archivo permite exportar las incidencias a formatos CSV o PDF.
// Básicamente, toma los datos de la base de datos y los convierte en un archivo descargable

session_start(); // Iniciamos la sesión para ver si el usuario está logueado
ob_start(); // Iniciamos el buffer de salida para evitar errores de redirección
require 'db_connection.php'; // Incluimos la conexión a la base de datos

// Comprobamos si el usuario ha iniciado sesión, si no, lo mandamos al login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Debes iniciar sesión."); // Redirigimos con un mensaje de error
    exit();
}

// Lista de formatos permitidos para la exportación (solo permitimos csv y pdf)
$formatos_validos = ['csv', 'pdf'];
$formato = $_GET['formato'] ?? 'csv'; // Si no especifica formato, asumimos CSV por defecto
if (!in_array($formato, $formatos_validos)) {
    header("Location: listar_incidencias_admin.php?error=Formato no válido"); // Si el formato no es válido, redirigimos
    exit();
}

// Filtros para buscar incidencias específicas en la base de datos
$provincia_filter = $_GET['provincia'] ?? ''; // Filtramos por provincia si el usuario selecciona una
$prioridad_filter = $_GET['prioridad'] ?? ''; // Filtramos por nivel de prioridad si se especifica
$busqueda = $_GET['busqueda'] ?? ''; // Filtramos por término de búsqueda si se proporciona

// Construimos la consulta SQL con los filtros aplicados
$sql = "SELECT i.ID_INCIDENCIA, i.TITULO, DATE_FORMAT(i.FECHA_CREACION, '%Y-%m-%d') AS FECHA_CREACION, i.NIVEL_PRIORIDAD, i.ESTADO, 
               COALESCE(u.NOMBRE, 'Sin usuario') AS USUARIO, 
               COALESCE(p.NOMBRE_PROVINCIA, 'Sin provincia') AS PROVINCIA,
               DATE_FORMAT(DATE_ADD(i.FECHA_CREACION, INTERVAL 
                   CASE 
                       WHEN i.NIVEL_PRIORIDAD = 'Baja' THEN 7
                       WHEN i.NIVEL_PRIORIDAD = 'Media' THEN 5
                       WHEN i.NIVEL_PRIORIDAD = 'Alta' THEN 3
                       WHEN i.NIVEL_PRIORIDAD = 'Urgente' THEN 1
                       ELSE 7
                   END DAY), '%Y-%m-%d') AS FECHA_CIERRE
        FROM INCIDENCIAS i
        LEFT JOIN USUARIOS u ON i.ID = u.ID
        LEFT JOIN PROVINCIAS p ON i.ID_PROVINCIA = p.ID_PROVINCIA
        WHERE 1=1"; 

$params = []; // Array para almacenar los parámetros de la consulta
$types = ""; // Variable para almacenar los tipos de datos de los parámetros

// Aplicamos filtro por provincia si se ha seleccionado una
if (!empty($provincia_filter)) {
    $sql .= " AND p.NOMBRE_PROVINCIA = ?"; // Agregamos condición en la consulta SQL
    $params[] = $provincia_filter; // Agregamos el valor del filtro al array de parámetros
    $types .= "s"; // Indicamos que el tipo de dato es string
}

// Aplicamos filtro por prioridad si el usuario lo seleccionó
if (!empty($prioridad_filter)) {
    $sql .= " AND i.NIVEL_PRIORIDAD = ?";
    $params[] = $prioridad_filter;
    $types .= "s";
}

// Filtramos por búsqueda en título o usuario
if (!empty($busqueda)) {
    $sql .= " AND (i.TITULO LIKE ? OR u.NOMBRE LIKE ?)";
    $busqueda_param = "%{$busqueda}%";
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $types .= "ss";
}

// Preparamos y ejecutamos la consulta
$stmt = $conn->prepare($sql);
if (!$stmt) {
    header("Location: listar_incidencias_admin.php?error=Error en la consulta."); // Si hay error, redirigimos
    exit();
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params); // Asignamos los parámetros a la consulta preparada
}

$stmt->execute(); // Ejecutamos la consulta
$result = $stmt->get_result(); // Obtenemos los resultados
$incidencias = $result->fetch_all(MYSQLI_ASSOC); // Convertimos los resultados en un array asociativo

// Si no hay incidencias, mostramos un mensaje de error
if (empty($incidencias)) {
    header("Location: listar_incidencias_admin.php?error=No se encontraron incidencias.");
    exit();
}

// Si el usuario eligió CSV, generamos el archivo CSV
if ($formato == 'csv') {
    ob_clean(); // Limpiamos el buffer de salida para evitar errores
    header('Content-Type: text/csv; charset=UTF-8'); // Indicamos que el contenido es un CSV
    header('Content-Disposition: attachment; filename="incidencias.csv"'); // Nombre del archivo descargado

    $output = fopen('php://output', 'w'); // Abrimos el flujo de salida para escribir el CSV
    $delimiter = ";"; // Usamos punto y coma como separador de columnas
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // Agregamos BOM para compatibilidad con Excel

    // Escribimos los encabezados del CSV
    fputcsv($output, ['ID', 'Título', 'Fecha de Creación', 'Prioridad', 'Estado', 'Usuario', 'Provincia', 'Fecha de Cierre'], $delimiter);

    // Escribimos cada fila con los datos obtenidos de la consulta
    foreach ($incidencias as $incidencia) {
        fputcsv($output, [
            $incidencia['ID_INCIDENCIA'],
            $incidencia['TITULO'],
            $incidencia['FECHA_CREACION'],
            $incidencia['NIVEL_PRIORIDAD'],
            $incidencia['ESTADO'],
            $incidencia['USUARIO'],
            $incidencia['PROVINCIA'],
            $incidencia['FECHA_CIERRE']
        ], $delimiter);
    }

    fclose($output); // Cerramos el flujo de salida del CSV
    exit(); // Terminamos la ejecución del script para que no se agregue contenido adicional
// Si el usuario eligió PDF, generamos el archivo PDF
}elseif ($formato == 'pdf') {
    ob_end_clean(); // Limpiamos el buffer de salida para evitar errores
    require __DIR__ . '/fpdf186/fpdf.php'; // Importamos la librería FPDF para generar el PDF

    $pdf = new FPDF('L', 'mm', 'A4'); // Creamos el objeto PDF en orientación horizontal, tamaño A4
    $pdf->AddPage(); // Agregamos una nueva página
    $pdf->SetFont('Arial', 'B', 10); // Establecemos la fuente del encabezado

    // Definimos los encabezados de la tabla
    $headers = ['ID', 'Título', 'Fecha Creación', 'Prioridad', 'Estado', 'Usuario', 'Provincia', 'Fecha de Cierre'];
    $widths = [15, 60, 40, 30, 30, 30, 25, 40]; // Definimos el ancho de cada columna

    // Agregamos los encabezados al PDF
    foreach ($headers as $i => $header) {
        $pdf->Cell($widths[$i], 10, utf8_decode($header), 1, 0, 'C');
    }
    $pdf->Ln(); // Hacemos un salto de línea después del encabezado

    // Configuramos la fuente para los datos
    $pdf->SetFont('Arial', '', 9);
    foreach ($incidencias as $incidencia) {
        // Agregamos los datos en cada celda de la tabla
        $pdf->Cell($widths[0], 10, $incidencia['ID_INCIDENCIA'], 1, 0, 'C');
        $pdf->Cell($widths[1], 10, utf8_decode($incidencia['TITULO']), 1, 0, 'L');
        $pdf->Cell($widths[2], 10, $incidencia['FECHA_CREACION'], 1, 0, 'C');
        $pdf->Cell($widths[3], 10, $incidencia['NIVEL_PRIORIDAD'], 1, 0, 'C');
        $pdf->Cell($widths[4], 10, $incidencia['ESTADO'], 1, 0, 'C');
        $pdf->Cell($widths[5], 10, utf8_decode($incidencia['USUARIO']), 1, 0, 'L');
        $pdf->Cell($widths[6], 10, utf8_decode($incidencia['PROVINCIA']), 1, 0, 'C');
        $pdf->Cell($widths[7], 10, $incidencia['FECHA_CIERRE'], 1, 0, 'C');
        $pdf->Ln();
    }

    $pdf->Output(); // Generamos el archivo PDF y lo enviamos al navegador
    exit();
}
?>
