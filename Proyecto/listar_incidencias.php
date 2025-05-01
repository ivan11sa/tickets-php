<?php
session_start();
require 'db_connection.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['admin'] ?? 0;

try {
    if ($is_admin) {
        $sql = "SELECT i.ID_INCIDENCIA, i.TITULO, i.FECHA_CREACION, i.NIVEL_PRIORIDAD, i.ESTADO, 
                       u.NOMBRE AS USUARIO, p.NOMBRE_PROVINCIA AS PROVINCIA
                FROM INCIDENCIAS i
                JOIN USUARIOS u ON i.ID = u.ID
                JOIN PROVINCIAS p ON i.ID_PROVINCIA = p.ID_PROVINCIA";
        $stmt = $conn->prepare($sql);
    } else {
        $sql = "SELECT ID_INCIDENCIA, TITULO, FECHA_CREACION, NIVEL_PRIORIDAD, ESTADO 
                FROM INCIDENCIAS WHERE ID = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error en la consulta SQL: " . $conn->error);
        }
        $stmt->bind_param("i", $user_id);
    }

    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta SQL: " . $conn->error);
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
    <title>Listado de Incidencias</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        
        tr:nth-child(odd) {
            background-color: rgba(176, 176, 176, 0.93); /* Blanco con transparencia */
        }
        tr:nth-child(even) {
            background-color: white; /* Totalmente blanco */
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        .fixed-bar {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: #007BFF;
            padding: 15px;
            text-align: center;
            font-size: 18px;
            color: white;
        }
        .fixed-bar button {
            background: white;
            color: #007BFF;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            margin: 0 10px;
        }
        .priority {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            display: inline-block;
            width: 100px;
            text-align: center;
            margin-bottom: 5px;
        }
        .Baja { background-color: green; }
        .priority-container:has(.Baja) { align-items: start !important;}
        .Media { background-color: orange;}
        .priority-container:has(.Media) { align-items: center !important;}
        .Alta { background-color: red;}
        .priority-container:has(.Alta) { align-items: end !important;}
        .Urgente { background-color: darkred;}
        .priority-container:has(.Urgente) { align-items: end !important;}
        .priority-container {
            display: flex;
            flex-direction: column-reverse;
            align-items: flex-start;
        }
        /* Ajuste del checkbox */
        .checkbox-column {
            width: 40px;
            text-align: center;
        }
        input[type="checkbox"] {
            transform: scale(0.8);
            cursor: pointer;
        }
    </style>
    <script>
        function modifyDescription() {
            const selectedIds = [];
            document.querySelectorAll('input[type=checkbox]:checked').forEach(checkbox => {
                selectedIds.push(checkbox.value);
            });
            if (selectedIds.length > 0) {
                window.location.href = 'modificar_descripcion.php?ids=' + selectedIds.join(',');
            } else {
                alert('Seleccione al menos una incidencia.');
            }
        }

        function goHome() {
            window.location.href = 'dashboard.php';
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Listado de Incidencias</h2>
        <table>
            <thead>
                <tr>
                    <th class="checkbox-column"></th> <!-- Nueva columna para los checkboxes -->
                    <th>Título</th>
                    <th>Fecha de Creación</th>
                    <th>Nivel de Prioridad</th>
                    <th>Estado</th>
                    <?php if ($is_admin): ?>
                        <th>Usuario</th>
                        <th>Provincia</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($incidencias as $incidencia): ?>
                <tr>
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
                    <?php if ($is_admin): ?>
                        <td><?php echo htmlspecialchars($incidencia['USUARIO']); ?></td>
                        <td><?php echo htmlspecialchars($incidencia['PROVINCIA']); ?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="fixed-bar">
        <button onclick="modifyDescription()">Modificar Descripción</button>
        <button onclick="goHome()">Volver al Inicio</button>
    </div>
</body>
</html>
