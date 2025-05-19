<?php
/**
 * Página de configuración de usuario
 * 
 * Permite a los usuarios gestionar su perfil, contraseña y preferencias de notificación
 */
session_start();
require_once(__DIR__ . '/../config/db_config.php');
// No requerimos el archivo functions.php ya que implementaremos las funciones directamente aquí

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: altaLogin.php");
    exit();
}

$id_usuario = $_SESSION['usuario'];
$messages = [
    'password' => ['error' => '', 'success' => ''],
    'profile' => ['error' => '', 'success' => ''],
    'notification' => ['error' => '', 'success' => '']
];

// Inicializar valores predeterminados para prevenir errores
$usuario = [
    'nombre' => '',
    'email' => '',
    'telefono' => '',
    'password' => ''
];

$preferencias = [
    'notificaciones_email' => 0,
    'notificaciones_app' => 0
];

// Cargar datos del usuario con manejo adecuado de errores
try {
    $usuario = getUserData($conn, $id_usuario);
    $preferencias = getUserPreferences($conn, $id_usuario);
} catch (Exception $e) {
    $messages['profile']['error'] = $e->getMessage();
}

// Procesar formularios según la acción solicitada
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'password':
                $messages['password'] = processPasswordChange($conn, $id_usuario, $_POST);
                break;
            case 'profile':
                $result = processProfileUpdate($conn, $id_usuario, $_POST);
                $messages['profile'] = $result['messages'];
                if (!empty($result['usuario'])) {
                    $usuario = $result['usuario'];
                }
                break;
            case 'notifications':
                $messages['notification'] = processNotificationPreferences($conn, $id_usuario, $_POST);
                $preferencias = getUserPreferences($conn, $id_usuario);
                break;
        }
    }
}

/**
 * Obtiene los datos del usuario
 * 
 * @param mysqli $conn Conexión a la base de datos
 * @param int $id_usuario ID del usuario
 * @return array Datos del usuario
 * @throws Exception Si hay algún error en la consulta
 */
function getUserData($conn, $id_usuario) {
    $usuario = [
        'nombre' => '',
        'email' => '',
        'telefono' => '',
        'password' => ''
    ];
    
    // Verificamos primero cuál es el nombre de la columna de ID en la tabla Usuarios
    $table_info_query = "SHOW COLUMNS FROM Usuarios";
    $table_info_result = $conn->query($table_info_query);
    
    $id_column_name = 'id'; // Valor predeterminado
    
    if ($table_info_result) {
        while ($column = $table_info_result->fetch_assoc()) {
            // Buscar columna que parece ser el ID principal (usualmente 'id', 'ID', 'id_usuario', etc.)
            if ($column['Key'] == 'PRI' || strtolower($column['Field']) == 'id' || 
                strtolower($column['Field']) == 'id_usuario' || 
                strtolower($column['Field']) == 'idusuario') {
                $id_column_name = $column['Field'];
                break;
            }
        }
    }
    
    $sql = "SELECT * FROM Usuarios WHERE $id_column_name = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Error de preparación: " . $conn->error);
    }
    
    $stmt->bind_param("i", $id_usuario);
    
    if (!$stmt->execute()) {
        throw new Exception("Error de ejecución: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $usuario = array_merge($usuario, $result->fetch_assoc());
    } else {
        throw new Exception("No se encontró información para este usuario.");
    }
    
    return $usuario;
}

/**
 * Obtiene las preferencias del usuario
 * 
 * @param mysqli $conn Conexión a la base de datos
 * @param int $id_usuario ID del usuario
 * @return array Preferencias del usuario
 */
