<?php
session_start();
include('config/db_config.php');
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM Usuarios";
$result = $conn->query($sql);
?>
<?php include('includes/navbar_admin.php'); ?>
<div class="container mt-5">
    <h1 class="text-center">Gestión de Usuarios</h1>
    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Tipo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id_usuario']; ?></td>
                    <td><?php echo $row['nombre']; ?></td>
                    <td><?php echo $row['correo']; ?></td>
                    <td><?php echo $row['tipo']; ?></td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $row['id_usuario']; ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="delete_user.php?id=<?php echo $row['id_usuario']; ?>" class="btn btn-sm btn-danger">Eliminar</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php include('includes/footer.php'); ?>
