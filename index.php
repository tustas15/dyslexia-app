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
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <h1><i class="fas fa-book-open"></i> Dyslexia App</h1>
        <nav>
            <a href="progress/dashboard.php"><i class="fas fa-chart-line"></i> Progreso</a>
            <a href="user/logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </nav>
    </header>
    
    <main class="games-grid">
        <?php foreach ($games as $id => $game): ?>
            <a href="games/<?= $id ?>/" class="game-card">
                <div class="game-icon"><?= $game['icon'] ?></div>
                <h3><?= $game['name'] ?></h3>
                <p><?= $game['description'] ?></p>
            </a>
        <?php endforeach; ?>
    </main>
    
    <footer>
        <p>Dyslexia App &copy; <?= date('Y') ?> - Ayudando a niños con dislexia</p>
    </footer>
    
    <script src="assets/js/main.js"></script>
</body>
</html>