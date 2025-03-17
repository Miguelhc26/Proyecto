<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: altaLogin.php");
    exit();
}

// Definición de rutas predeterminadas
$rutas = [
    [
        'id' => 1,
        'nombre' => 'Ruta Express Centro',
        'origen' => 'Estación Central',
        'destino' => 'Plaza Mayor',
        'paradas' => ['Estación Central', 'Avenida Principal', 'Calle Comercio', 'Plaza Mayor'],
        'tiempo_total' => '25 minutos',
        'tiempos_parada' => ['0 min', '8 min', '15 min', '25 min'],
        'frecuencia' => 'Cada 10 minutos',
        'color' => '#4CAF50',
        'icono' => 'bus'
    ],
    [
        'id' => 2,
        'nombre' => 'Ruta Circular Norte',
        'origen' => 'Terminal Norte',
        'destino' => 'Terminal Sur',
        'paradas' => ['Terminal Norte', 'Hospital General', 'Parque Industrial', 'Centro Comercial', 'Universidad', 'Terminal Norte'],
        'tiempo_total' => '45 minutos',
        'tiempos_parada' => ['0 min', '10 min', '20 min', '30 min', '35 min', '45 min'],
        'frecuencia' => 'Cada 15 minutos',
        'color' => '#2196F3',
        'icono' => 'bus-alt'
    ],
    [
        'id' => 3,
        'nombre' => 'Línea Rápida Sur',
        'origen' => 'Estación Sur',
        'destino' => 'Zona Residencial',
        'paradas' => ['Estación Sur', 'Avenida del Sur', 'Centro Deportivo', 'Parque Tecnológico', 'Zona Residencial'],
        'tiempo_total' => '35 minutos',
        'tiempos_parada' => ['0 min', '8 min', '18 min', '25 min', '35 min'],
        'frecuencia' => 'Cada 12 minutos',
        'color' => '#FF5722',
        'icono' => 'shuttle-van'
    ],
    [
        'id' => 4,
        'nombre' => 'Conexión Este-Oeste',
        'origen' => 'Terminal Este',
        'destino' => 'Terminal Oeste',
        'paradas' => ['Terminal Este', 'Barrio Antiguo', 'Centro Cultural', 'Zona Financiera', 'Terminal Oeste'],
        'tiempo_total' => '40 minutos',
        'tiempos_parada' => ['0 min', '10 min', '20 min', '30 min', '40 min'],
        'frecuencia' => 'Cada 20 minutos',
        'color' => '#9C27B0',
        'icono' => 'bus'
    ],
    [
        'id' => 5,
        'nombre' => 'Ruta Turística',
        'origen' => 'Estación Turística',
        'destino' => 'Mirador',
        'paradas' => ['Estación Turística', 'Museo de Arte', 'Plaza de la Ciudad', 'Mirador'],
        'tiempo_total' => '50 minutos',
        'tiempos_parada' => ['0 min', '15 min', '30 min', '50 min'],
        'frecuencia' => 'Cada 30 minutos',
        'color' => '#FF9800 ',
        'icono' => 'camera'
    ],
    [
        'id' => 6,
        'nombre' => 'Ruta Universitaria',
        'origen' => 'Campus Universitario',
        'destino' => 'Centro Estudiantil',
        'paradas' => ['Campus Universitario', 'Biblioteca', 'Cafetería', 'Centro Estudiantil'],
        'tiempo_total' => '20 minutos',
        'tiempos_parada' => ['0 min', '5 min', '10 min', '20 min'],
        'frecuencia' => 'Cada 5 minutos',
        'color' => '#3F51B5',
        'icono' => 'graduation-cap'
    ],
    [
        'id' => 7,
        'nombre' => 'Ruta de la Salud',
        'origen' => 'Hospital General',
        'destino' => 'Clínica Especializada',
        'paradas' => ['Hospital General', 'Centro de Salud', 'Clínica Especializada'],
        'tiempo_total' => '15 minutos',
        'tiempos_parada' => ['0 min', '7 min', '15 min'],
        'frecuencia' => 'Cada 8 minutos',
        'color' => '#8BC34A',
        'icono' => 'heartbeat'
    ],
    [
        'id' => 8,
        'nombre' => 'Ruta de Compras',
        'origen' => 'Plaza de Compras',
        'destino' => 'Centro Comercial',
        'paradas' => ['Plaza de Compras', 'Supermercado', 'Centro Comercial'],
        'tiempo_total' => '30 minutos',
        'tiempos_parada' => ['0 min', '10 min', '30 min'],
        'frecuencia' => 'Cada 15 minutos',
        'color' => '#FFEB3B',
        'icono' => 'shopping-cart'
    ],
    [
        'id' => 9,
        'nombre' => 'Ruta Nocturna',
        'origen' => 'Estación Nocturna',
        'destino' => 'Zona de Entretenimiento',
        'paradas' => ['Estación Nocturna', 'Calle de los Bares', 'Zona de Entretenimiento'],
        'tiempo_total' => '25 minutos',
        'tiempos_parada' => ['0 min', '10 min', '25 min'],
        'frecuencia' => 'Cada 30 minutos',
        'color' => '#F44336',
        'icono' => 'moon'
    ],
];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Rutas - TransporteApp</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            color: #343a40;
        }
        .card {
            transition: transform 0.2s;
            border-radius: 10px;
            height: 100%;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        .card-title {
            font-weight: bold;
            text-align: center;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .filter-input {
            margin-bottom: 20px;
        }
        .row {
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">Rutas Disponibles</h2>
        <input type="text" id="filter" class="form-control filter-input" placeholder="Filtrar rutas...">
        <div class="row" id="route-list">
            <?php foreach ($rutas as $ruta): ?>
                <div class="col-md-4 mb-4 route-item" data-name="<?php echo htmlspecialchars($ruta['nombre']); ?>">
                    <div class="card shadow-sm" style="border-left: 5px solid <?php echo htmlspecialchars($ruta['color']); ?>;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($ruta['nombre']); ?></h5>
                            <p class="card-text"><strong>Origen:</strong> <?php echo htmlspecialchars($ruta['origen']); ?></p>
                            <p class="card-text"><strong>Destino:</strong> <?php echo htmlspecialchars($ruta['destino']); ?></p>
                            <h6>Paradas:</h6>
                            <ul>
                                <?php foreach ($ruta['paradas'] as $index => $parada): ?>
                                    <li><?php echo htmlspecialchars($parada); ?> - Tiempo estimado: <?php echo htmlspecialchars($ruta['tiempos_parada'][$index]); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <p class="card-text"><strong>Tiempo total:</strong> <?php echo htmlspecialchars($ruta['tiempo_total']); ?></p>
                            <p class="card-text"><strong>Frecuencia:</strong> <?php echo htmlspecialchars($ruta['frecuencia']); ?></p>
                            <a href="javascript:void(0);" class="btn btn-primary" onclick="alert('Ruta seleccionada: <?php echo htmlspecialchars($ruta['nombre']); ?>');">Seleccionar Ruta</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <a href="../dashboard.php" class="btn btn-secondary">Volver</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('filter').addEventListener('input', function() {
            const filterValue = this.value.toLowerCase();
            const routeItems = document.querySelectorAll('.route-item');
            routeItems.forEach(item => {
                const routeName = item.getAttribute('data-name').toLowerCase();
                item.style.display = routeName.includes(filterValue) ? '' : 'none';
            });
        });
    </script>
</body>
</html>