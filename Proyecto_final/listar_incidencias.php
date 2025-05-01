<?php
// Inicia la sesión para gestionar la autenticación del usuario
session_start();

// Incluye los archivos necesarios para la conexión a la base de datos y el control de acceso
require 'db_connection.php';
require 'control.php';

// Actualiza automáticamente el estado de las incidencias vencidas a "Cerrada"
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

// Verifica si el usuario ha iniciado sesión, de lo contrario, lo redirige al login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obtiene el ID del usuario desde la sesión
$user_id = $_SESSION['user_id'];


// Consulta para obtener las incidencias del usuario autenticado
$sql = "SELECT ID_INCIDENCIA, TITULO, FECHA_CREACION, NIVEL_PRIORIDAD, ESTADO 
        FROM INCIDENCIAS WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$incidencias = $result->fetch_all(MYSQLI_ASSOC);

// Filtrado de búsqueda por título de la incidencia
$busqueda = isset($_POST['busqueda']) ? strtolower(trim($_POST['busqueda'])) : '';
if ($busqueda) {
    $incidencias = array_filter($incidencias, function ($incidencia) use ($busqueda) {
        return strpos(strtolower($incidencia['TITULO']), $busqueda) !== false;
    });
}

// Manejo de la modificación de la descripción de incidencias seleccionadas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modificar'])) {
    if (!empty($_POST['ids'])) {
        // Convierte los ID seleccionados en una cadena separada por comas y redirige a la página de modificación
        $ids = implode(',', array_map('intval', $_POST['ids']));
        header("Location: modificar_descripcion.php?ids=$ids");
        exit();
    } else {
        $error = "Seleccione al menos una incidencia.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Incidencias</title>
    <link rel="stylesheet" href="styles1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2>Mis Incidencias</h2>

        <!-- Formulario de búsqueda -->
        <div class="filters">
            <form method="post">
                <input type="text" name="busqueda" placeholder="Buscar incidencia..." value="<?php echo htmlspecialchars($busqueda); ?>">
                <button type="submit">Buscar</button>
            </form>
        </div>

        <!-- Tabla con la lista de incidencias -->
        <form method="post">
            <table>
                <thead>
                    <tr>
                        <th class="checkbox-column"></th>
                        <th>Título</th>
                        <th>Fecha de Creación</th>
                        <th>Nivel de Prioridad</th>
                        <th>Estado</th>
                        <th>Fecha de Cierre</th>
                        <th>Comentarios</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($incidencias as $incidencia): ?>
                        <?php
                            // Calcula la fecha de cierre de la incidencia según su nivel de prioridad
                            $plazo_dias = [
                                'Baja' => 7,
                                'Media' => 5,
                                'Alta' => 3,
                                'Urgente' => 1
                            ];
                            $fecha_creacion = new DateTime($incidencia['FECHA_CREACION']);
                            $dias_para_cierre = $plazo_dias[$incidencia['NIVEL_PRIORIDAD']] ?? 7;
                            $fecha_cierre = clone $fecha_creacion;
                            $fecha_cierre->modify("+$dias_para_cierre days");
                            
                            // Calcula si la incidencia está próxima a cerrarse
                            $hoy = new DateTime();
                            $diferencia = $hoy->diff($fecha_cierre)->days;
                            $alerta = ($diferencia == 1 && $fecha_cierre > $hoy);
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
                            <td><?php echo $fecha_cierre->format('Y-m-d'); ?></td>
                            <td><a href="ver_incidencia.php?id=<?= $incidencia['ID_INCIDENCIA'] ?>">Ver</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Barra fija con opciones -->
            <div class="fixed-bar">
                <span>Tiempo transcurrido desde inicio de sesión: <?php printf("%02d:%02d:%02d", $horas, $minutos, $segundos); ?></span>
                <button type="submit" name="modificar">Modificar Descripción</button>
                <button type="button" onclick="window.location.href='dashboard.php'">Volver al Inicio</button>
            </div>
        </form>

        <!-- Muestra errores si hay problemas con la selección -->
        <?php if (isset($error)): ?>
            <p style="color: red;"> <?php echo $error; ?> </p>
        <?php endif; ?>
    </div>
</body>
</html>
