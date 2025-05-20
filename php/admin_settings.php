<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin'])) {
    header("Location: ../php/altaLogin.php");
    exit();
}

// Inicializar variables
$message = '';
$error_message = '';

// Aquí puedes agregar lógica para manejar la actualización de configuraciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener configuraciones del formulario
    $allowUserRegistration = isset($_POST['allow_user_registration']) ? 1 : 0;
    $enableNotifications = isset($_POST['enable_notifications']) ? 1 : 0;
    $maintenanceMode = isset($_POST['maintenance_mode']) ? (int)$_POST['maintenance_mode'] : 0;
    $enableTwoFactorAuth = isset($_POST['enable_two_factor_auth']) ? 1 : 0;
    $adminNotes = $_POST['admin_notes'];

    // Aquí deberías agregar la lógica para guardar estos cambios en tu archivo de configuración
    // Por ejemplo, podrías escribir en un archivo de configuración PHP o en una base de datos

    // Mensaje de éxito
    $message = "Configuración actualizada correctamente.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración del Administrador | Panel de Control</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --secondary-color: #475569;
            --secondary-hover: #334155;
            --success-color: #10b981;
            --error-color: #ef4444;
            --text-color: #1e293b;
            --light-text: #64748b;
            --light-bg: #f1f5f9;
            --border-color: #cbd5e1;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s ease;
            --gradient-bg: linear-gradient(135deg, #4f46e5 0%, #9333ea 100%);
            --card-bg: #ffffff;
            --header-bg: linear-gradient(90deg, #4f46e5 0%, #7c3aed 100%);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', 'Segoe UI', 'Roboto', sans-serif;
            background-color: var(--light-bg);
            color: var(--text-color);
            line-height: 1.6;
            padding: 0;
            margin: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%234f46e5' fill-opacity='0.05'%3E%3Cpath opacity='.5' d='M96 95h4v1h-4v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9zm-1 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
            padding: 2rem 1rem;
            border-radius: 12px;
            background: var(--header-bg);
            color: white;
            box-shadow: var(--card-shadow);
        }

        .header h1 {
            font-size: 2.2rem;
            color: white;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
        }

        .card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: var(--transition);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .card-header {
            background-color: #f8fafc;
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            background: linear-gradient(to right, #f8fafc, #f1f5f9);
        }

        .card-header i {
            margin-right: 12px;
            color: var(--primary-color);
            font-size: 1.2rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
            color: var(--text-color);
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-color);
        }

        .form-control, .form-select {
            width: 100%;
            padding: 0.85rem;
            font-size: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background-color: #fff;
            color: var(--text-color);
            transition: var(--transition);
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .form-check {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.25rem;
            background: #f8fafc;
            padding: 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            border: 1px solid var(--border-color);
        }

        .form-check:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .form-check-input {
            margin-right: 0.75rem;
            margin-top: 0.25rem;
            width: 18px;
            height: 18px;
        }

        .form-check-label {
            font-weight: 600;
            font-size: 1.05rem;
            color: var(--text-color);
        }

        .form-text {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.875rem;
            color: var(--light-text);
        }

        .btn {
            display: inline-block;
            font-weight: 500;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            padding: 0.75rem 1.25rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: 8px;
            transition: var(--transition);
            border: none;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            margin-right: 0.5rem;
        }

        .btn-primary {
            color: white;
            background: var(--gradient-bg);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #4338ca 0%, #8b5cf6 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .btn-outline-secondary {
            color: var(--secondary-color);
            background-color: transparent;
            border: 1px solid var(--secondary-color);
        }

        .btn-outline-secondary:hover {
            background-color: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
        }

        .actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .alert {
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 10px;
            font-weight: 500;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .alert i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border-left: 4px solid var(--success-color);
            color: var(--success-color);
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            border-left: 4px solid var(--error-color);
            color: var(--error-color);
        }

        .footer {
            text-align: center;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .back-link {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            color: white;
            background: var(--gradient-bg);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: var(--transition);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .back-link:hover {
            background: linear-gradient(135deg, #4338ca 0%, #8b5cf6 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .quick-tools {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            grid-gap: 1.5rem;
        }

        .quick-tool-item {
            background: #f8fafc;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .quick-tool-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-color: #cbd5e1;
        }

        .quick-tool-item i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .quick-tool-item h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }

        .quick-tool-item p {
            color: var(--light-text);
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }

        .quick-tool-item .btn {
            width: 100%;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quick-tool-item .btn i {
            font-size: 0.9rem;
            margin: 0 0 0 5px;
            color: inherit;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Configuración del Administrador</h1>
            <p>Gestiona las configuraciones generales del sistema</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <i class="fas fa-cogs"></i>
                <h2 class="card-title">Configuraciones Generales</h2>
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="allow_user_registration" name="allow_user_registration" <?php echo isset($allowUserRegistration) && $allowUserRegistration ? 'checked' : ''; ?>>
                            <div>
                                <label class="form-check-label" for="allow_user_registration">Permitir Registro de Nuevos Usuarios</label>
                                <small class="form-text">Permite que nuevos usuarios se registren en la aplicación.</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="enable_notifications" name="enable_notifications" <?php echo isset($enableNotifications) && $enableNotifications ? 'checked' : ''; ?>>
                            <div>
                                <label class="form-check-label" for="enable_notifications">Habilitar Notificaciones</label>
                                <small class="form-text">Recibe notificaciones sobre eventos importantes del sistema.</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="maintenance_mode" class="form-label">Modo de Mantenimiento</label>
                        <select class="form-select" id="maintenance_mode" name="maintenance_mode" required>
                            <option value="0" <?php echo (isset($maintenanceMode) && $maintenanceMode == 0) ? 'selected' : ''; ?>>Desactivado</option>
                            <option value="1" <?php echo (isset($maintenanceMode) && $maintenanceMode == 1) ? 'selected' : ''; ?>>Activado</option>
                        </select>
                        <small class="form-text">Activa este modo para realizar mantenimiento en la aplicación. Los usuarios no podrán acceder durante este tiempo.</small>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="enable_two_factor_auth" name="enable_two_factor_auth" <?php echo isset($enableTwoFactorAuth) && $enableTwoFactorAuth ? 'checked' : ''; ?>>
                            <div>
                                <label class="form-check-label" for="enable_two_factor_auth">Habilitar Verificación en Dos Pasos (2FA)</label>
                                <small class="form-text">Aumenta la seguridad de las cuentas de usuario con verificación en dos pasos.</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="admin_notes" class="form-label">Notas del Administrador</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"><?php echo isset($adminNotes) ? htmlspecialchars($adminNotes) : ''; ?></textarea>
                        <small class="form-text">Notas internas visibles solo para los administradores del sistema.</small>
                    </div>
                    
                    <div class="actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="footer">
            <a href="admin_dashboard.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Volver al Panel de Administración
            </a>
        </div>
    </div>

    <script>
        // Script para mostrar una confirmación antes de salir si hay cambios sin guardar
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            let formChanged = false;
            
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    formChanged = true;
                });
            });
            
            window.addEventListener('beforeunload', function(e) {
                if (formChanged) {
                    e.preventDefault();
                    e.returnValue = '¿Estás seguro de que quieres salir? Los cambios no guardados se perderán.';
                }
            });
            
            form.addEventListener('submit', function() {
                formChanged = false;
            });
        });
    </script>
</body>
</html>