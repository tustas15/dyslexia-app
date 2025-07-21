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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registro - Dyslexia App</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body class="bg-gray-100 font-sans">
    <main class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-8">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-blue-600"><i class="fas fa-book-open mr-2 mb-7"></i>Dyslexia App</h1>
                <h2 class="text-2xl font-bold text-blue-600">Registro</h2>
                <p class="text-gray-600 mt-1">¡Regístrate para comenzar a jugar y aprender!</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4 flex items-center space-x-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= $error ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4 flex items-center space-x-2">
                    <i class="fas fa-check-circle"></i>
                    <span><?= $success ?></span>
                </div>
                <div class="text-center">
                    <a href="login.php" class="text-blue-600 hover:underline">Iniciar sesión</a>
                </div>
            <?php else: ?>
                <form method="POST" class="space-y-5">
                    <div>
                        <label for="username" class="block text-gray-700 font-semibold mb-1">Usuario</label>
                        <input type="text" id="username" name="username" required placeholder="Elige un nombre de usuario"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" />
                    </div>

                    <div>
                        <label for="password" class="block text-gray-700 font-semibold mb-1">Contraseña</label>
                        <input type="password" id="password" name="password" required placeholder="Crea una contraseña"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" />
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-gray-700 font-semibold mb-1">Confirmar Contraseña</label>
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Repite la contraseña"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" />
                    </div>

                    <div>
                        <label for="age" class="block text-gray-700 font-semibold mb-1">Edad</label>
                        <input type="number" id="age" name="age" min="5" max="12" required placeholder="¿Cuántos años tienes?"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" />
                        <div class="bg-blue-100 text-blue-700 p-2 mt-2 rounded-md text-sm flex items-center gap-2">
                            <i class="fas fa-info-circle"></i>
                            <span>La aplicación está diseñada para niños entre 5 y 12 años.</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="login.php" class="text-sm text-blue-600 hover:underline">¿Ya tienes cuenta? Inicia sesión</a>
                        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition-all duration-200">
                            Registrarse
                        </button>
                    </div>
                </form>
            <?php endif; ?>

            <div class="text-center mt-6">
                <img src="../assets/images/register.svg" alt="Niños aprendiendo" class="w-40 mx-auto opacity-80">
            </div>
        </div>
    </main>

    <footer class="text-center py-4 text-sm text-gray-500">
        Dyslexia App &copy; <?= date('Y') ?>
    </footer>
</body>

</html>
