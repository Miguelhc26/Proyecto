<?php
session_start();
include('config/db_config.php');
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM Incidencias";
$result = $conn->query($sql);
?>
<?php include('includes/navbar_admin.php'); ?>
<div class="container mt-5">
    <h1 class="text-center">Gestión de Reportes</h1>
    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Ruta</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id_incidencia']; ?></td>
                    <td><?php echo $row['id_usuario']; ?></td>
                    <td><?php echo $row['id_ruta']; ?></td>
                    <td><?php echo $row['descripcion']; ?></td>
                    <td><?php echo $row['estado']; ?></td>
                    <td>
                        <a href="resolve_report.php?id=<?php echo $row['id_incidencia']; ?>" class="btn btn-sm btn-success">Resolver</a>
                        <a href="delete_report.php?id=<?php echo $row['id_incidencia']; ?>" class="btn btn-sm btn-danger">Eliminar</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php include('includes/footer.php'); ?>
