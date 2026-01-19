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

$page_title = 'Panel de Progreso';

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
];

$game_icons = [
    'auditory-codes' => 'fas fa-volume-up',
    'syllable-hunt' => 'fas fa-search',
    'word-painting' => 'fas fa-paint-brush',
    'letter-detective' => 'fas fa-search-plus',
    'interactive-story' => 'fas fa-book',
    'word-robot' => 'fas fa-robot',
    'rhyme-platform' => 'fas fa-frog',
];

$game_colors = [
    'auditory-codes' => 'purple',
    'syllable-hunt' => 'blue',
    'word-painting' => 'green',
    'letter-detective' => 'yellow',
    'interactive-story' => 'indigo',
    'word-robot' => 'gray',
    'rhyme-platform' => 'emerald',
];

ob_start();
?>

<div class="px-6 py-10 max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="text-center mb-12">
        <div class="text-6xl mb-4">📊</div>
        <h1 class="text-4xl lg:text-5xl font-bold text-gray-800 dark:text-gray-100 mb-4">
            Tu Progreso
        </h1>
        <p class="text-xl text-gray-600 dark:text-gray-300">
            ¡Sigue aprendiendo y mejora tus habilidades!
        </p>
    </div>

    <!-- Overall Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        <!-- Total Score -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-3xl p-8 text-white shadow-2xl transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="text-4xl">
                    <i class="fas fa-star"></i>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold mb-1"><?= number_format($stats['overall_score'] ?? 0) ?></div>
                    <div class="text-blue-100 text-sm">Puntos Totales</div>
                </div>
            </div>
            <div class="w-full bg-blue-300 dark:bg-blue-400 rounded-full h-2">
                <div class="bg-white h-2 rounded-full transition-all duration-1000" style="width: 100%"></div>
            </div>
        </div>

        <!-- Games Completed -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 rounded-3xl p-8 text-white shadow-2xl transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="text-4xl">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold mb-1"><?= $stats['games_unlocked'] ?? 0 ?>/7</div>
                    <div class="text-green-100 text-sm">Juegos Completados</div>
                </div>
            </div>
            <div class="w-full bg-green-300 dark:bg-green-400 rounded-full h-2">
                <div class="bg-white h-2 rounded-full transition-all duration-1000" style="width: <?= min(100, (($stats['games_unlocked'] ?? 0) / 7) * 100) ?>%"></div>
            </div>
        </div>

        <!-- Current Level -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700 rounded-3xl p-8 text-white shadow-2xl transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="text-4xl">
                    <i class="fas fa-level-up-alt"></i>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold mb-1">Nivel <?= floor(($stats['overall_score'] ?? 0) / 100) + 1 ?></div>
                    <div class="text-purple-100 text-sm">Nivel Actual</div>
                </div>
            </div>
            <div class="w-full bg-purple-300 dark:bg-purple-400 rounded-full h-2">
                <div class="bg-white h-2 rounded-full transition-all duration-1000" style="width: <?= min(100, (($stats['overall_score'] ?? 0) % 100)) ?>%"></div>
            </div>
        </div>
    </div>

    <!-- Games Progress Section -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-8 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                <i class="fas fa-gamepad mr-3 text-blue-600 dark:text-blue-400"></i>
                Progreso por Juego
            </h2>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Última actualización: <?= date('d/m/Y H:i') ?>
            </div>
        </div>

        <?php if (empty($progress)): ?>
            <!-- No progress yet -->
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🎮</div>
                <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-4">¡Comienza tu aventura!</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Aún no has jugado ningún juego. ¡Elige uno y comienza a aprender!</p>
                <a href="../index.php" class="inline-flex items-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                    <i class="fas fa-play mr-2"></i>Ir a los juegos
                </a>
            </div>
        <?php else: ?>
            <!-- Progress Grid -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($progress as $game):
                    $progressPercent = min(100, ($game['total_score'] / 50) * 100);
                    $gameName = $game_names[$game['game_type']] ?? $game['game_type'];
                    $gameIcon = $game_icons[$game['game_type']] ?? 'fas fa-gamepad';
                    $gameColor = $game_colors[$game['game_type']] ?? 'blue';
                ?>
                    <div class="bg-gradient-to-br from-<?= $gameColor ?>-50 to-<?= $gameColor ?>-100 dark:from-gray-700 dark:to-gray-600 rounded-2xl p-6 border border-<?= $gameColor ?>-200 dark:border-gray-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <!-- Game Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="text-3xl text-<?= $gameColor ?>-600 dark:text-<?= $gameColor ?>-400 mr-3">
                                    <i class="<?= $gameIcon ?>"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                                        <?= htmlspecialchars($gameName) ?>
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        <?= $game['games_played'] ?> <?= $game['games_played'] == 1 ? 'vez jugada' : 'veces jugadas' ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300 mb-2">
                                <span>Progreso</span>
                                <span class="font-semibold"><?= number_format($progressPercent, 0) ?>%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-<?= $gameColor ?>-500 to-<?= $gameColor ?>-600 h-3 rounded-full transition-all duration-1000 ease-out"
                                     style="width: <?= $progressPercent ?>%">
                                </div>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-3">
                                <div class="text-2xl font-bold text-<?= $gameColor ?>-600 dark:text-<?= $gameColor ?>-400 mb-1">
                                    <?= $game['total_score'] ?>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Puntos</div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-3">
                                <div class="text-2xl font-bold text-<?= $gameColor ?>-600 dark:text-<?= $gameColor ?>-400 mb-1">
                                    <?= $game['games_played'] ?>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Jugadas</div>
                            </div>
                        </div>

                        <!-- Last Played -->
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                            <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                Último juego: <?= date('d/m/Y', strtotime($game['last_played'])) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Achievement Section -->
    <?php if (($stats['overall_score'] ?? 0) > 0): ?>
    <div class="mt-12 bg-gradient-to-r from-yellow-400 via-orange-500 to-red-500 dark:from-yellow-500 dark:via-orange-600 dark:to-red-600 rounded-3xl p-8 text-white text-center shadow-2xl">
        <div class="text-5xl mb-4">🏆</div>
        <h3 class="text-3xl font-bold mb-2">¡Gran Trabajo!</h3>
        <p class="text-lg opacity-90">
            Has acumulado <?= number_format($stats['overall_score'] ?? 0) ?> puntos jugando.
            ¡Sigue practicando para mejorar tus habilidades!
        </p>
    </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include '../includes/base_layout.php';
?>
