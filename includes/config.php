<?php
// Load environment variables
require_once 'env.php';

// Configuración de la base de datos desde variables de entorno
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'dyslexia_app');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// Configuración de rutas
define('BASE_URL', getenv('BASE_URL') ?: 'http://localhost/dyslexia-app');
define('ASSETS_PATH', BASE_URL . '/assets');
define('AUDIO_PATH', ASSETS_PATH . '/audios');
define('IMAGE_PATH', ASSETS_PATH . '/images');

// Configuración de juegos
define('MAX_ATTEMPTS', getenv('MAX_LOGIN_ATTEMPTS') ?: 3);
define('POINTS_CORRECT', 10);
define('POINTS_BONUS', 5);

// Configuración de aplicación
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('LOG_LEVEL', getenv('LOG_LEVEL') ?: 'warning');
define('LOG_FILE', getenv('LOG_FILE') ?: __DIR__ . '/../logs/app.log');
