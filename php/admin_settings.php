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

<?php include(__DIR__ . '/../includes/header.php'); ?>

<div class="container mt-5">
    <h1 class="text-center">Configuración del Administrador</h1>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="" class="shadow p-4 rounded bg-light">
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="allow_user_registration" name="allow_user_registration" <?php echo isset($allowUserRegistration) && $allowUserRegistration ? 'checked' : ''; ?>>
            <label class="form-check-label" for="allow_user_registration">Permitir Registro de Nuevos Usuarios</label>
            <small class="form-text text-muted">Permite que nuevos usuarios se registren en la aplicación.</small>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="enable_notifications" name="enable_notifications" <?php echo isset($enableNotifications) && $enableNotifications ? 'checked' : ''; ?>>
            <label class="form-check-label" for="enable_notifications">Habilitar Notificaciones</label>
            <small class="form-text text-muted">Recibe notificaciones sobre eventos importantes.</small>
        </div>
        <div class="mb-3">
            <label for="maintenance_mode" class="form-label">Modo de Mantenimiento</label>
            <select class="form-select" id="maintenance_mode" name="maintenance_mode" required>
                <option value="0" <?php echo (isset($maintenanceMode) && $maintenanceMode == 0) ? 'selected' : ''; ?>>Desactivado</option>
                <option value="1" <?php echo (isset($maintenanceMode) && $maintenanceMode == 1) ? 'selected' : ''; ?>>Activado</option>
            </select>
            <small class="form-text text-muted">Activa este modo para realizar mantenimiento en la aplicación.</small>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="enable_two_factor_auth" name="enable_two_factor_auth" <?php echo isset($enableTwoFactorAuth) && $enableTwoFactorAuth ? 'checked' : ''; ?>>
            <label class="form-check-label" for="enable_two_factor_auth">Habilitar Verificación en Dos Pasos (2FA)</label>
            <small class="form-text text-muted">Aumenta la seguridad de las cuentas de usuario.</small>
        </div>
        <div class="mb-3">
            <label for="admin_notes" class="form-label">Notas del Administrador</label>
            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"><?php echo isset($adminNotes) ? htmlspecialchars($adminNotes) : ''; ?></textarea>
            <small class="form-text text-muted">Notas internas para el administrador.</small>
        </div>
        <button type="submit" class="btn btn-primary ">Guardar Cambios</button>
        <a href="admin_dashboard.php" class="btn btn-outline-secondary ms-2">Volver</a>
    </form>
</div>

<style>
    body {
        background-color: #f8f9fa;
    }
    h1 {
        color: #343a40;
        margin-bottom: 30px;
    }
    .form-check-label {
        font-weight: bold;
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
    }
    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: white;
    }
    .alert {
        margin-top: 20px;
    }
</style>

<?php include(__DIR__ . '/../includes/footer.php'); ?>