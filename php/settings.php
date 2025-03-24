<?php
session_start();
include_once(__DIR__ . '/../config/db_config.php');

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: altaLogin.php");
    exit();
}

$id_usuario = $_SESSION['usuario'];

// Inicializar array de usuario con valores predeterminados
$usuario = [
    'nombre' => '',
    'email' => '',
    'telefono' => '',
    'password' => ''
];

// Obtener información del usuario
try {
    $sql = "SELECT * FROM Usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }
    
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $usuario = array_merge($usuario, $result->fetch_assoc());
    } else {
        throw new Exception("No se encontró información para este usuario.");
    }
} catch (Exception $e) {
    $error_message = $e->getMessage();
}

// Inicializar valores para las preferencias
$notificaciones_email = 0;
$notificaciones_app = 0;

// Intentar obtener preferencias del usuario
try {
    $sql = "SELECT notificaciones_email, notificaciones_app FROM Preferencias_Usuario WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $preferencias = $result->fetch_assoc();
            $notificaciones_email = $preferencias['notificaciones_email'];
            $notificaciones_app = $preferencias['notificaciones_app'];
        }
    }
} catch (Exception $e) {
    // No hacemos nada, usaremos los valores predeterminados
}

// Declarar variables para mensajes
$password_error = "";
$password_success = "";
$profile_error = "";
$profile_success = "";
$notification_success = "";
$notification_error = "";

// Procesar cambio de contraseña
if (isset($_POST['cambiar_password'])) {
    $password_actual = $_POST['password_actual'];
    $password_nueva = $_POST['password_nueva'];
    $password_confirmar = $_POST['password_confirmar'];
    
    // Verificar que la contraseña actual es correcta
    if (isset($usuario['password']) && password_verify($password_actual, $usuario['password'])) {
        // Verificar que las nuevas contraseñas coinciden
        if ($password_nueva === $password_confirmar) {
            // Actualizar la contraseña
            $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
            $sql = "UPDATE Usuarios SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("si", $password_hash, $id_usuario);
                
                if ($stmt->execute()) {
                    $password_success = "Contraseña actualizada correctamente.";
                } else {
                    $password_error = "Error al actualizar la contraseña: " . $conn->error;
                }
            } else {
                $password_error = "Error en la consulta: " . $conn->error;
            }
        } else {
            $password_error = "Las nuevas contraseñas no coinciden.";
        }
    } else {
        $password_error = "La contraseña actual es incorrecta.";
    }
}

// Procesar actualización de perfil
if (isset($_POST['actualizar_perfil'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];

    $sql = "UPDATE Usuarios SET nombre = ?, email = ?, telefono = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("sssi", $nombre, $email, $telefono, $id_usuario);
        
        if ($stmt->execute()) {
            $profile_success = "Perfil actualizado correctamente.";
            // Actualizar los datos en la sesión
            $usuario['nombre'] = $nombre;
            $usuario['email'] = $email;
            $usuario['telefono'] = $telefono;
        } else {
            $profile_error = "Error al actualizar el perfil: " . $conn->error;
        }
    } else {
        $profile_error = "Error en la consulta: " . $conn->error;
    }
}

// Procesar preferencias de notificaciones
if (isset($_POST['actualizar_notificaciones'])) {
    $notificaciones_email = isset($_POST['notificaciones_email']) ? 1 : 0;
    $notificaciones_app = isset($_POST['notificaciones_app']) ? 1 : 0;
    
    $sql = "UPDATE Preferencias_Usuario SET notificaciones_email = ?, notificaciones_app = ? WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("iii", $notificaciones_email, $notificaciones_app, $id_usuario);
        
        if ($stmt->execute()) {
            $notification_success = "Preferencias de notificación actualizadas correctamente.";
        } else {
            $notification_error = "Error al actualizar las preferencias: " . $conn->error;
        }
    } else {
        // Si la sentencia falla, es posible que necesitemos insertar en lugar de actualizar
        $sql = "INSERT INTO Preferencias_Usuario (id_usuario, notificaciones_email, notificaciones_app) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("iii", $id_usuario, $notificaciones_email, $notificaciones_app);
            if ($stmt->execute()) {
                $notification_success = "Preferencias de notificación guardadas correctamente.";
            } else {
                $notification_error = "Error al guardar las preferencias: " . $conn->error;
            }
        } else {
            $notification_error = "Error en la consulta: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Usuario</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        h1, h2 {
            color: #343a40;
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            background: #ffffff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"] {
            width: calc(100% - 20px);
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ced4da;
            border-radius: 4px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        p {
            color: red;
            text-align: center;
        }
        .success {
            color: green;
            text-align: center }
        .notification-label {
            display: block;
            margin: 8px 0;
            font-weight: bold;
        }
        .back-button {
            display: block;
            text-align: center;
            margin-top: 20px;
            background-color: #6c757d;
            color: white;
            padding: 8px;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .back-button:hover {
            background-color: #5a6268;
        }
        .form-section {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 20px;
        }
        .form-title {
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        label {
            margin: 5px 0;
            display: block;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Configuración de Usuario</h1>
    
    <div class="form-section">
        <form method="post">
            <div class="form-title"><i class="fas fa-lock"></i> Cambiar Contraseña</div>
            <label for="password_actual">Contraseña Actual</label>
            <input type="password" name="password_actual" id="password_actual" required>
            <label for="password_nueva">Nueva Contraseña</label>
            <input type="password" name="password_nueva" id="password_nueva" required>
            <label for="password_confirmar">Confirmar Nueva Contraseña</label>
            <input type="password" name="password_confirmar" id="password_confirmar" required>
            <button type="submit" name="cambiar_password">Cambiar Contraseña</button>
            <p class="error"><?php echo $password_error; ?></p>
            <p class="success"><?php echo $password_success; ?></p>
        </form>
    </div>

    <div class="form-section">
        <form method="post">
            <div class="form-title"><i class="fas fa-user"></i> Actualizar Perfil</div>
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
            <label for="telefono">Teléfono</label>
            <input type="tel" name="telefono" id="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>" required>
            <button type="submit" name="actualizar_perfil">Actualizar Perfil</button>
            <p class="error"><?php echo $profile_error; ?></p>
            <p class="success"><?php echo $profile_success; ?></p>
        </form>
    </div>

    <div class="form-section">
        <form method="post">
            <div class="form-title"><i class="fas fa-bell"></i> Preferencias de Notificación</div>
            <label class="notification-label">
                <input type="checkbox" name="notificaciones_email" <?php echo $notificaciones_email ? 'checked' : ''; ?>> Recibir notificaciones por email
            </label>
            <label class="notification-label">
                <input type="checkbox" name="notificaciones_app" <?php echo $notificaciones_app ? 'checked' : ''; ?>> Recibir notificaciones en la app
            </label>
            <button type="submit" name="actualizar_notificaciones">Actualizar Preferencias</button>
            <p class="success"><?php echo $notification_success; ?></p>
            <p class="error"><?php echo $notification_error; ?></p>
        </form>
    </div>

    <a href="../dashboard.php" class="back-button">Volver</a>
</body>
</html>