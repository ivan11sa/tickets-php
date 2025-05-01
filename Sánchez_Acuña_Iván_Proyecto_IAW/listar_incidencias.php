<?php
// Este archivo permite a los usuarios ver, filtrar, buscar y eliminar incidencias.

//  Iniciar la sesión para manejar la autenticación.
session_start();

//  Incluir conexión a la base de datos y control de acceso.
require 'db_connection.php';
require 'control.php';

//  Verificar si el usuario ha iniciado sesión, si no, redirigir al login.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Debes iniciar sesión.");
    exit();
}

// Obtener el ID del usuario autenticado.
$user_id = $_SESSION['user_id'];

// Cierra incidencias vencidas automáticamente.
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
$conn->query($sql_update);

// Filtrado de búsqueda por título de la incidencia.
$busqueda = isset($_POST['busqueda']) ? trim($_POST['busqueda']) : '';

$sql = "SELECT ID_INCIDENCIA, TITULO, FECHA_CREACION, NIVEL_PRIORIDAD, ESTADO 
        FROM INCIDENCIAS 
        WHERE ID = ?";

// Agregar búsqueda por título si se ingresó un término.
if (!empty($busqueda)) {
    $sql .= " AND LOWER(TITULO) LIKE ?";
}

$stmt = $conn->prepare($sql);
if (!empty($busqueda)) {
    $busqueda_param = '%' . strtolower($busqueda) . '%';
    $stmt->bind_param("is", $user_id, $busqueda_param);
} else {
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
$incidencias = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

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

// Eliminar múltiples incidencias seleccionadas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_masivo'])) {
    if (!empty($_POST['ids'])) {
        $ids = array_map('intval', $_POST['ids']);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $sql = "DELETE FROM INCIDENCIAS WHERE ID_INCIDENCIA IN ($placeholders) AND ID = ?";
        $stmt = $conn->prepare($sql);

        // Generar los parámetros de la consulta
        $params = array_merge($ids, [$user_id]);
        $types = str_repeat('i', count($params));
        $stmt->bind_param($types, ...$params);

        $stmt->execute();
        $stmt->close();

        header("Location: listar_incidencias.php?success=Incidencias eliminadas");
        exit();
    } else {
        $error = "Seleccione al menos una incidencia para eliminar.";
    }
}

// Redirigir a la página de edición de incidencias seleccionadas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_masivo'])) {
    if (!empty($_POST['ids'])) {
        $ids = implode(',', array_map('intval', $_POST['ids']));
        header("Location: editar_incidencia.php?ids=$ids");
        exit();
    } else {
        $error = "Seleccione al menos una incidencia para editar.";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Incidencias</title>
    <link rel="stylesheet" href="css/styles1.css">
</head>
<body>
    <div class="container2">
        <h2>Mis Incidencias</h2>

        <!-- Formulario de búsqueda -->
        <form method="post">
            <input type="text" name="busqueda" placeholder="Buscar incidencia..." value="<?php echo htmlspecialchars($busqueda); ?>">
            <button type="submit">Buscar</button>
        </form>

        <!-- Muestra mensajes de error o éxito si existen -->
        <?php if (isset($_GET['error'])): ?>
            <p class="message error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <p class="message success"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php endif; ?>

        <!-- Tabla con incidencias -->
        <div class="table-container2">
        <form method="post"action="procesar_acciones.php">
            <input type="hidden" name="origen" value="listar_incidencias.php">
            <table>
                <thead>
                    <tr>
                        <th>Seleccionar</th>
                        <th>Título</th>
                        <th>Fecha de Creación</th>
                        <th>Nivel de Prioridad</th>
                        <th>Estado</th>
                        <th>Fecha de Cierre</th>
                        <th>Ver</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($incidencias as $incidencia): ?>
                        <?php
                            // Calcula la fecha de cierre de la incidencia.
                            $plazo_dias = ['Baja' => 7, 'Media' => 5, 'Alta' => 3, 'Urgente' => 1];
                            $fecha_creacion = new DateTime($incidencia['FECHA_CREACION']);
                            $dias_para_cierre = $plazo_dias[$incidencia['NIVEL_PRIORIDAD']] ?? 7;
                            $fecha_cierre = clone $fecha_creacion;
                            $fecha_cierre->modify("+$dias_para_cierre days");
                            
                            // Resaltar incidencias que están por cerrarse.
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
                                </div></td>
                            <td><?php echo htmlspecialchars($incidencia['ESTADO']); ?></td>
                            <td><?php echo $fecha_cierre->format('Y-m-d'); ?></td>
                            <td><a href="ver_incidencia.php?id=<?= $incidencia['ID_INCIDENCIA'] ?>">Ver Comentarios</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Barra fija con opciones -->
            <div class="fixed-bar">       
                <span>Tiempo transcurrido desde inicio de sesión: <?php printf("%02d:%02d:%02d", $horas, $minutos, $segundos); ?></span>
                <button type="submit" name="accion" value="eliminar">Eliminar Seleccionadas</button>
                <button type="submit" name="accion" value="modificar_descripcion">Modificar Descripción</button>
                <button type="submit" name="accion" value="editar">Editar Incidencia</button>
                <button type="button" onclick="window.location.href='dashboard.php'">
                    <i class="fa-solid fa-arrow-left"></i> Volver al Inicio
                </button>
            </div>
        </form>
                    </div>
    </div>
</body>
</html>
