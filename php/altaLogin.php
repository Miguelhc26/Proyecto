<?php
session_start();
include('../config/db_config.php'); // Conexión con la base de datos

// Si ya está autenticado, lo redirigimos al Dashboard
if (isset($_SESSION['usuario'])) {
    header("Location: ../dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - MoveSync</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
        <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
            <h2 class="text-center fw-bold text-primary mb-4">Iniciar Sesión</h2>
            <form action="procesarLogin.php" method="POST">
                <div class="mb-3">
                    <label for="correo" class="form-label fw-bold">Correo Electrónico</label>
                    <input type="email" name="correo" class="form-control" placeholder="Ingrese su correo" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label fw-bold">Contraseña</label>
                    <input type="password" name="password" class="form-control" placeholder="Ingrese su contraseña" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="terms" required>
                    <label class="form-check-label" for="terms">Acepto los <a href="#" class="text-primary">términos y condiciones</a></label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
            </form>
            <div class="text-center mt-3">
            <p>¿No tienes cuenta creada? <a href="register.php" class="text-primary">Regístrate aquí</a></p>

            </div>
        </div>
    </div>
    
    <?php include('../includes/footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
