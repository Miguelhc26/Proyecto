<?php
session_start();
include('../config/db_config.php'); // Conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM Usuarios WHERE correo='$correo'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['contrasena'])) {
            $_SESSION['usuario'] = $user['id_usuario'];
            
            if ($user['tipo'] == "Administrador") {
                $_SESSION['admin'] = true;
                header("Location: ../php/admin_dashboard.php");
            } else {
                header("Location: ../dashboard.php");
            }
            exit();
        } else {
            echo "<script>alert('Contraseña incorrecta'); window.location.href = 'altaLogin.php';</script>";
        }
    } else {
        echo "<script>alert('Usuario no encontrado'); window.location.href = 'altaLogin.php';</script>";
    }
}
?>