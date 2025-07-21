<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'dyslexia_app');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración de rutas
define('BASE_URL', 'http://localhost/dyslexia-app');
define('ASSETS_PATH', BASE_URL . '/assets');
define('AUDIO_PATH', ASSETS_PATH . '/audios');
define('IMAGE_PATH', ASSETS_PATH . '/images');

// Configuración de juegos
define('MAX_ATTEMPTS', 3);
define('POINTS_CORRECT', 10);
define('POINTS_BONUS', 5);

