<?php
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin'])) {
    header("Location: ../php/altaLogin.php");
    exit();
}

include(__DIR__ . '/../config/db_config.php');

$sqlUsuarios = "SELECT id_usuario, nombre, correo, tipo FROM Usuarios";
$resultUsuarios = $conn->query($sqlUsuarios);
?>

<?php include(__DIR__ . '/../includes/header.php'); ?>

<div class="container mt-5">
    <h1 class="text-center">Gestión de Usuarios</h1>
    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultUsuarios->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id_usuario']); ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['correo']); ?></td>
                    <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                    <td>
                        <a href="admin_edit_user.php?id=<?php echo $row['id_usuario']; ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="admin_delete_user.php?id=<?php echo $row['id_usuario']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include(__DIR__ . '/../includes/footer.php'); ?>