<?php
session_start(); // Inicia la sesión
include(__DIR__ . '/../config/db_config.php'); // Asegúrate de que la ruta sea correcta

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: altaLogin.php"); // Redirige si no ha iniciado sesión
    exit();
}

// Verificar si el usuario_id está definido en la sesión
if (!isset($_SESSION['usuario_id'])) {
    die("Error: ID de usuario no encontrado en la sesión.");
}

// Obtener los datos del usuario desde la base de datos
$user_id = $_SESSION['usuario_id']; // Obtiene el ID del usuario
$sql = "SELECT nombre, email FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "No se encontraron datos del usuario.";
    exit();
}

// Manejo del formulario de edición
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];

    // Actualizar los datos del usuario
    $update_sql = "UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);

    if ($update_stmt === false) {
        die("Error en la preparación de la consulta de actualización: " . $conn->error);
    }

    $update_stmt->bind_param("ssi", $nombre, $email, $user_id);

    if ($update_stmt->execute()) {
        echo "Perfil actualizado correctamente.";
        // Opcional: Redirigir a otra página o mostrar un mensaje de éxito
    } else {
        echo "Error al actualizar el perfil: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title> Perfil de Usuario</title>
</head>
<body>
    <h2>Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?></h2>
    <form method="POST" action="">
        <div>
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
        </div>
        <div>
            <label for="email">Correo Electrónico:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <button type="submit">Actualizar Perfil</button>
    </form>
    <a href="logout.php">Cerrar Sesión</a>
</body>
</html>