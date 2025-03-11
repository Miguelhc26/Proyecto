<?php
session_start();

include(__DIR__ . '/../config/db_config.php'); 

if (!isset($_SESSION['usuario'])) {
    header("Location: altaLogin.php");
    exit();
}

$sqlCheck = "SELECT COUNT(*) AS total FROM Rutas";
$resultCheck = $conn->query($sqlCheck);
$rowCheck = $resultCheck->fetch_assoc();

if ($rowCheck['total'] == 0) {
    $sqlInsert = "INSERT INTO Rutas (Origen, Destino, Horario, TipoTransporte) VALUES
        ('Centro', 'Universidad', '07:00 - 20:00', 'Autobús'),
        ('Estación Norte', 'Plaza Mayor', '06:30 - 22:30', 'Autobús'),
        ('Aeropuerto', 'Terminal Bus', '05:00 - 23:00', 'Autobús'),
        ('Playa', 'Centro Histórico', '09:00 - 18:00', 'Autobús'),
        ('Hospital Central', 'Residencial Oeste', '07:00 - 19:00', 'Autobús'),
        ('Estación Sur', 'Parque Central', '06:00 - 21:00', 'Autobús'),
        ('Zona Industrial', 'Centro Financiero', '05:30 - 20:30', 'Autobús'),
        ('Universidad', 'Biblioteca Nacional', '08:00 - 22:00', 'Autobús'),
        ('Barrio Norte', 'Estadio Nacional', '10:00 - 23:00', 'Autobús'),
        ('Mercado Central', 'Puerto', '06:00 - 18:00', 'Autobús')";
    $conn->query($sqlInsert);
}

$sql = "SELECT * FROM Rutas";
$result = $conn->query($sql);
?>

<?php include(__DIR__ . '/../includes/header.php'); ?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Rutas Disponibles</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Origen</th>
                <th>Destino</th>
                <th>Horario</th>
                <th>Tipo de Transporte</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['ID_Ruta']); ?></td>
                        <td><?php echo htmlspecialchars($row['Origen']); ?></td>
                        <td><?php echo htmlspecialchars($row['Destino']); ?></td>
                        <td><?php echo htmlspecialchars($row['Horario']); ?></td>
                        <td><?php echo htmlspecialchars($row['TipoTransporte']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No hay rutas disponibles.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include(__DIR__ . '/../includes/footer.php'); ?>
