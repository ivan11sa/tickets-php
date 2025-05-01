<?php
session_start();
require 'db_connection.php';

// Mostrar errores en caso de fallo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Obtener la lista de provincias desde la base de datos
$sql = "SELECT ID_PROVINCIA, NOMBRE_PROVINCIA FROM PROVINCIAS ORDER BY NOMBRE_PROVINCIA ASC";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error en la consulta SQL: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();
$provincias = $result->fetch_all(MYSQLI_ASSOC);

if (empty($provincias)) {
    die("Error: No se encontraron provincias en la base de datos.");
}

$incidencia_creada = false;
$incidencia_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $nivel_prioridad = $_POST['nivel_prioridad'] ?? '';
    $id_provincia = $_POST['provincia'] ?? '';

    if (empty($titulo) || empty($descripcion) || empty($nivel_prioridad) || empty($id_provincia)) {
        die("Error: Todos los campos son obligatorios.");
    }

    $stmt = $conn->prepare("SELECT ID_PROVINCIA FROM PROVINCIAS WHERE ID_PROVINCIA = ?");
    $stmt->bind_param("i", $id_provincia);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        die("Error: La provincia seleccionada no existe en la base de datos.");
    }

    $stmt = $conn->prepare("INSERT INTO INCIDENCIAS (TITULO, DESCRIPCION, NIVEL_PRIORIDAD, ID_PROVINCIA, ID) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("sssii", $titulo, $descripcion, $nivel_prioridad, $id_provincia, $user_id);

    if ($stmt->execute()) {
        $incidencia_creada = true;
        $incidencia_data = [
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'nivel_prioridad' => $nivel_prioridad,
            'provincia' => array_column($provincias, 'NOMBRE_PROVINCIA', 'ID_PROVINCIA')[$id_provincia],
        ];
    } else {
        die("Error al crear la incidencia: " . $stmt->error);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Incidencia</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group textarea {
            width: 100%;
            height: 60px;
            resize: none;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            text-decoration: none;
            background-color: #007BFF;
            color: white;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .fixed-bar {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: #007BFF;
            padding: 15px;
            text-align: left;
            font-size: 18px;
            color: white;
        }
        .fixed-bar a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding-left: 20px;
        }
        select option[value="Baja"] { background-color: green; color: white; }
        select option[value="Media"] { background-color: orange; color: white; }
        select option[value="Alta"] { background-color: red; color: white; }
        select option[value="Urgente"] { background-color: darkred; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Crear Nueva Incidencia</h2>
        <form action="crear_incidencia.php" method="POST">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" required>
                <label for="nivel_prioridad">Nivel de Prioridad:</label>
                <select id="nivel_prioridad" name="nivel_prioridad" required>
                    <option value="Baja">Baja</option>
                    <option value="Media">Media</option>
                    <option value="Alta">Alta</option>
                    <option value="Urgente">Urgente</option>
                </select>
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
            <button type="submit" class="btn">Crear Incidencia</button>
        </form>
    </div>
    <div class="fixed-bar">
        <a href="dashboard.php">Volver al Inicio</a>
    </div>
</body>
</html>
