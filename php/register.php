<?php
session_start();
include('../config/db_config.php'); // Conexión con la base de datos

// Si ya está autenticado, lo redirigimos al Dashboard
if (isset($_SESSION['usuario'])) {
    header("Location: ../dashboard.php");
    exit();
}

// Procesar el registro del usuario
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
        // Redirección al login con un mensaje de éxito
        echo "<script>alert('Registro exitoso. Ahora puedes iniciar sesión.'); window.location.href = 'altaLogin.php';</script>";
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - MoveSync</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
        <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
            <h2 class="text-center fw-bold text-primary mb-4">Registro de Usuario</h2>
            <form action="register.php" method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label fw-bold">Nombre Completo</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ingrese su nombre" required>
                </div>
                <div class="mb-3">
                    <label for="correo" class="form-label fw-bold">Correo Electrónico</label>
                    <input type="email" name="correo" class="form-control" placeholder="Ingrese su correo" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label fw-bold">Contraseña</label>
                    <input type="password" name="password" class="form-control" placeholder="Cree una contraseña" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="terms" required>
                    <label class="form-check-label" for="terms">Acepto los <a href="#" class="text-primary">términos y condiciones</a></label>
                </div>
                <button type="submit" class="btn btn-success w-100">Registrarse</button>
            </form>
            <div class="text-center mt-3">
                <p>¿Ya tienes una cuenta? <a href="altaLogin.php" class="text-primary">Inicia sesión aquí</a></p>
            </div>
        </div>
    </div>
    
    <?php include('../includes/footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
