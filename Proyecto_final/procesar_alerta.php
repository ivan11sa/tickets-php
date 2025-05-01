<?php
// Inicia la sesión para gestionar la autenticación del usuario
session_start();

// Incluye la conexión a la base de datos y el archivo de control de acceso
require 'db_connection.php';
require 'control.php';

// Verifica si el usuario está autenticado y si tiene permisos de administrador
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// Verifica si la alerta de incidencias próximas a cerrarse ha sido cerrada por el usuario
if (isset($_SESSION['cerrar_alerta']) && $_SESSION['cerrar_alerta'] === true) {
    $alerta_incidencias = []; 
} else {
    $alerta_incidencias = [];

// Consulta para obtener incidencias abiertas que están próximas a cerrarse

    $sql_alerta = "SELECT TITULO FROM INCIDENCIAS 
                    WHERE ESTADO = 'Abierta' 
                    AND DATEDIFF(
                        DATE_ADD(FECHA_CREACION, INTERVAL 
                            CASE 
                                WHEN NIVEL_PRIORIDAD = 'Baja' THEN 7
                                WHEN NIVEL_PRIORIDAD = 'Media' THEN 5
                                WHEN NIVEL_PRIORIDAD = 'Alta' THEN 3
                                WHEN NIVEL_PRIORIDAD = 'Urgente' THEN 1
                                ELSE 7
                            END DAY), CURDATE()
                    ) = 1";

    $stmt_alerta = $conn->prepare($sql_alerta);
    $stmt_alerta->execute();
    $result_alerta = $stmt_alerta->get_result();
    
    // Guarda los títulos de las incidencias próximas a cerrarse
    while ($row = $result_alerta->fetch_assoc()) {
        $alerta_incidencias[] = $row['TITULO'];
    }
} 

// Si el usuario cierra la alerta, recarga la página sin mostrarla
if (isset($_GET['cerrar_alerta'])) {
    header("Location: listar_incidencias_admin.php"); 
    exit();
}

// Obtiene los filtros aplicados en la búsqueda de incidencias
$provincia_filter = $_GET['provincia'] ?? '';
$prioridad_filter = $_GET['prioridad'] ?? '';
$proxima_cierre = isset($_GET['proxima_cierre']) ? true : false;
$busqueda = $_GET['busqueda'] ?? '';

// Consulta para obtener la lista de provincias disponibles
$provincias = [];
$provincia_query = "SELECT NOMBRE_PROVINCIA FROM PROVINCIAS ORDER BY NOMBRE_PROVINCIA";
$provincia_stmt = $conn->prepare($provincia_query);
$provincia_stmt->execute();
$provincia_result = $provincia_stmt->get_result();

// Guarda los nombres de provincias en un array
while ($row = $provincia_result->fetch_assoc()) {
    $provincias[] = $row['NOMBRE_PROVINCIA'];
}


// Consulta para obtener incidencias aplicando filtros de búsqueda

$sql = "SELECT 
    i.ID_INCIDENCIA, 
    i.TITULO, 
    i.FECHA_CREACION, 
    i.NIVEL_PRIORIDAD, 
    i.ESTADO, 
    u.NOMBRE AS USUARIO, 
    p.NOMBRE_PROVINCIA AS PROVINCIA,
    DATE_ADD(i.FECHA_CREACION, INTERVAL t.DIAS DAY) AS FECHA_CIERRE,
    DATEDIFF(DATE_ADD(i.FECHA_CREACION, INTERVAL t.DIAS DAY), CURDATE()) AS DIAS_RESTANTES
