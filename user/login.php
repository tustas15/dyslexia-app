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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (login($username, $password)) {
        header('Location: ../index.php');
        exit;
    } else {
        $error = 'Usuario o contraseña incorrectos';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Dyslexia App</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }
        
        .login-form {
            background-color: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header h1 {
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
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
        
        .login-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
        }
        
        .login-actions a {
            color: var(--primary);
            text-decoration: none;
        }
        
        .login-actions a:hover {
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
        
        .login-image {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .login-image img {
            max-width: 200px;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <header>
        <h1><i class="fas fa-book-open"></i> Dyslexia App</h1>
    </header>
    
    <main class="login-container">
        <div class="login-form">
            <div class="login-header">
                <h1>Iniciar Sesión</h1>
                <p>¡Bienvenido de nuevo! Por favor inicia sesión para continuar</p>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <p><?= $error ?></p>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" id="username" name="username" required placeholder="Tu nombre de usuario">
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required placeholder="Tu contraseña">
                </div>
                
                <div class="login-actions">
                    <a href="register.php">¿No tienes cuenta? Regístrate</a>
                    <button type="submit">Entrar</button>
                </div>
            </form>
            
            <div class="login-image">
                <img src="../assets/images/login.svg" alt="Niños jugando">
            </div>
        </div>
    </main>
    
    <footer>
        <p>Dyslexia App &copy; <?= date('Y') ?></p>
    </footer>
</body>
</html>