function getUserPreferences($conn, $id_usuario) {
    $preferencias = [
        'notificaciones_email' => 0,
        'notificaciones_app' => 0
    ];
    
    // Verificamos primero cuál es el nombre de la columna de ID en la tabla Preferencias_Usuario
    $table_info_query = "SHOW COLUMNS FROM Preferencias_Usuario";
    $table_info_result = $conn->query($table_info_query);
    
    $id_usuario_column_name = 'id_usuario'; // Valor predeterminado
    
    if ($table_info_result) {
        while ($column = $table_info_result->fetch_assoc()) {
            // Buscar columna que parece ser el ID de usuario (usualmente 'id_usuario', 'usuario_id', etc.)
            if (strtolower($column['Field']) == 'id_usuario' || 
                strtolower($column['Field']) == 'usuario_id' || 
                strtolower($column['Field']) == 'idusuario') {
                $id_usuario_column_name = $column['Field'];
                break;
            }
        }
    }
    
    // Verificar si la tabla existe
    $table_check = $conn->query("SHOW TABLES LIKE 'Preferencias_Usuario'");
    if ($table_check->num_rows == 0) {
        // La tabla no existe, creamos la tabla
        $create_table = "CREATE TABLE IF NOT EXISTS Preferencias_Usuario (
            id INT AUTO_INCREMENT PRIMARY KEY,
            $id_usuario_column_name INT NOT NULL,
            notificaciones_email TINYINT(1) DEFAULT 0,
            notificaciones_app TINYINT(1) DEFAULT 0,
            UNIQUE ($id_usuario_column_name)
        )";
        $conn->query($create_table);
        
        // Insertar preferencias por defecto
        $insert_default = "INSERT INTO Preferencias_Usuario ($id_usuario_column_name, notificaciones_email, notificaciones_app) 
                        VALUES (?, 0, 0)";
        $stmt = $conn->prepare($insert_default);
        if ($stmt) {
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
        }
        
        return $preferencias;
    }
    
    // La tabla existe, intentar obtener las preferencias
    $sql = "SELECT notificaciones_email, notificaciones_app FROM Preferencias_Usuario WHERE $id_usuario_column_name = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $id_usuario);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $preferencias = $result->fetch_assoc();
            } else {
                // No hay preferencias para este usuario, insertamos valores predeterminados
                $insert_default = "INSERT INTO Preferencias_Usuario ($id_usuario_column_name, notificaciones_email, notificaciones_app) 
                                VALUES (?, 0, 0)";
                $stmt_insert = $conn->prepare($insert_default);
                if ($stmt_insert) {
                    $stmt_insert->bind_param("i", $id_usuario);
                    $stmt_insert->execute();
                }
            }
        }
    }
    
    return $preferencias;
}

/**
 * Procesa el cambio de contraseña
 * 
 * @param mysqli $conn Conexión a la base de datos
 * @param int $id_usuario ID del usuario
 * @param array $data Datos del formulario
 * @return array Mensajes de éxito o error
 */
function processPasswordChange($conn, $id_usuario, $data) {
    $messages = ['error' => '', 'success' => ''];
    
    // Verificamos primero cuál es el nombre de la columna de ID en la tabla Usuarios
    $table_info_query = "SHOW COLUMNS FROM Usuarios";
    $table_info_result = $conn->query($table_info_query);
    
    $id_column_name = 'id'; // Valor predeterminado
    
    if ($table_info_result) {
        while ($column = $table_info_result->fetch_assoc()) {
            // Buscar columna que parece ser el ID principal
            if ($column['Key'] == 'PRI' || strtolower($column['Field']) == 'id' || 
                strtolower($column['Field']) == 'id_usuario' || 
                strtolower($column['Field']) == 'idusuario') {
                $id_column_name = $column['Field'];
                break;
            }
        }
    }
    
    // Validar campos requeridos
    if (empty($data['password_actual']) || empty($data['password_nueva']) || empty($data['password_confirmar'])) {
        $messages['error'] = "Todos los campos son requeridos";
        return $messages;
    }
    
    // Obtener la contraseña actual del usuario
    $sql = "SELECT password FROM Usuarios WHERE $id_column_name = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        $messages['error'] = "Error en la consulta: " . $conn->error;
        return $messages;
    }
    
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $messages['error'] = "Usuario no encontrado";
        return $messages;
    }
    
    $usuario = $result->fetch_assoc();
    
    // Verificar la contraseña actual
    if (!password_verify($data['password_actual'], $usuario['password'])) {
        $messages['error'] = "La contraseña actual es incorrecta";
        return $messages;
    }
    
    // Verificar que las nuevas contraseñas coinciden
    if ($data['password_nueva'] !== $data['password_confirmar']) {
        $messages['error'] = "Las nuevas contraseñas no coinciden";
        return $messages;
    }
    
    // Validar requisitos de seguridad de la contraseña
    if (strlen($data['password_nueva']) < 8) {
        $messages['error'] = "La nueva contraseña debe tener al menos 8 caracteres";
        return $messages;
    }
    
    // Actualizar la contraseña
    $password_hash = password_hash($data['password_nueva'], PASSWORD_DEFAULT);
    $sql = "UPDATE Usuarios SET password = ? WHERE $id_column_name = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        $messages['error'] = "Error en la consulta: " . $conn->error;
        return $messages;
    }
    
    $stmt->bind_param("si", $password_hash, $id_usuario);
    
    if ($stmt->execute()) {
        $messages['success'] = "Contraseña actualizada correctamente";
    } else {
        $messages['error'] = "Error al actualizar la contraseña: " . $stmt->error;
    }
    
    return $messages;
}

