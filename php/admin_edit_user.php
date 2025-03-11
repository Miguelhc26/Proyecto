<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin'])) {
    header("Location: ../php/altaLogin.php");
    exit();
}

include(__DIR__ . '/../config/db_config.php');

if (!isset($_GET['id'])) {
    echo "<script>alert('Usuario no encontrado'); window.location.href='admin_users.php';</script>";
    exit();
}

$id_usuario = $_GET['id'];
$sqlUsuario = "SELECT * FROM Usuarios WHERE id_usuario = $id_usuario";
$resultUsuario = $conn->query($sqlUsuario);

if ($resultUsuario->num_rows == 0) {
    echo "<script>alert('Usuario no encontrado'); window.location.href='admin_users.php';</script>";
    exit();
}

$usuario = $resultUsuario->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $tipo = $_POST['tipo'];

    $sqlUpdate = "UPDATE Usuarios SET nombre='$nombre', correo='$correo', tipo='$tipo' WHERE id_usuario=$id_usuario";
    if ($conn->query($sqlUpdate) === TRUE) {
        echo "<script>alert('Usuario actualizado exitosamente'); window.location.href='admin_users.php';</script>";
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<?php include(__DIR__ . '/../includes/header.php'); ?>

<div class="container mt-5">
    <h1 class="text-center">Editar Usuario</h1>
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre Completo</label>
            <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="correo" class="form-label">Correo Electrónico</label>
            <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="tipo" class="form-label">Rol</label>
            <select name="tipo" class="form-select" required>
                <option value="Usuario" <?php if ($usuario['tipo'] == 'Usuario') echo 'selected'; ?>>Usuario</option>
                <option value="Administrador" <?php if ($usuario['tipo'] == 'Administrador') echo 'selected'; ?>>Administrador</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success w-100">Actualizar Usuario</button>
    </form>
</div>

<?php include(__DIR__ . '/../includes/footer.php'); ?>
