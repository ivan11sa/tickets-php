<?php
session_start();
require 'db_connection.php';
require 'control.php';


if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}


$provincia_filter = $_GET['provincia'] ?? '';
$prioridad_filter = $_GET['prioridad'] ?? '';
$proxima_cierre = isset($_GET['proxima_cierre']) ? true : false;


$provincias = [];
try {
    $provincia_query = "SELECT NOMBRE_PROVINCIA FROM PROVINCIAS ORDER BY NOMBRE_PROVINCIA";
    $provincia_stmt = $conn->prepare($provincia_query);
    $provincia_stmt->execute();
    $provincia_result = $provincia_stmt->get_result();
    while ($row = $provincia_result->fetch_assoc()) {
        $provincias[] = $row['NOMBRE_PROVINCIA'];
    }
} catch (Exception $e) {
    error_log("Error al obtener provincias: " . $e->getMessage());
}

try {

    $sql = "SELECT 
                i.ID_INCIDENCIA, 
                i.TITULO, 
                i.FECHA_CREACION, 
                i.NIVEL_PRIORIDAD, 
                i.ESTADO, 
                u.NOMBRE AS USUARIO, 
                p.NOMBRE_PROVINCIA AS PROVINCIA
            FROM INCIDENCIAS i
            INNER JOIN USUARIOS u ON i.ID = u.ID
            INNER JOIN PROVINCIAS p ON i.ID_PROVINCIA = p.ID_PROVINCIA
            WHERE 1=1";

    $params = [];
    $types = "";
    if (!empty($provincia_filter)) {
        $sql .= " AND p.NOMBRE_PROVINCIA = ?";
        $params[] = &$provincia_filter;
        $types .= "s";
    }
    if (!empty($prioridad_filter)) {
        $sql .= " AND i.NIVEL_PRIORIDAD = ?";
        $params[] = &$prioridad_filter;
        $types .= "s";
    }

    if ($proxima_cierre) {
        $sql .= " AND DATE_ADD(i.FECHA_CREACION, INTERVAL 
                    CASE 
                        WHEN i.NIVEL_PRIORIDAD = 'Baja' THEN 7
                        WHEN i.NIVEL_PRIORIDAD = 'Media' THEN 5
                        WHEN i.NIVEL_PRIORIDAD = 'Alta' THEN 3
                        WHEN i.NIVEL_PRIORIDAD = 'Urgente' THEN 1
                        ELSE 7
                    END DAY) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
    }
    
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $incidencias = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    error_log("Error en la consulta: " . $e->getMessage());
    exit("Error al cargar las incidencias. Detalles en el log.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Incidencias</title>
    <link rel="stylesheet" href="styles1.css">
    <script>
        function applyFilters() {
            const provincia = document.getElementById('provincia').value;
            const prioridad = document.getElementById('prioridad').value;
            const proximaCierre = document.getElementById('proxima_cierre').checked ? '1' : '';
            window.location.href = listar_incidencias_admin.php?provincia=${provincia}&prioridad=${prioridad};
        }

        function editSelected() {
            const selectedIds = Array.from(document.querySelectorAll('input[type=checkbox]:checked'))
                                    .map(checkbox => checkbox.value);
            if (selectedIds.length === 1) {
                window.location.href = editar_incidencia.php?id=${selectedIds[0]};
            } else {
                alert('Seleccione exactamente una incidencia para editar.');
            }
        }

        function deleteSelected() {
            const selectedIds = Array.from(document.querySelectorAll('input[type=checkbox]:checked'))
                                    .map(checkbox => checkbox.value);
            if (selectedIds.length > 0) {
                if (confirm("¿Seguro que deseas eliminar las incidencias seleccionadas?")) {
                    window.location.href = eliminar_incidencia.php?ids=${selectedIds.join(',')};
                }
            } else {
                alert('Seleccione al menos una incidencia para eliminar.');
            }
        }

        function buscarIncidencia() {
            let input = document.getElementById("busqueda").value.toLowerCase();
            let filas = document.querySelectorAll("tbody tr");

            filas.forEach(fila => {
            let textoFila = fila.innerText.toLowerCase();
            fila.style.display = textoFila.includes(input) ? "" : "none";
            });
        }

    </script>
</head>
<body>
    <div class="container">
        <h2>Listado de Incidencias</h2>
        <div class="filters">
            <label for="provincia">Filtrar por provincia:</label>
            <select id="provincia" name="provincia">
                <option value="">Todas</option>
                <?php foreach ($provincias as $provincia): ?>
                    <option value="<?php echo htmlspecialchars($provincia); ?>"
                        <?php echo ($provincia_filter === $provincia) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($provincia); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="prioridad">Filtrar por prioridad:</label>
            <select id="prioridad" name="prioridad">
                <option value="">Todas</option>
                <option value="Baja">Baja</option>
                <option value="Media">Media</option>
                <option value="Alta">Alta</option>
                <option value="Urgente">Urgente</option>
            </select>
            <label><input type="checkbox" id="proxima_cierre" name="proxima_cierre" <?php echo $proxima_cierre ? 'checked' : ''; ?>> Próxima a cerrarse</label>
            <input type="text" id="busqueda" onkeyup="buscarIncidencia()" placeholder="Buscar incidencia...">
            <button onclick="applyFilters()">Aplicar Filtros</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th class="checkbox-column"></th>
                    <th>Título</th>
                    <th>Fecha de Creación</th>
                    <th>Nivel de Prioridad</th>
                    <th>Estado</th>
                    <th>Usuario</th>
                    <th>Provincia</th>
                    <th>Fecha de Cierre</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($incidencias as $incidencia): ?>
                    <?php
                        $plazo_dias = ['Baja' => 7, 'Media' => 5, 'Alta' => 3, 'Urgente' => 1];
                        $fecha_creacion = new DateTime($incidencia['FECHA_CREACION']);
                        $dias_para_cierre = $plazo_dias[$incidencia['NIVEL_PRIORIDAD']] ?? 7;
                        $fecha_cierre = clone $fecha_creacion;
                        $fecha_cierre->modify("+{$dias_para_cierre} days");
                        $hoy = new DateTime();
                        $alerta = ($hoy->diff($fecha_cierre)->days == 1 && $fecha_cierre > $hoy);
                    ?>
                    <tr <?php echo $alerta ? 'style="background-color: #ffcccc;"' : ''; ?>>
                        <td class="checkbox-column">
                            <input type="checkbox" value="<?php echo $incidencia['ID_INCIDENCIA']; ?>">
                        </td>
                    <td><?php echo htmlspecialchars($incidencia['TITULO']); ?></td>
                    <td><?php echo htmlspecialchars($incidencia['FECHA_CREACION']); ?></td>
                    <td>
                        <div class="priority-container">
                            <span class="priority <?php echo htmlspecialchars($incidencia['NIVEL_PRIORIDAD']); ?>">
                                <?php echo htmlspecialchars($incidencia['NIVEL_PRIORIDAD']); ?>
                            </span>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($incidencia['ESTADO']); ?></td>
                    <td><?php echo htmlspecialchars($incidencia['USUARIO']); ?></td>
                    <td><?php echo htmlspecialchars($incidencia['PROVINCIA']); ?></td>
                    <td><?php echo $fecha_cierre->format('Y-m-d'); ?></td>
                </tr>
                
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="fixed-bar">
        <span>Tiempo restante de sesión: <span id="contadorSesion">30:00</span></span>
        <button onclick="editSelected()">Editar Incidencia</button>
        <button onclick="deleteSelected()">Eliminar Incidencia</button>
        <button onclick="window.location.href='dashboard.php'">Volver al Inicio</button>
    </div>
</body>
</html>







<?php
session_start();
require 'db_connection.php';


if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php?error=Acceso denegado");
    exit();
}

