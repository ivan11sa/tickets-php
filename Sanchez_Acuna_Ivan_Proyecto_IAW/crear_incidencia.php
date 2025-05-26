<?php
ob_start();
// Inicia la sesión para gestionar variables de sesión
session_start();

// Evitar que el navegador almacene en caché la página
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

// Incluye el archivo de conexión a la base de datos y el archivo que nos permite tener un sistema de control de uso y sesion activa.
require 'db_connection.php';
require 'control.php';

// Verifica si el usuario ha iniciado sesión, si no, lo redirige a la página de inicio de sesión con un mensaje de error.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Debe iniciar sesión para acceder.");
    exit();
}

// Obtiene el ID del usuario desde la sesión que hayamos iniciado en login.php.
$user_id = $_SESSION['user_id'];

// La lista de provincias no la tenemor configurada en un select en html asi que las obtiene desde la base de datos.
$sql = "SELECT ID_PROVINCIA, NOMBRE_PROVINCIA FROM PROVINCIAS ORDER BY NOMBRE_PROVINCIA ASC";
$stmt = $conn->prepare($sql);

// Ejecuta la consulta y obtiene el resultado
$stmt->execute();
$result = $stmt->get_result();
$provincias = $result->fetch_all(MYSQLI_ASSOC);


// Creamos un array para gestionar la creación de una incidencia. empezamos con una variable de incidencia creada false para que salgan los datos vacios. 
$incidencia_creada = false;
$incidencia_data = [];