/**
 * Procesa la actualización del perfil
 * 
 * @param mysqli $conn Conexión a la base de datos
 * @param int $id_usuario ID del usuario
 * @param array $data Datos del formulario
 * @return array Resultado de la operación
 */
function processProfileUpdate($conn, $id_usuario, $data) {
    $result = [
        'messages' => ['error' => '', 'success' => ''],
        'usuario' => null
    ];
    
    // Verificamos primero cuál es el nombre de la columna de ID en la tabla Usuarios
    $table_info_query = "SHOW COLUMNS FROM Usuarios";
    $table_info_result = $conn->query($table_info_query);
    
    $id_column_name = 'id'; // Valor predeterminado
    
    if ($table_info_result) {
        while ($column = $table_info_result->fetch_assoc()) {
            // Buscar columna que parece ser el ID principal
            if ($column['Key'] == 'PRI' || strtolower($column['Field']) == 'id' || 
                strtolower($column['Field']) == 'id_usuario' || 
                strtolower($column['Field']) == 'idusuario') {
                $id_column_name = $column['Field'];
                break;
            }
        }
    }
    
    // Validar campos requeridos
    if (empty($data['nombre']) || empty($data['email']) || empty($data['telefono'])) {
        $result['messages']['error'] = "Todos los campos son requeridos";
        return $result;
    }
    
    $nombre = trim($data['nombre']);
    $email = filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL);
    $telefono = trim($data['telefono']);
    
    // Validar email
    if (!$email) {
        $result['messages']['error'] = "El formato del email no es válido";
        return $result;
    }
    
    // Comprobar si el email ya está en uso por otro usuario
    $sql = "SELECT $id_column_name FROM Usuarios WHERE email = ? AND $id_column_name != ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("si", $email, $id_usuario);
        $stmt->execute();
        $existingEmail = $stmt->get_result();
        
        if ($existingEmail->num_rows > 0) {
            $result['messages']['error'] = "El email ya está en uso por otro usuario";
            return $result;
        }
    }
    
    // Actualizar perfil
    $sql = "UPDATE Usuarios SET nombre = ?, email = ?, telefono = ? WHERE $id_column_name = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        $result['messages']['error'] = "Error en la consulta: " . $conn->error;
        return $result;
    }
    
    $stmt->bind_param("sssi", $nombre, $email, $telefono, $id_usuario);
    
    if ($stmt->execute()) {
        $result['messages']['success'] = "Perfil actualizado correctamente";
        // Devolver datos actualizados
        $result['usuario'] = [
            'nombre' => $nombre,
            'email' => $email,
            'telefono' => $telefono
        ];
    } else {
        $result['messages']['error'] = "Error al actualizar el perfil: " . $stmt->error;
    }
    
    return $result;
}

/**
 * Procesa las preferencias de notificación
 * 
 * @param mysqli $conn Conexión a la base de datos
 * @param int $id_usuario ID del usuario
 * @param array $data Datos del formulario
 * @return array Mensajes de éxito o error
 */
