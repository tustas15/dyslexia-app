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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Iniciar Sesión - Dyslexia App</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body class="bg-gray-100 font-sans">
    <main class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-8">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-blue-600"><i class="fas fa-book-open mr-2 mb-7"></i>Dyslexia App</h1>
                <h2 class="text-2xl font-bold text-blue-600">Iniciar Sesión</h2>
                <p class="text-gray-600 mt-1">¡Bienvenido de nuevo! Ingresa tus datos para continuar.</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4 flex items-center space-x-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= $error ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label for="username" class="block text-gray-700 font-semibold mb-1">Usuario</label>
                    <input type="text" id="username" name="username" required placeholder="Tu nombre de usuario"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" />
                </div>

                <div>
                    <label for="password" class="block text-gray-700 font-semibold mb-1">Contraseña</label>
                    <input type="password" id="password" name="password" required placeholder="Tu contraseña"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" />
                </div>

                <div class="flex items-center justify-between">
                    <a href="register.php" class="text-sm text-blue-600 hover:underline">¿No tienes cuenta? Regístrate</a>
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition-all duration-200">
                        Entrar
                    </button>
                </div>
            </form>

            <div class="text-center mt-6">
                <img src="../assets/images/login.svg" alt="Niños jugando" class="w-40 mx-auto opacity-80">
            </div>
        </div>
    </main>

    <footer class="text-center py-4 text-sm text-gray-500">
        Dyslexia App &copy; <?= date('Y') ?>
    </footer>
</body>

</html>