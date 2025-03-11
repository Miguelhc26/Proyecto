<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirige al login si no ha iniciado sesión
    exit();
}

$username = $_SESSION['username']; // Obtiene el nombre del usuario
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    <link rel="stylesheet" href="welcomecss.css">
</head>
<body>
    <div class="welcome-container">
        <h1>¡Bienvenido, <?php echo htmlspecialchars($username); ?>!</h1>
    </div>
</body>
</html>
