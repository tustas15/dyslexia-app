<?php
/**
 * Environment Configuration Loader
 * Loads environment variables from .env file
 */

class EnvLoader {
    private static $loaded = false;

    public static function load($path = null) {
        if (self::$loaded) {
            return;
        }

        $envFile = $path ?: __DIR__ . '/../.env';

        if (!file_exists($envFile)) {
            // Try .env.example as fallback for development
            $envFile = __DIR__ . '/../.env.example';
            if (!file_exists($envFile)) {
                return;
            }
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes if present
                $value = trim($value, '"\'');

                // Set environment variable
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }

        self::$loaded = true;
    }
}

// Load environment variables on include
EnvLoader::load();
?>
