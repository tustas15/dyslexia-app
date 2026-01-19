<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';

safe_session_start();

if (is_logged_in()) {
    header('Location: ../index.php');
    exit;
}

$page_title = 'Registro';

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

ob_start();
?>

<div class="min-h-screen flex items-center justify-center px-4 py-12 bg-gradient-to-br from-green-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 transition-colors duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-md p-8 border border-gray-200 dark:border-gray-700 transition-colors duration-300">
        <!-- Theme Toggle Button -->
        <div class="flex justify-end mb-4">
            <button id="theme-toggle" class="text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white p-2 rounded-lg transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500" title="Cambiar tema">
                <i class="fas fa-moon text-lg"></i>
            </button>
        </div>

        <!-- Header -->
        <div class="text-center mb-8">
            <div class="text-5xl mb-4">🎨</div>
            <h1 class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">
                <i class="fas fa-user-plus mr-2"></i>Dyslexia App
            </h1>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Registro</h2>
            <p class="text-gray-600 dark:text-gray-300 mt-2">¡Regístrate para comenzar a jugar y aprender!</p>
        </div>

        <!-- Error Message -->
        <?php if ($error): ?>
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 p-4 rounded-xl mb-6 flex items-center space-x-3 transition-colors duration-300">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <span class="font-medium"><?= $error ?></span>
            </div>
        <?php endif; ?>

        <!-- Success Message -->
        <?php if ($success): ?>
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 p-4 rounded-xl mb-6 flex items-center space-x-3 transition-colors duration-300">
                <i class="fas fa-check-circle text-green-500"></i>
                <span class="font-medium"><?= $success ?></span>
            </div>
            <div class="text-center">
                <a href="login.php" class="inline-flex items-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                    <i class="fas fa-sign-in-alt mr-2"></i>Iniciar sesión
                </a>
            </div>
        <?php else: ?>

        <!-- Registration Form -->
        <form method="POST" class="space-y-5">
            <div>
                <label for="username" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 text-sm">
                    <i class="fas fa-user mr-2 text-green-500"></i>Usuario
                </label>
                <input type="text"
                       id="username"
                       name="username"
                       required
                       placeholder="Elige un nombre de usuario"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 transition-colors duration-300"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" />
            </div>

            <div>
                <label for="password" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 text-sm">
                    <i class="fas fa-lock mr-2 text-green-500"></i>Contraseña
                </label>
                <input type="password"
                       id="password"
                       name="password"
                       required
                       placeholder="Crea una contraseña segura"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 transition-colors duration-300" />
            </div>

            <div>
                <label for="confirm_password" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 text-sm">
                    <i class="fas fa-shield-alt mr-2 text-green-500"></i>Confirmar Contraseña
                </label>
                <input type="password"
                       id="confirm_password"
                       name="confirm_password"
                       required
                       placeholder="Repite la contraseña"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 transition-colors duration-300" />
            </div>

            <div>
                <label for="age" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 text-sm">
                    <i class="fas fa-birthday-cake mr-2 text-green-500"></i>Edad
                </label>
                <input type="number"
                       id="age"
                       name="age"
                       min="5"
                       max="12"
                       required
                       placeholder="¿Cuántos años tienes?"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 transition-colors duration-300"
                       value="<?= htmlspecialchars($_POST['age'] ?? '') ?>" />
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300 p-3 mt-2 rounded-lg text-sm flex items-center gap-2 transition-colors duration-300">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    <span>La aplicación está diseñada para niños entre 5 y 12 años.</span>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="login.php" class="text-sm text-green-600 dark:text-green-400 hover:underline font-medium transition-colors duration-300">
                    ¿Ya tienes cuenta? Inicia sesión
                </a>
                <button type="submit" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <i class="fas fa-user-plus mr-2"></i>Registrarse
                </button>
            </div>
        </form>

        <?php endif; ?>

        <!-- Illustration -->
        <div class="text-center mt-8">
            <img src="../assets/images/register.svg" alt="Niños aprendiendo" class="w-32 mx-auto opacity-80 dark:opacity-60 transition-opacity duration-300">
        </div>
    </div>
</div>

<!-- Theme Toggle Script -->
<script>
    // Theme management class
    class ThemeManager {
        constructor() {
            this.theme = localStorage.getItem('theme') || 'light';
            this.init();
        }

        init() {
            this.applyTheme();
            this.setupEventListeners();
        }

        setupEventListeners() {
            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', () => this.toggleTheme());
            }
        }

        toggleTheme() {
            this.theme = this.theme === 'light' ? 'dark' : 'light';
            this.applyTheme();
            localStorage.setItem('theme', this.theme);
        }

        applyTheme() {
            const html = document.documentElement;
            const icon = document.querySelector('#theme-toggle i');

            if (this.theme === 'dark') {
                html.classList.add('dark');
                if (icon) icon.className = 'fas fa-sun text-lg';
            } else {
                html.classList.remove('dark');
                if (icon) icon.className = 'fas fa-moon text-lg';
            }
        }
    }

    // Initialize theme manager when DOM is loaded
    document.addEventListener('DOMContentLoaded', () => {
        new ThemeManager();
    });
</script>

<?php
$content = ob_get_clean();

// Custom layout for auth pages (no header navigation)
?>
<!DOCTYPE html>
<html lang="es" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dyslexia App - <?= $page_title ?? 'Inicio' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {}
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/games.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 font-sans min-h-screen flex flex-col transition-colors duration-300">
    <!-- Main Content -->
    <main class="flex-grow">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 text-center text-gray-500 dark:text-gray-400 text-sm py-4 border-t border-gray-200 dark:border-gray-700 mt-10 transition-colors duration-300">
        Dyslexia App &copy; <?= date('Y') ?> - Ayudando a niños con dislexia
    </footer>
</body>
</html>