// Primero comprobamos si el formulario se envió con el método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Se obtienen los datos enviados por el formulario y se eliminan espacios en blanco al inicio y al final.
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $nivel_prioridad = $_POST['nivel_prioridad'] ?? '';
    $id_provincia = $_POST['provincia'] ?? '';

    // Si TODOS los campos están vacíos, se muestra un mensaje de error.
    if (empty($titulo) && empty($descripcion) && empty($nivel_prioridad) && empty($id_provincia)) {
        $error = "Todos los campos están vacíos.";
    } else {

        // Aquí vamos a ver qué campos específicos están vacíos.
        // Creamos un array llamado $campo_vacio para guardar los nombres de los campos que no se hayan rellenado.
        $campo_vacio = [];

        // Comprobamos cada campo y si está vacío, lo agregamos al array.
        if (empty($titulo)) {
            $campo_vacio[] = "título";
        }
        if (empty($descripcion)) {
            $campo_vacio[] = "descripción";
        }
        if (empty($nivel_prioridad)) {
            $campo_vacio[] = "nivel de prioridad";
        }
        if (empty($id_provincia)) {
            $campo_vacio[] = "provincia";
        }

        // Si hay uno o más campos vacíos, se arma un mensaje de error indicando cuáles son.
        if (!empty($campo_vacio)) {
            // Se unen los nombres de los campos vacíos con una coma y se añade un mensaje apropiado.
            $error = "El campo " . implode(", ", $campo_vacio) . " " . (count($campo_vacio) === 1 ? "está" : "están") . " vacío" . (count($campo_vacio) === 1 ? "" : "s") . ".";
        }
    }


    // Solo si no hubo errores de campos vacíos, se comprueba que la provincia seleccionada exista en la base de datos.
    if (!isset($error)) {
        // Preparamos una consulta para buscar la provincia en la tabla PROVINCIAS.
        $stmt = $conn->prepare("SELECT ID_PROVINCIA FROM PROVINCIAS WHERE ID_PROVINCIA = ?");
        // Vinculamos el parámetro de la consulta con el valor enviado.
        $stmt->bind_param("i", $id_provincia);
        $stmt->execute();
        // Obtenemos el resultado de la consulta.
        $result_provincia = $stmt->get_result();

        // Si no se encontró la provincia, mostramos un error.
        if ($result_provincia->num_rows == 0) {
            $error = "Provincia seleccionada no válida.";
        }
    }

   
    // Si no se produjo ningún error en las validaciones anteriores, se inserta la incidencia en la base de datos.
    if (!isset($error)) {
        // Definimos cuántos días se tienen para resolver la incidencia según el nivel de prioridad.
        $plazo_dias = ['Baja' => 7, 'Media' => 5, 'Alta' => 3, 'Urgente' => 1];

        // Obtenemos la fecha actual para usarla como fecha de creación.
        $fecha_creacion = new DateTime();
        // Buscamos cuántos días se deben añadir según el nivel de prioridad. Si el nivel no existe, se usan 7 días.
        $dias_para_cierre = $plazo_dias[$nivel_prioridad] ?? 7;
        // Clonamos la fecha de creación para poder modificarla sin alterar la original.
        $fecha_cierre = clone $fecha_creacion;
        // Sumamos los días correspondientes para obtener la fecha estimada de cierre.
        $fecha_cierre->modify("+$dias_para_cierre days");

        // Preparamos la consulta para insertar la nueva incidencia en la base de datos.
        // Se utilizan parámetros en la consulta para evitar problemas de seguridad como la inyección SQL.
        $stmt = $conn->prepare("INSERT INTO INCIDENCIAS (TITULO, DESCRIPCION, NIVEL_PRIORIDAD, ID_PROVINCIA, ID) VALUES (?, ?, ?, ?, ?)");
        // Si la consulta no se puede preparar, se redirige a la página con un mensaje de error.
        if (!$stmt) {
            header("Location: crear_incidencia.php?error=Error al preparar la consulta.");
            exit();
        }
        // Se vinculan los valores a los parámetros de la consulta.
        $stmt->bind_param("sssii", $titulo, $descripcion, $nivel_prioridad, $id_provincia, $user_id);

        // Se ejecuta la consulta para insertar la incidencia.
        if ($stmt->execute()) {
            // Si la inserción es exitosa, marcamos la incidencia como creada y preparamos un resumen de los datos.
            $incidencia_creada = true;
            $incidencia_data = [
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'nivel_prioridad' => $nivel_prioridad,
                // Obtenemos el nombre de la provincia usando el array de provincias que se cargó previamente.
                'provincia' => array_column($provincias, 'NOMBRE_PROVINCIA', 'ID_PROVINCIA')[$id_provincia] ?? 'Desconocida',
                // Formateamos la fecha estimada de cierre para mostrarla.
                'fecha_cierre' => $fecha_cierre->format('Y-m-d'),
            ];

            // ¡Aquí vaciamos las variables para que el formulario aparezca limpio!
            $titulo = '';
            $descripcion = '';
            $nivel_prioridad = '';
            $id_provincia = '';
                }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Incidencia</title>
    <link rel="stylesheet" href="css/incidencia.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<?php if (!empty($_SESSION['show_warning'])): ?>
  <?php include 'session_warning.php'; ?>
<?php endif; ?>
<div class="container">
    <h2>Crear Nueva Incidencia</h2>

        <!-- Muestra mensajes de error o éxito si existen -->
        <?php if (!empty($error)): ?>
            <p class="message error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if ($incidencia_creada): ?>
            <p class="message success">Incidencia creada con éxito.</p>
        <?php endif; ?>

        <?php $mostrar_modal = $incidencia_creada ? 'style="display: flex;"' : 'style="display: none;"'; ?>
     
        <!-- Contenedor del alert para mostrar el resumen de la incidencia introducida.  -->
        <div class="modal-overlay" <?= $mostrar_modal; ?>>
            <div class="incidencia-modal">
                <!-- Botón para cerrar el modal -->
                <a href="crear_incidencia.php" class="modal-close">×</a>
                
                <h3>Resumen de la Incidencia Creada</h3>
                <p><strong>Título:</strong> <?= htmlspecialchars($incidencia_data['titulo'] ?? '') ?></p>
                <p><strong>Descripción:</strong> <?= htmlspecialchars($incidencia_data['descripcion'] ?? '') ?></p>
                <p><strong>Nivel de Prioridad:</strong> <?= htmlspecialchars($incidencia_data['nivel_prioridad'] ?? '') ?></p>
                <p><strong>Provincia:</strong> <?= htmlspecialchars($incidencia_data['provincia'] ?? '') ?></p>
                <p><strong>Fecha Estimada de Cierre:</strong> <?= htmlspecialchars($incidencia_data['fecha_cierre'] ?? '') ?></p>
            </div>
        </div>


    <!-- Formulario para registrar una nueva incidencia -->
    <form action="crear_incidencia.php" method="POST" novalidate autocomplete="off">
        <div class="form-group">
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($titulo ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="nivel_prioridad">Nivel de Prioridad:</label>
            <select id="nivel_prioridad" name="nivel_prioridad" required>
                <option value="">Seleccione nivel</option>
                <option value="Baja" class="Baja" <?php if(isset($nivel_prioridad) && $nivel_prioridad === "Baja") echo "selected"; ?>>Baja</option>
                <option value="Media" class="Media"<?php if(isset($nivel_prioridad) && $nivel_prioridad === "Media") echo "selected"; ?>>Media</option>
                <option value="Alta" class="Alta"<?php if(isset($nivel_prioridad) && $nivel_prioridad === "Alta") echo "selected"; ?>>Alta</option>
                <option value="Urgente" class="Urgente"<?php if(isset($nivel_prioridad) && $nivel_prioridad === "Urgente") echo "selected"; ?>>Urgente</option>
            </select>
        </div>

        <div class="form-group">
            <label for="provincia">Provincia:</label>
            <select id="provincia" name="provincia" required>
                <option value="">Seleccione una provincia</option>
                <?php foreach ($provincias as $provincia): ?>
                    <option value="<?php echo htmlspecialchars($provincia['ID_PROVINCIA']); ?>" <?php if(isset($id_provincia) && $id_provincia == $provincia['ID_PROVINCIA']) echo "selected"; ?>>
                        <?php echo htmlspecialchars($provincia['NOMBRE_PROVINCIA']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($descripcion ?? ''); ?></textarea>
        </div>

        <button type="submit" class="btn"><i class="fa-solid fa-plus"></i> Crear Incidencia</button>
    </form>
</div>

<!-- Barra fija con el tiempo de sesión -->
<div class="fixed-bar">
    <span>Tiempo transcurrido desde inicio de sesión: <?php printf("%02d:%02d:%02d", $horas, $minutos, $segundos); ?></span>
    <button type="button" onclick="window.location.href='dashboard.php'">
        <i class="fa-solid fa-arrow-left"></i> Volver al Inicio
    </button>
</div>

</body>
</html>