// Verificar conexión a la base de datos
if (!$conn) {
    die("Error de conexión a la base de datos.");
}

$formato = $_POST['formato'] ?? 'csv';

// Obtener datos filtrados de incidencias (sin responsable)
$sql = "SELECT i.ID_INCIDENCIA, i.TITULO, i.NIVEL_PRIORIDAD, i.ESTADO, p.NOMBRE_PROVINCIA AS PROVINCIA
        FROM INCIDENCIAS i
        INNER JOIN PROVINCIAS p ON i.ID_PROVINCIA = p.ID_PROVINCIA";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();
$incidencias = $result->fetch_all(MYSQLI_ASSOC);

if ($formato == 'csv') {
    header('Content-Type: text/csv; charset=ISO-8859-1');
    header('Content-Disposition: attachment; filename="incidencias.csv"');

    $output = fopen('php://output', 'w');

    // Encabezados con codificación corregida
    fputcsv($output, array_map("utf8_decode", ['ID', 'Título', 'Prioridad', 'Estado', 'Provincia']));

    foreach ($incidencias as $incidencia) {
        fputcsv($output, array_map("utf8_decode", $incidencia));
    }

    fclose($output);
    exit();

} elseif ($formato == 'pdf') {
    require __DIR__ . '/fpdf186/fpdf.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(20, 10, utf8_decode('ID'), 1);
$pdf->Cell(70, 10, utf8_decode('Título'), 1);
$pdf->Cell(30, 10, utf8_decode('Prioridad'), 1);
$pdf->Cell(30, 10, utf8_decode('Estado'), 1);
$pdf->Cell(40, 10, utf8_decode('Provincia'), 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
foreach ($incidencias as $incidencia) {
    $pdf->Cell(20, 10, utf8_decode($incidencia['ID_INCIDENCIA']), 1);
    $pdf->Cell(70, 10, utf8_decode($incidencia['TITULO']), 1);
    $pdf->Cell(30, 10, utf8_decode($incidencia['NIVEL_PRIORIDAD']), 1);
    $pdf->Cell(30, 10, utf8_decode($incidencia['ESTADO']), 1);
    $pdf->Cell(40, 10, utf8_decode($incidencia['PROVINCIA']), 1);
    $pdf->Ln();
}

$pdf->Output();
exit();

}

header("Location: listar_incidencias_admin.php?error=Formato de exportación no válido");
exit();
?>

<?php
session_start();
require 'db_connection.php';
require 'control.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// Actualizar estados de incidencias automáticamente
try {
    $sql_update = "UPDATE INCIDENCIAS 
                   SET ESTADO = 'Cerrada' 
                   WHERE ESTADO != 'Cerrada' 
                   AND DATE_ADD(FECHA_CREACION, INTERVAL 
                        CASE 
                            WHEN NIVEL_PRIORIDAD = 'Baja' THEN 7
                            WHEN NIVEL_PRIORIDAD = 'Media' THEN 5
                            WHEN NIVEL_PRIORIDAD = 'Alta' THEN 3
                            WHEN NIVEL_PRIORIDAD = 'Urgente' THEN 1
                            ELSE 7
                        END DAY) <= CURDATE()";
    
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->execute();
} catch (Exception $e) {
    error_log("Error al actualizar estados de incidencias: " . $e->getMessage());
}

// Obtener filtros desde el formulario
$provincia_filter = $_GET['provincia'] ?? '';
$prioridad_filter = $_GET['prioridad'] ?? '';
$proxima_cierre = isset($_GET['proxima_cierre']) ? true : false;
$busqueda = $_GET['busqueda'] ?? '';

// Obtener lista de provincias
$provincias = [];
try {
    $provincia_query = "SELECT NOMBRE_PROVINCIA FROM PROVINCIAS ORDER BY NOMBRE_PROVINCIA";
    $provincia_stmt = $conn->prepare($provincia_query);
    $provincia_stmt->execute();
    $provincia_result = $provincia_stmt->get_result();
    while ($row = $provincia_result->fetch_assoc()) {
        $provincias[] = $row['NOMBRE_PROVINCIA'];
    }
} catch (Exception $e) {
    error_log("Error al obtener provincias: " . $e->getMessage());
}

// Obtener lista de incidencias con filtros aplicados
try {
    $sql = "SELECT 
                i.ID_INCIDENCIA, 
                i.TITULO, 
                i.FECHA_CREACION, 
                i.NIVEL_PRIORIDAD, 
                i.ESTADO, 
                u.NOMBRE AS USUARIO, 
                p.NOMBRE_PROVINCIA AS PROVINCIA
            FROM INCIDENCIAS i
            INNER JOIN USUARIOS u ON i.ID = u.ID
            INNER JOIN PROVINCIAS p ON i.ID_PROVINCIA = p.ID_PROVINCIA
            WHERE 1=1";

    $params = [];
    $types = "";
    
    if (!empty($provincia_filter)) {
        $sql .= " AND p.NOMBRE_PROVINCIA = ?";
        $params[] = &$provincia_filter;
        $types .= "s";
    }
    if (!empty($prioridad_filter)) {
        $sql .= " AND i.NIVEL_PRIORIDAD = ?";
        $params[] = &$prioridad_filter;
        $types .= "s";
    }
    if ($proxima_cierre) {
        $sql .= " AND DATE_ADD(i.FECHA_CREACION, INTERVAL 
                    CASE 
                        WHEN i.NIVEL_PRIORIDAD = 'Baja' THEN 7
                        WHEN i.NIVEL_PRIORIDAD = 'Media' THEN 5
                        WHEN i.NIVEL_PRIORIDAD = 'Alta' THEN 3
                        WHEN i.NIVEL_PRIORIDAD = 'Urgente' THEN 1
                        ELSE 7
                    END DAY) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
    }
    if (!empty($busqueda)) {
        $sql .= " AND (i.TITULO LIKE ? OR u.NOMBRE LIKE ?)";
        $busqueda_param = "%{$busqueda}%";
        $params[] = &$busqueda_param;
        $params[] = &$busqueda_param;
        $types .= "ss";
    }

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        if (!$stmt->bind_param($types, ...$params)) {
            die("Error en bind_param: " . $stmt->error);
        }
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $incidencias = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    error_log("Error en la consulta: " . $e->getMessage());
    exit("Error al cargar las incidencias. Detalles en el log.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Incidencias</title>
    <link rel="stylesheet" href="styles1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2>Listado de Incidencias</h2>
        <form method="GET">
            <label for="provincia">Filtrar por provincia:</label>
            <select id="provincia" name="provincia">
                <option value="">Todas</option>
                <?php foreach ($provincias as $provincia): ?>
                    <option value="<?php echo htmlspecialchars($provincia); ?>"
                        <?php echo ($provincia_filter === $provincia) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($provincia); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="prioridad">Filtrar por prioridad:</label>
            <select id="prioridad" name="prioridad">
                <option value="">Todas</option>
                <option value="Baja">Baja</option>
                <option value="Media">Media</option>
                <option value="Alta">Alta</option>
                <option value="Urgente">Urgente</option>
            </select>

            <label>
                <input type="checkbox" name="proxima_cierre" value="1" <?php echo $proxima_cierre ? 'checked' : ''; ?>> Próxima a cerrarse
            </label>

            <input type="text" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar incidencia...">
            <button type="submit">Aplicar Filtros</button>
        </form>

        <form method="GET" action="exportar_incidencias.php">
    <input type="hidden" name="provincia" value="<?php echo htmlspecialchars($provincia_filter); ?>">
    <input type="hidden" name="prioridad" value="<?php echo htmlspecialchars($prioridad_filter); ?>">
    <input type="hidden" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>">
    <input type="hidden" name="proxima_cierre" value="<?php echo $proxima_cierre ? '1' : ''; ?>">

    <label for="formato">Exportar como:</label>
    <select name="formato" required>
        <option value="csv">CSV</option>
        <option value="pdf">PDF</option>
    </select>
    <button type="submit">Exportar</button>
</form>




       
        <form method="POST" action="procesar_acciones.php" id="incidencia-form">
    <table>
        <thead>
            <tr>
                <th class="checkbox-column">Seleccionar</th>
                <th>Título</th>
                <th>Fecha de Creación</th>
                <th>Nivel de Prioridad</th>
                <th>Estado</th>
                <th>Usuario</th>
                <th>Provincia</th>
                <th>Fecha de Cierre</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($incidencias as $incidencia): ?>
            <?php
                $plazo_dias = ['Baja' => 7, 'Media' => 5, 'Alta' => 3, 'Urgente' => 1];
                $fecha_creacion = new DateTime($incidencia['FECHA_CREACION']);
                $dias_para_cierre = $plazo_dias[$incidencia['NIVEL_PRIORIDAD']] ?? 7;
                $fecha_cierre = clone $fecha_creacion;
                $fecha_cierre->modify("+{$dias_para_cierre} days");
                $hoy = new DateTime();
                $alerta = ($hoy->diff($fecha_cierre)->days == 1 && $fecha_cierre > $hoy);
            ?>
            <tr <?php echo $alerta ? 'style="background-color: #ffcccc;"' : ''; ?>>
                <td class="checkbox-column">
                    <input type="checkbox" name="ids[]" value="<?php echo $incidencia['ID_INCIDENCIA']; ?>">
                </td>
                <td><?php echo htmlspecialchars($incidencia['TITULO']); ?></td>
                <td><?php echo htmlspecialchars($incidencia['FECHA_CREACION']); ?></td>
                <td>
                    <div class="priority-container">
                        <span class="priority <?php echo htmlspecialchars($incidencia['NIVEL_PRIORIDAD']); ?>">
                            <?php echo htmlspecialchars($incidencia['NIVEL_PRIORIDAD']); ?>
                        </span>
                    </div>
                </td>
                <td><?php echo htmlspecialchars($incidencia['ESTADO']); ?></td>
                <td><?php echo htmlspecialchars($incidencia['USUARIO']); ?></td>
                <td><?php echo htmlspecialchars($incidencia['PROVINCIA']); ?></td>
                <td><?php echo $fecha_cierre->format('Y-m-d'); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="fixed-bar">
        <span>Tiempo restante de sesión: <span id="contadorSesion">30:00</span></span>
        
        <button type="submit" name="accion" value="eliminar">Eliminar Seleccionadas</button>
        <button type="submit" name="accion" value="editar">Editar Incidencia</button>
        <button type="button" onclick="window.location.href='dashboard.php'">Volver al Inicio</button>
    </div>
</form>

</body>
</html>