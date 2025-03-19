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

// Contar reportes de incidencias resueltas
$sqlReportesResueltos = "SELECT COUNT(*) AS total FROM Incidencias WHERE estado='Resuelto'";
$resultReportesResueltos = $conn->query($sqlReportesResueltos);
$totalReportesResueltos = $resultReportesResueltos->fetch_assoc()['total'];

// Obtener las incidencias más recientes
$sqlUltimasIncidencias = "SELECT i.ID_Incidencia, i.Descripcion, i.categoria, i.Estado, i.fecha 
                          FROM Incidencias i 
                          ORDER BY i.fecha DESC LIMIT 5";
$resultUltimasIncidencias = $conn->query($sqlUltimasIncidencias);

// Obtener estadísticas de categorías de incidencias
$sqlCategorias = "SELECT categoria, COUNT(*) as total FROM Incidencias GROUP BY categoria";
$resultCategorias = $conn->query($sqlCategorias);
$categorias = [];
$totalCategoria = [];
if ($resultCategorias) {
    while ($row = $resultCategorias->fetch_assoc()) {
        $categorias[] = $row['categoria'];
        $totalCategoria[] = $row['total'];
    }
}
?>

<?php include(__DIR__ . '/../includes/header.php'); ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-lg border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 class="m-0"><i class="fas fa-tachometer-alt me-2"></i>Panel de Administración</h2>
                    <div>
                        <a href="admin_settings.php" class="btn btn-outline-secondary"><i class="fas fa-cog me-2"></i>Configuración</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-lg mb-4">
                <div class="card-body text-center">
                    <h5 class="card-title">Usuarios Registrados</h5>
                    <p class="card-text display-4 text-primary"><?php echo $totalUsuarios; ?></p>
                    <a href="admin_users.php" class="btn btn-primary w-100">Gestionar Usuarios</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-lg mb-4">
                <div class="card-body text-center">
                    <h5 class="card-title">Reportes Pendientes</h5>
                    <p class="card-text display-4 text-danger"><?php echo $totalReportes; ?></p>
                    <a href="admin_reportes.php" class="btn btn-danger w-100">Ver Reportes</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-lg mb-4">
                <div class="card-body text-center">
                    <h5 class="card-title">Reportes Resueltos</h5>
                    <p class="card-text display-4 text-success"><?php echo $totalReportesResueltos; ?></p>
                    <a href="admin_resueltos.php" class="btn btn-success w-100">Ver Resueltos</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h5 class="card-title">Últimas Incidencias</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($resultUltimasIncidencias && $resultUltimasIncidencias->num_rows > 0): ?>
                                <?php while ($incidencia = $resultUltimasIncidencias->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $incidencia['ID_Incidencia']; ?></td>
                                        <td><?php echo htmlspecialchars($incidencia['Descripcion']); ?></td>
                                        <td><?php echo htmlspecialchars($incidencia['Estado']); ?></td>
                                        <td><?php echo htmlspecialchars($incidencia['fecha']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No hay incidencias recientes.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h5 class="card-title">Estadísticas de Incidencias por Categoría</h5>
                    <canvas id="categoriaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h5 class="card-title">Estadísticas de Reportes</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Categoría</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categorias as $index => $categoria): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($categoria); ?></td>
                                    <td><?php echo $totalCategoria[$index]; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('categoriaChart').getContext('2d');
    const categoriaChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($categorias); ?>,
            datasets: [{
                label: 'Total de Incidencias por Categoría',
                data: <?php echo json_encode($totalCategoria); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php include(__DIR__ . '/../includes/footer.php'); ?>