FROM INCIDENCIAS i
INNER JOIN USUARIOS u ON i.ID = u.ID
INNER JOIN PROVINCIAS p ON i.ID_PROVINCIA = p.ID_PROVINCIA
INNER JOIN (
    SELECT 'Baja' AS PRIORIDAD, 7 AS DIAS UNION ALL
    SELECT 'Media', 5 UNION ALL
    SELECT 'Alta', 3 UNION ALL
    SELECT 'Urgente', 1
) t ON i.NIVEL_PRIORIDAD = t.PRIORIDAD";

    $params = [];
    $types = "";

    // Aplica filtros a la consulta si están definidos
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
        $sql .= " AND DATEDIFF(
                    DATE_ADD(i.FECHA_CREACION, INTERVAL 
                        CASE 
                            WHEN i.NIVEL_PRIORIDAD = 'Baja' THEN 7
                            WHEN i.NIVEL_PRIORIDAD = 'Media' THEN 5
                            WHEN i.NIVEL_PRIORIDAD = 'Alta' THEN 3
                            WHEN i.NIVEL_PRIORIDAD = 'Urgente' THEN 1
                            ELSE 7
                        END DAY), CURDATE()
                ) = 1";
    }
    if (!empty($busqueda)) {
        $sql .= " AND (i.TITULO LIKE ? OR u.NOMBRE LIKE ?)";
        $busqueda_param = "%{$busqueda}%";
        $params[] = &$busqueda_param;
        $params[] = &$busqueda_param;
        $types .= "ss";
    }

    // Prepara y ejecuta la consulta SQL
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    
    if (!empty($params)) {
        if (!$stmt->bind_param($types, ...$params)) {
            die("Error en bind_param: " . $stmt->error);
        }
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $incidencias = $result->fetch_all(MYSQLI_ASSOC);


// Consulta adicional para actualizar la lista de alertas de incidencias
$alerta_incidencias = [];

$sql_alerta = "SELECT TITULO FROM INCIDENCIAS 
                WHERE ESTADO = 'Abierta' 
                AND DATEDIFF(
                    DATE_ADD(FECHA_CREACION, INTERVAL 
                        CASE 
                            WHEN NIVEL_PRIORIDAD = 'Baja' THEN 7
                            WHEN NIVEL_PRIORIDAD = 'Media' THEN 5
                            WHEN NIVEL_PRIORIDAD = 'Alta' THEN 3
                            WHEN NIVEL_PRIORIDAD = 'Urgente' THEN 1
                            ELSE 7
                        END DAY), CURDATE()
                ) = 1";

$stmt_alerta = $conn->prepare($sql_alerta);
$stmt_alerta->execute();
$result_alerta = $stmt_alerta->get_result();

// Guarda los títulos de incidencias que están próximas a cerrarse
while ($row = $result_alerta->fetch_assoc()) {
    $alerta_incidencias[] = $row['TITULO'];
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

        <!-- Muestra una alerta si hay incidencias próximas a cerrarse -->
        <?php if (!empty($alerta_incidencias)): ?>
            <div style="display: block;" class="alerta-incidencias">
                <strong>ATENCIÓN:</strong> Estas incidencias están próximas a cerrarse:
                <ul>
                    <?php foreach ($alerta_incidencias as $incidencia): ?>
                        <li><?php echo htmlspecialchars($incidencia); ?></li>
                    <?php endforeach; ?>
                </ul>
                <form method="POST" action="listar_incidencias_admin.php">
                    <button type="submit">Cerrar</button>
                </form>
            </div>
        <?php endif; ?>

        <!-- Formulario para filtrar las incidencias -->
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

        <!-- Formulario para exportar las incidencias a CSV o PDF -->
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

        <!-- Tabla que muestra la lista de incidencias -->
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
                            // Calcula la fecha de cierre según el nivel de prioridad de la incidencia
                            $plazo_dias = ['Baja' => 7, 'Media' => 5, 'Alta' => 3, 'Urgente' => 1];
                            $fecha_creacion = new DateTime($incidencia['FECHA_CREACION']);
                            $dias_para_cierre = $plazo_dias[$incidencia['NIVEL_PRIORIDAD']] ?? 7;
                            $fecha_cierre = clone $fecha_creacion;
                            $fecha_cierre->modify("+{$dias_para_cierre} days");

                            // Obtiene la fecha actual y verifica si la incidencia está próxima a cerrarse
                            $hoy = new DateTime();
                            $alerta = ($hoy->diff($fecha_cierre)->days == 1 && $fecha_cierre > $hoy);
                        ?>
                        <tr style="background-color: <?php echo ($incidencia['DIAS_RESTANTES'] == 1) ? '#ffcccc' : 'transparent'; ?>;">
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
                            <td><?php echo $fecha_cierre->format('Y-m-d'); ?> 
                                <br>
                                <span style="font-size: 12px; color: <?php echo ($incidencia['DIAS_RESTANTES'] <= 1) ? 'red' : 'green'; ?>">
                                    (<?php echo $incidencia['DIAS_RESTANTES']; ?> días restantes)
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Controles de acciones y navegación -->
            <div class="fixed-bar">
                <span>Tiempo transcurrido desde inicio de sesión: <?php printf("%02d:%02d:%02d", $horas, $minutos, $segundos); ?></span>
                <button type="submit" name="accion" value="eliminar">Eliminar Seleccionadas</button>
                <button type="submit" name="accion" value="editar">Editar Incidencia</button>
                <button type="button" onclick="window.location.href='dashboard.php'">Volver al Inicio</button>
            </div>
        </form>
    </div>
</body>
</html>
