<?php
session_start();
include('config/db_config.php');
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM Rutas";
$result = $conn->query($sql);
?>
<?php include('includes/navbar_admin.php'); ?>
<div class="container mt-5">
    <h1 class="text-center">Gestión de Rutas</h1>
    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Origen</th>
                <th>Destino</th>
                <th>Horario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id_ruta']; ?></td>
                    <td><?php echo $row['origen']; ?></td>
                    <td><?php echo $row['destino']; ?></td>
                    <td><?php echo $row['horario']; ?></td>
                    <td>
                        <a href="edit_route.php?id=<?php echo $row['id_ruta']; ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="delete_route.php?id=<?php echo $row['id_ruta']; ?>" class="btn btn-sm btn-danger">Eliminar</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php include('includes/footer.php'); ?>
