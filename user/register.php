<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';

safe_session_start();

if (is_logged_in()) {
    header('Location: ../index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $age = (int)($_POST['age'] ?? 0);
    
    if (empty($username) || empty($password) || empty($confirm_password) || $age === 0) {
        $error = 'Por favor completa todos los campos';
    } elseif ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden';
    } elseif ($age < 5 || $age > 12) {
        $error = 'La edad debe estar entre 5 y 12 años';
    } elseif (register($username, $password, $age)) {
        $success = '¡Registro exitoso! Ahora puedes iniciar sesión';
    } else {
        $error = 'El usuario ya existe o hubo un error';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Dyslexia App</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }
        
        .register-form {
            background-color: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .register-header h1 {
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .register-header p {
            color: var(--dark);
            opacity: 0.8;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: var(--dark);
        }
        
        .form-group input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 1.1rem;
            font-family: 'OpenDyslexic', Arial, sans-serif;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            border-color: var(--primary);
            outline: none;
        }
        
        .register-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
        }
        
        .register-actions a {
            color: var(--primary);
            text-decoration: none;
        }
        
        .register-actions a:hover {
            text-decoration: underline;
        }
        
        .error-message {
            background-color: rgba(244, 67, 54, 0.1);
            color: var(--error);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        
        .success-message {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        
        .age-info {
            background-color: rgba(78, 137, 174, 0.1);
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        
        .register-image {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .register-image img {
            max-width: 200px;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <header>
        <h1><i class="fas fa-book-open"></i> Dyslexia App</h1>
    </header>
    
    <main class="register-container">
        <div class="register-form">
            <div class="register-header">
                <h1>Crear Cuenta</h1>
                <p>Regístrate para comenzar a jugar y aprender</p>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <p><?= $error ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <p><?= $success ?></p>
                </div>
                <div class="register-actions">
                    <a href="login.php">Iniciar Sesión</a>
                </div>
            <?php else: ?>
                <form method="POST">
                    <div class="form-group">
                        <label for="username">Usuario</label>
                        <input type="text" id="username" name="username" required placeholder="Elige un nombre de usuario">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" required placeholder="Crea una contraseña segura">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirmar Contraseña</label>
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Repite tu contraseña">
                    </div>
                    
                    <div class="form-group">
                        <label for="age">Edad</label>
                        <input type="number" id="age" name="age" min="5" max="12" required placeholder="¿Cuántos años tienes?">
                        <div class="age-info">
                            <i class="fas fa-info-circle"></i>
                            La aplicación está diseñada para niños entre 5 y 12 años
                        </div>
                    </div>
                    
                    <div class="register-actions">
                        <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
                        <button type="submit">Registrarse</button>
                    </div>
                </form>
            <?php endif; ?>
            
            <div class="register-image">
                <img src="../assets/images/register.svg" alt="Niños aprendiendo">
            </div>
        </div>
    </main>
    
    <footer>
        <p>Dyslexia App &copy; <?= date('Y') ?></p>
    </footer>
</body>
</html>