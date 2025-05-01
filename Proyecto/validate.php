<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    try {
        $sql = "SELECT ID, CONTRASENA, ADMIN FROM USUARIOS WHERE CORREO = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['CONTRASENA'])) {
                $_SESSION['user_id'] = $user['ID'];
                $_SESSION['is_admin'] = $user['ADMIN'];
                header("Location: dashboard.php");
                exit();
            } else {
                header("Location: login.php?error=Contraseña incorrecta");
                exit();
            }
        } else {
            header("Location: login.php?error=Usuario no encontrado");
            exit();
        }
    } catch (Exception $e) {
        error_log("Error en la autenticación: " . $e->getMessage());
        header("Location: login.php?error=Error en el sistema, intente más tarde.");
        exit();
    }
}
?>