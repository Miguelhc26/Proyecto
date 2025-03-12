<?php
$inPhpFolder = strpos($_SERVER['PHP_SELF'], '/php/') !== false;
$basePath = $inPhpFolder ? '../' : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoveSync - Transporte Inteligente</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #e9ecef;
            color: #343a40;
        }
        .nav-link {
            transition: color 0.3s, transform 0.3s;
        }
        .nav-link:hover {
            color: #007bff;
            transform: scale(1.1);
        }
        .navbar {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="<?php echo $basePath; ?>index.php">
                    <img src="<?php echo $basePath; ?>img/logo.png" alt="MoveSync Logo" height="30" class="d-inline-block align-text-top">
                    MoveSync
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $basePath; ?>index.php">
                                <i class="fas fa-home"></i> Inicio
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $basePath; ?>php/routes.php">
                                <i class="fas fa-route"></i> Rutas
                            </a>
                        </li>
                        <?php if (isset($_SESSION['usuario'])): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $basePath; ?>php/report.php">
                                    <i class="fas fa-exclamation-triangle"></i> Reportar Incidencia
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $basePath; ?>dashboard.php">
                                    <i class="fas fa-user"></i> Mi Cuenta
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $basePath; ?>php/logout.php">Cerrar Sesión</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $basePath; ?>php/altaLogin.php">Iniciar Sesión</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $basePath; ?>php/register.php">Registrarse</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main>