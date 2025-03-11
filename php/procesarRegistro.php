<?php
session_start();
include('../config/db_config.php'); // Conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Comprobar si el correo ya está registrado
    $checkEmail = "SELECT * FROM Usuarios WHERE correo='$correo'";
    $result = $conn->query($checkEmail);
    if ($result->num_rows > 0) {
        echo "<script>alert('El correo ya está registrado.'); window.location.href = 'register.php';</script>";
        exit();
    }

    // Insertar el nuevo usuario en la base de datos
    $sql = "INSERT INTO Usuarios (nombre, correo, contrasena, tipo) VALUES ('$nombre', '$correo', '$password', 'Usuario')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Registro exitoso. Ahora puedes iniciar sesión.'); window.location.href = 'altaLogin.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>