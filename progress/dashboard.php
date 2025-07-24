<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';

safe_session_start();

if (!is_logged_in()) {
    header('Location: ../user/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Obtener progreso del usuario
global $db;
$stmt = $db->prepare("SELECT game_type, SUM(score) AS total_score, 
                     COUNT(*) AS games_played, MAX(timestamp) AS last_played
                     FROM user_progress 
                     WHERE user_id = ?
                     GROUP BY game_type");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$progress = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Obtener estadísticas generales
$stmt = $db->prepare("SELECT SUM(score) AS overall_score, 
                     COUNT(DISTINCT game_type) AS games_unlocked
                     FROM user_progress 
                     WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

$game_names = [
    'auditory-codes' => 'Rompecódigos Auditivos',
    'syllable-hunt' => 'Caza Sílabas',
    'word-painting' => 'Pintando Palabras',
    'letter-detective' => 'Detective de Letras',
    'interactive-story' => 'Cuento Interactivo',
    'word-robot' => 'Palabrabot',
    'rhyme-platform' => 'Saltarima',
    'letter-detective' => 'Detective de Letras',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Panel de Progreso - Dyslexia App</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="bg-gray-100 text-gray-800 font-sans min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
        <a href="../index.php" class="text-blue-600 hover:underline flex items-center gap-2 font-semibold">
            <i class="fas fa-arrow-left"></i> Inicio
        </a>
        <h1 class="text-xl sm:text-2xl font-bold text-blue-600 flex items-center gap-2">
            <i class="fas fa-chart-line"></i> Panel de Progreso
        </h1>
        <nav class="flex gap-4 text-sm sm:text-base">
            <a href="../user/logout.php" class="text-red-600 hover:underline flex items-center gap-1">
                <i class="fas fa-sign-out-alt"></i> Salir
            </a>
        </nav>
    </header>

    <!-- Estadísticas generales -->
    <main class="flex-grow px-6 py-10 container mx-auto max-w-6xl">

        <section class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
            <div class="bg-white rounded-xl shadow-md p-6 flex flex-col items-center">
                <h3 class="text-gray-600 font-semibold mb-2">Puntuación Total</h3>
                <p class="text-4xl font-extrabold text-blue-700"><?= $stats['overall_score'] ?? 0 ?></p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 flex flex-col items-center">
                <h3 class="text-gray-600 font-semibold mb-2">Juegos Completados</h3>
                <p class="text-4xl font-extrabold text-blue-700"><?= $stats['games_unlocked'] ?? 0 ?></p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 flex flex-col items-center">
                <h3 class="text-gray-600 font-semibold mb-2">Nivel Actual</h3>
                <p class="text-4xl font-extrabold text-blue-700"><?= floor(($stats['overall_score'] ?? 0) / 100) + 1 ?></p>
            </div>
        </section>

        <!-- Progreso por juego -->
        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b border-blue-300 pb-2">Progreso por Juego</h2>

        <section class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($progress as $game):
                $progressPercent = min(100, ($game['total_score'] / 50) * 100);
                $gameName = $game_names[$game['game_type']] ?? $game['game_type'];
            ?>
                <article class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition transform hover:-translate-y-1">
                    <h3 class="text-xl font-bold text-blue-700 mb-3"><?= htmlspecialchars($gameName) ?></h3>
                    <div class="w-full bg-blue-100 rounded-full h-4 mb-3 overflow-hidden">
                        <div class="bg-blue-600 h-4 rounded-full transition-all duration-500" style="width: <?= $progressPercent ?>%"></div>
                    </div>
                    <p class="text-gray-700 font-medium mb-1">Puntos: <span class="font-semibold"><?= $game['total_score'] ?></span></p>
                    <p class="text-gray-700 font-medium mb-1">Jugados: <span class="font-semibold"><?= $game['games_played'] ?></span></p>
                    <p class="text-gray-500 text-sm">Último juego: <?= date('d/m/Y', strtotime($game['last_played'])) ?></p>
                </article>
            <?php endforeach; ?>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-white text-center text-gray-500 text-sm py-4 border-t mt-10">
        Dyslexia App &copy; <?= date('Y') ?> - Ayudando a niños con dislexia
    </footer>

</body>
</html>
