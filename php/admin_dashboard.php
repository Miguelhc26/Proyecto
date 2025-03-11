<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin'])) {
    header("Location: ../php/altaLogin.php");
    exit();
}

include(__DIR__ . '/../config/db_config.php');

// Contar usuarios registrados
$sqlUsuarios = "SELECT COUNT(*) AS total FROM Usuarios";
$resultUsuarios = $conn->query($sqlUsuarios);
$totalUsuarios = $resultUsuarios->fetch_assoc()['total'];

// Contar reportes de incidencias pendientes
$sqlReportes = "SELECT COUNT(*) AS total FROM Incidencias WHERE estado='Pendiente'";
$resultReportes = $conn->query($sqlReportes);
$totalReportes = $resultReportes->fetch_assoc()['total'];

// Contar rutas registradas
$sqlRutas = "SELECT COUNT(*) AS total FROM Rutas";
$resultRutas = $conn->query($sqlRutas);
$totalRutas = $resultRutas->fetch_assoc()['total'];
?>

<?php include(__DIR__ . '/../includes/header.php'); ?>

<div class="container mt-5">
    <h1 class="text-center">Panel de Administración</h1>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card shadow-lg p-3 mb-5 bg-body rounded">
                <div class="card-body">
                    <h5 class="card-title">Usuarios Registrados</h5>
                    <p class="card-text display-4 text-primary"><?php echo $totalUsuarios; ?></p>
                    <a href="admin_users.php" class="btn btn-primary w-100">Gestionar Usuarios</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-lg p-3 mb-5 bg-body rounded">
                <div class="card-body">
                    <h5 class="card-title">Reportes Pendientes</h5>
                    <p class="card-text display-4 text-danger"><?php echo $totalReportes; ?></p>
                    <a href="admin_reports.php" class="btn btn-danger w-100">Ver Reportes</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-lg p-3 mb-5 bg-body rounded">
                <div class="card-body">
                    <h5 class="card-title">Rutas Disponibles</h5>
                    <p class="card-text display-4 text-success"><?php echo $totalRutas; ?></p>
                    <a href="admin_routes.php" class="btn btn-success w-100">Gestionar Rutas</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include(__DIR__ . '/../includes/footer.php'); ?>
