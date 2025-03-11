<?php
session_start();
include('config/db_config.php');
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM Pagos";
$result = $conn->query($sql);
?>
<?php include('includes/navbar_admin.php'); ?>
<div class="container mt-5">
    <h1 class="text-center">Historial de Pagos</h1>
    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Monto</th>
                <th>Método</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id_pago']; ?></td>
                    <td><?php echo $row['id_usuario']; ?></td>
                    <td><?php echo $row['monto']; ?></td>
                    <td><?php echo $row['metodo_pago']; ?></td>
                    <td><?php echo $row['fecha_pago']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php include('includes/footer.php'); ?>
