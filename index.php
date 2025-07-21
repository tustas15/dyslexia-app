<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';

safe_session_start();

if (!is_logged_in()) {
    header('Location: user/login.php');
    exit;
}

$games = [
    'auditory-codes' => [
        'name' => 'Rompecódigos Auditivos',
        'icon' => '🎧',
        'description' => 'Escucha y selecciona la palabra correcta'
    ],
    'syllable-hunt' => [
        'name' => 'Caza Sílabas',
        'icon' => '🔍',
        'description' => 'Ordena sílabas para formar palabras'
    ],
    'word-painting' => [
        'name' => 'Pintando Palabras',
        'icon' => '🎨',
        'description' => 'Colorea las letras correctas'
    ],
    'letter-detective' => [
        'name' => 'Detective de Letras',
        'icon' => '🕵️',
        'description' => 'Identifica letras correctas'
    ],
    'interactive-story' => [
        'name' => 'Cuento Interactivo',
        'icon' => '📖',
        'description' => 'Crea tu propio cuento'
    ],
    'word-robot' => [
        'name' => 'Palabrabot',
        'icon' => '🤖',
        'description' => 'Corrige las palabras del robot'
    ],
    'rhyme-platform' => [
        'name' => 'Saltarima',
        'icon' => '🦘',
        'description' => 'Salta sobre palabras que riman'
    ]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dyslexia App</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800 font-sans min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
        <h1 class="text-xl sm:text-2xl font-bold text-blue-600 flex items-center gap-2">
            <i class="fas fa-book-open"></i> Dyslexia App
        </h1>
        <nav class="flex gap-4 text-sm sm:text-base">
            <a href="progress/dashboard.php" class="text-blue-600 hover:underline flex items-center gap-1">
                <i class="fas fa-chart-line"></i> Progreso
            </a>
            <a href="user/logout.php" class="text-red-600 hover:underline flex items-center gap-1">
                <i class="fas fa-sign-out-alt"></i> Salir
            </a>
        </nav>
    </header>

    <!-- Main: Juegos -->
    <main class="flex-grow px-6 py-10">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($games as $id => $game): ?>
                <a href="games/<?= $id ?>/" class="bg-white rounded-xl shadow-md p-6 text-center hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="text-5xl mb-3"><?= $game['icon'] ?></div>
                    <h3 class="text-lg font-bold text-blue-700 mb-2"><?= $game['name'] ?></h3>
                    <p class="text-gray-600 text-sm"><?= $game['description'] ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white text-center text-gray-500 text-sm py-4 border-t mt-10">
        Dyslexia App &copy; <?= date('Y') ?> - Ayudando a niños con dislexia
    </footer>

</body>
</html>
