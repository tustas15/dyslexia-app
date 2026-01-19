<?php
/**
 * Base Layout Template with Dark Mode Support
 * Provides consistent header, navigation, and theme switching for all pages
 */
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
    <link rel="stylesheet" href="assets/css/games.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 font-sans min-h-screen flex flex-col transition-colors duration-300">

    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow-md py-4 px-6 flex justify-between items-center transition-colors duration-300 border-b border-gray-200 dark:border-gray-700">
        <a href="index.php" class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2 font-semibold transition-colors duration-300">
            <i class="fas fa-arrow-left"></i> Inicio
        </a>
        <h1 class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400 flex items-center gap-2">
            <i class="fas fa-book-open"></i> Dyslexia App
        </h1>
        <nav class="flex gap-4 text-sm sm:text-base items-center">
            <button id="theme-toggle" class="text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white p-2 rounded-lg transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500" title="Cambiar tema">
                <i class="fas fa-moon text-lg"></i>
            </button>
            <a href="progress/dashboard.php" class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1 transition-colors duration-300">
                <i class="fas fa-chart-line"></i> Progreso
            </a>
            <a href="user/logout.php" class="text-red-600 dark:text-red-400 hover:underline flex items-center gap-1 transition-colors duration-300">
                <i class="fas fa-sign-out-alt"></i> Salir
            </a>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 text-center text-gray-500 dark:text-gray-400 text-sm py-4 border-t border-gray-200 dark:border-gray-700 mt-10 transition-colors duration-300">
        Dyslexia App &copy; <?= date('Y') ?> - Ayudando a niños con dislexia
    </footer>

    <!-- Theme Toggle Script -->
    <script>
        // Global theme management
        window.ThemeManager = {
            theme: localStorage.getItem('theme') || 'light',

            init() {
                this.applyTheme();
                this.setupEventListeners();
                console.log('ThemeManager initialized with theme:', this.theme);
            },

            setupEventListeners() {
                const themeToggle = document.getElementById('theme-toggle');
                if (themeToggle) {
                    themeToggle.addEventListener('click', () => this.toggleTheme());
                    console.log('Theme toggle button found and event listener attached');
                } else {
                    console.warn('Theme toggle button not found');
                }
            },

            toggleTheme() {
                this.theme = this.theme === 'light' ? 'dark' : 'light';
                this.applyTheme();
                localStorage.setItem('theme', this.theme);
                console.log('Theme toggled to:', this.theme);

                // Dispatch custom event for other components
                window.dispatchEvent(new CustomEvent('themeChanged', {
                    detail: { theme: this.theme }
                }));
            },

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

                console.log('Theme applied:', this.theme);
            },

            getCurrentTheme() {
                return this.theme;
            }
        };

        // Initialize theme manager when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            window.ThemeManager.init();
        });

        // Make theme manager globally available
        if (typeof window !== 'undefined') {
            window.toggleTheme = () => window.ThemeManager.toggleTheme();
            window.getCurrentTheme = () => window.ThemeManager.getCurrentTheme();
        }
    </script>

</body>
</html>
