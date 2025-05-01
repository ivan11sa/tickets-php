<?php
// Inicia la sesión para gestionar variables de sesión
session_start();

// Incluye el archivo de conexión a la base de datos
require 'db_connection.php';

// Incluye un archivo de control (posiblemente para verificar permisos u otras validaciones)
require 'control.php';

// Configura la visualización de errores para facilitar la depuración en desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verifica si el usuario ha iniciado sesión, si no, lo redirige a la página de inicio de sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obtiene el ID del usuario desde la sesión
$user_id = $_SESSION['user_id'];

// Obtiene la lista de provincias desde la base de datos
$sql = "SELECT ID_PROVINCIA, NOMBRE_PROVINCIA FROM PROVINCIAS ORDER BY NOMBRE_PROVINCIA ASC";
$stmt = $conn->prepare($sql);

// Verifica si la consulta SQL se ha preparado correctamente
if (!$stmt) {
    die("Error en la consulta SQL: " . $conn->error);
}

// Ejecuta la consulta y obtiene el resultado
$stmt->execute();
$result = $stmt->get_result();
$provincias = $result->fetch_all(MYSQLI_ASSOC);

// Si no hay provincias en la base de datos, muestra un error
if (empty($provincias)) {
    die("Error: No se encontraron provincias en la base de datos.");
}

// Variables para gestionar la creación de una incidencia
$incidencia_creada = false;
$incidencia_data = [];

// Verifica si el formulario se envió por el método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene y sanitiza los datos enviados por el usuario
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $nivel_prioridad = $_POST['nivel_prioridad'] ?? '';
    $id_provincia = $_POST['provincia'] ?? '';

    // Verifica que todos los campos obligatorios estén completos
    if (empty($titulo) || empty($descripcion) || empty($nivel_prioridad) || empty($id_provincia)) {
        die("Error: Todos los campos son obligatorios.");
    }

    // Verifica que la provincia seleccionada existe en la base de datos
    $stmt = $conn->prepare("SELECT ID_PROVINCIA FROM PROVINCIAS WHERE ID_PROVINCIA = ?");
    $stmt->bind_param("i", $id_provincia);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        die("Error: La provincia seleccionada no existe en la base de datos.");
    }

    // Define los días de resolución según el nivel de prioridad
    $plazo_dias = ['Baja' => 7, 'Media' => 5, 'Alta' => 3, 'Urgente' => 1];

    // Calcula la fecha de creación y la fecha estimada de cierre
    $fecha_creacion = new DateTime();
    $dias_para_cierre = $plazo_dias[$nivel_prioridad] ?? 7;
    $fecha_cierre = clone $fecha_creacion;
    $fecha_cierre->modify("+$dias_para_cierre days");

    // Prepara la consulta para insertar la incidencia en la base de datos
    $stmt = $conn->prepare("INSERT INTO INCIDENCIAS (TITULO, DESCRIPCION, NIVEL_PRIORIDAD, ID_PROVINCIA, ID) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("sssii", $titulo, $descripcion, $nivel_prioridad, $id_provincia, $user_id);

    // Ejecuta la consulta y verifica si se creó correctamente la incidencia
    if ($stmt->execute()) {
        $incidencia_creada = true;
        $incidencia_data = [
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'nivel_prioridad' => $nivel_prioridad,
            'provincia' => array_column($provincias, 'NOMBRE_PROVINCIA', 'ID_PROVINCIA')[$id_provincia],
            'fecha_cierre' => $fecha_cierre->format('Y-m-d')
        ];
    } else {
        die("Error al crear la incidencia: " . $stmt->error);
    }
}

// Tiempo restante de sesión antes de la expiración
$max_inactividad = 1800;
$tiempo_restante = isset($_SESSION['last_activity']) ? max($max_inactividad - (time() - $_SESSION['last_activity']), 0) : $max_inactividad;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Incidencia</title>
    <link rel="stylesheet" href="incidencia.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<div class="container">
    <h2>Crear Nueva Incidencia</h2>
    
    <!-- Formulario para registrar una nueva incidencia -->
    <form action="crear_incidencia.php" method="POST">
        <div class="form-group">
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" required>
        </div>

        <div class="form-group">
            <label for="nivel_prioridad">Nivel de Prioridad:</label>
            <select id="nivel_prioridad" name="nivel_prioridad" required>
                <option value="">Seleccione nivel</option>
                <option value="Baja" class="Baja">Baja</option>
                <option value="Media" class="Media">Media</option>
                <option value="Alta" class="Alta">Alta</option>
                <option value="Urgente" class="Urgente">Urgente</option>
            </select>
        </div>

        <div class="form-group">
            <label for="provincia">Provincia:</label>
            <select id="provincia" name="provincia" required>
                <option value="">Seleccione una provincia</option>
                <?php foreach ($provincias as $provincia): ?>
                    <option value="<?php echo htmlspecialchars($provincia['ID_PROVINCIA']); ?>">
                        <?php echo htmlspecialchars($provincia['NOMBRE_PROVINCIA']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required></textarea>
        </div>

        <button type="submit" class="btn"><i class="fa-solid fa-plus"></i> Crear Incidencia</button>
    </form>
</div>

<!-- Barra fija con el tiempo de sesión -->
<div class="fixed-bar">
    <span>Tiempo transcurrido desde inicio de sesión: <?php printf("%02d:%02d:%02d", $horas, $minutos, $segundos); ?></span>
    <button type="button" onclick="window.location.href='dashboard.php'">Volver al Inicio</button>
</div>

</body>
</html>
