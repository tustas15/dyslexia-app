<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

safe_session_start();

if (!is_logged_in()) {
    header('Location: ../../user/login.php');
    exit;
}

$level = $_GET['level'] ?? 1;
$next_level = min(3, $level + 1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Nivel Completado - Dyslexia App</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-2xl w-full text-center">
        <div class="mb-6">
            <i class="fas fa-robot text-blue-500 text-6xl mb-4"></i>
            <h1 class="text-3xl font-bold text-blue-700 mb-2">¡Nivel <?= $level ?> Completado!</h1>
            <p class="text-xl text-gray-600">Has ayudado al robot a corregir todas las palabras</p>
        </div>
        
        <div class="bg-blue-50 rounded-xl p-6 mb-8">
            <div class="flex justify-center mb-6">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <p class="text-gray-600">Palabras corregidas</p>
                    <p class="text-3xl font-bold text-blue-700">3/3</p>
                </div>
            </div>
            
            <div class="stars flex justify-center mb-4">
                <?php for ($i = 0; $i < 3; $i++): ?>
                    <i class="fas fa-star text-yellow-400 text-3xl mx-1"></i>
                <?php endfor; ?>
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="index.php?level=<?= $level ?>" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition">
                <i class="fas fa-redo mr-2"></i> Repetir nivel
            </a>
            
            <?php if ($next_level > $level): ?>
                <a href="index.php?level=<?= $next_level ?>" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition">
                    <i class="fas fa-arrow-right mr-2"></i> Nivel <?= $next_level ?>
                </a>
            <?php else: ?>
                <a href="../../index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition">
                    <i class="fas fa-home mr-2"></i> Volver al inicio
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>