function processNotificationPreferences($conn, $id_usuario, $data) {
    $messages = ['error' => '', 'success' => ''];
    
    // Verificar si la tabla existe
    $table_check = $conn->query("SHOW TABLES LIKE 'Preferencias_Usuario'");
    if ($table_check->num_rows == 0) {
        // La tabla no existe, creamos la tabla
        $create_table = "CREATE TABLE IF NOT EXISTS Preferencias_Usuario (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario INT NOT NULL,
            notificaciones_email TINYINT(1) DEFAULT 0,
            notificaciones_app TINYINT(1) DEFAULT 0,
            UNIQUE (id_usuario)
        )";
        $conn->query($create_table);
    }
    
    // Verificamos el nombre de la columna de ID de usuario en la tabla Preferencias_Usuario
    $table_info_query = "SHOW COLUMNS FROM Preferencias_Usuario";
    $table_info_result = $conn->query($table_info_query);
    
    $id_usuario_column_name = 'id_usuario'; // Valor predeterminado
    
    if ($table_info_result) {
        while ($column = $table_info_result->fetch_assoc()) {
            // Buscar columna que parece ser el ID de usuario
            if (strtolower($column['Field']) == 'id_usuario' || 
                strtolower($column['Field']) == 'usuario_id' || 
                strtolower($column['Field']) == 'idusuario') {
                $id_usuario_column_name = $column['Field'];
                break;
            }
        }
    }
    
    $notificaciones_email = isset($data['notificaciones_email']) ? 1 : 0;
    $notificaciones_app = isset($data['notificaciones_app']) ? 1 : 0;
    
    // Comprobar si ya existe una preferencia para este usuario
    $sql = "SELECT id FROM Preferencias_Usuario WHERE $id_usuario_column_name = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        $messages['error'] = "Error en la consulta: " . $conn->error;
        return $messages;
    }
    
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Actualizar preferencias existentes
        $sql = "UPDATE Preferencias_Usuario SET notificaciones_email = ?, notificaciones_app = ? WHERE $id_usuario_column_name = ?";
    } else {
        // Insertar nuevas preferencias
        $sql = "INSERT INTO Preferencias_Usuario (notificaciones_email, notificaciones_app, $id_usuario_column_name) VALUES (?, ?, ?)";
    }
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        $messages['error'] = "Error en la consulta: " . $conn->error;
        return $messages;
    }
    
    $stmt->bind_param("iii", $notificaciones_email, $notificaciones_app, $id_usuario);
    
    if ($stmt->execute()) {
        $messages['success'] = "Preferencias de notificación actualizadas correctamente";
    } else {
        $messages['error'] = "Error al actualizar las preferencias: " . $stmt->error;
    }
    
    return $messages;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Usuario | Mi Aplicación</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --secondary-color: #475569;
            --secondary-hover: #334155;
            --success-color: #10b981;
            --error-color: #ef4444;
            --text-color: #1e293b;
            --light-text: #64748b;
            --light-bg: #f8fafc;
            --border-color: #cbd5e1;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --transition: all 0.3s ease;
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
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .header h1 {
            font-size: 2rem;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }

        .header p {
            color: var(--light-text);
        }

        .card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .card-header {
            background-color: #f8fafc;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
        }

        .card-header i {
            margin-right: 10px;
            color: var(--primary-color);
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

        .form-control {
            width: 100%;
            padding: 0.75rem;
            font-size: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background-color: #fff;
            color: var(--text-color);
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .form-checkbox {
            margin-right: 0.5rem;
        }

        .btn {
            display: inline-block;
            font-weight: 500;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: 6px;
            transition: var(--transition);
            width: 100%;
            border: none;
        }

        .btn-primary {
            color: white;
            background-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-secondary {
            color: white;
            background-color: var(--secondary-color);
        }

        .btn-secondary:hover {
            background-color: var(--secondary-hover);
        }

        .alert {
            padding: 0.75rem 1rem;
            margin: 1rem 0;
            border-radius: 6px;
            font-weight: 500;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: var(--success-color);
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: var(--error-color);
        }

        .password-rules {
            font-size: 0.875rem;
            color: var(--light-text);
            margin-top: 0.5rem;
        }

        .footer {
            text-align: center;
            margin-top: 2rem;
        }

        .back-link {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            color: white;
            background-color: var(--secondary-color);
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: var(--transition);
        }

        .back-link:hover {
            background-color: var(--secondary-hover);
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            .card-body {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Configuración de Usuario</h1>
            <p>Gestiona tu información personal y preferencias</p>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="fas fa-user"></i>
                <h2 class="card-title">Información Personal</h2>
            </div>
            <div class="card-body">
                <form method="post" autocomplete="off">
                    <input type="hidden" name="action" value="profile">
                    
                    <div class="form-group">
                        <label for="nombre" class="form-label">Nombre completo</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="tel" name="telefono" id="telefono" class="form-control" value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Actualizar perfil</button>
                    </div>
                    
                    <?php if (!empty($messages['profile']['error'])): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $messages['profile']['error']; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($messages['profile']['success'])): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo $messages['profile']['success']; ?>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="fas fa-lock"></i>
                <h2 class="card-title">Cambiar Contraseña</h2>
            </div>
            <div class="card-body">
                <form method="post" autocomplete="off">
                    <input type="hidden" name="action" value="password">
                    
                    <div class="form-group">
                        <label for="password_actual" class="form-label">Contraseña actual</label>
                        <input type="password" name="password_actual" id="password_actual" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_nueva" class="form-label">Nueva contraseña</label>
                        <input type="password" name="password_nueva" id="password_nueva" class="form-control" required>
                        <p class="password-rules">La contraseña debe tener al menos 8 caracteres</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirmar" class="form-label">Confirmar nueva contraseña</label>
                        <input type="password" name="password_confirmar" id="password_confirmar" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Cambiar contraseña</button>
                    </div>
                    
                    <?php if (!empty($messages['password']['error'])): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $messages['password']['error']; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($messages['password']['success'])): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo $messages['password']['success']; ?>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="fas fa-bell"></i>
                <h2 class="card-title">Preferencias de Notificación</h2>
            </div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="action" value="notifications">
                    
                    <div class="form-group">
                        <div class="checkbox-container">
                            <input type="checkbox" name="notificaciones_email" id="notificaciones_email" class="form-checkbox" 
                                <?php echo ($preferencias['notificaciones_email'] == 1) ? 'checked' : ''; ?>>
                            <label for="notificaciones_email" class="form-label">Recibir notificaciones por correo electrónico</label>
                        </div>
                        
                        <div class="checkbox-container">
                            <input type="checkbox" name="notificaciones_app" id="notificaciones_app" class="form-checkbox" 
                                <?php echo ($preferencias['notificaciones_app'] == 1) ? 'checked' : ''; ?>>
                            <label for="notificaciones_app" class="form-label">Recibir notificaciones en la aplicación</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Guardar preferencias</button>
                    </div>
                    
                    <?php if (!empty($messages['notification']['error'])): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $messages['notification']['error']; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($messages['notification']['success'])): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo $messages['notification']['success']; ?>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="footer">
            <a href="../dashboard.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <script>
        // Validación del lado del cliente para mejorar la experiencia del usuario
        document.addEventListener('DOMContentLoaded', function() {
            // Validación de contraseña
            const passwordForm = document.querySelector('form[action="password"]');
            if (passwordForm) {
                passwordForm.addEventListener('submit', function(e) {
                    const newPassword = document.getElementById('password_nueva').value;
                    const confirmPassword = document.getElementById('password_confirmar').value;
                    
                    if (newPassword.length < 8) {
                        e.preventDefault();
                        alert('La contraseña debe tener al menos 8 caracteres');
                        return false;
                    }
                    
                    if (newPassword !== confirmPassword) {
                        e.preventDefault();
                        alert('Las contraseñas no coinciden');
                        return false;
                    }
                    
                    return true;
                });
            }
            
            // Validación de email
            const profileForm = document.querySelector('form[action="profile"]');
            if (profileForm) {
                profileForm.addEventListener('submit', function(e) {
                    const email = document.getElementById('email').value;
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    
                    if (!emailRegex.test(email)) {
                        e.preventDefault();
                        alert('Por favor, introduce un email válido');
                        return false;
                    }
                    
                    return true;
                });
            }
        });
    </script>
</body>
</html>