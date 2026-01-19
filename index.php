<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';

safe_session_start();

if (!is_logged_in()) {
    header('Location: user/login.php');
    exit;
}

$page_title = 'Inicio';

$games = [
    'auditory-codes' => [
        'name' => 'Rompecódigos Auditivos',
        'icon' => '🎧',
        'description' => 'Escucha y selecciona la palabra correcta',
        'color' => 'purple'
    ],
    'syllable-hunt' => [
        'name' => 'Caza Sílabas',
        'icon' => '🔍',
        'description' => 'Ordena sílabas para formar palabras',
        'color' => 'blue'
    ],
    'word-painting' => [
        'name' => 'Pintando Palabras',
        'icon' => '🎨',
        'description' => 'Colorea las letras correctas',
        'color' => 'green'
    ],
    'letter-detective' => [
        'name' => 'Detective de Letras',
        'icon' => '🕵️',
        'description' => 'Identifica letras correctas',
        'color' => 'yellow'
    ],
    'interactive-story' => [
        'name' => 'Cuento Interactivo',
        'icon' => '📖',
        'description' => 'Crea tu propio cuento',
        'color' => 'indigo'
    ],
    'word-robot' => [
        'name' => 'Palabrabot',
        'icon' => '🤖',
        'description' => 'Corrige las palabras del robot',
        'color' => 'gray'
    ],
    'rhyme-platform' => [
        'name' => 'Saltarima',
        'icon' => '🦘',
        'description' => 'Salta sobre palabras que riman',
        'color' => 'emerald'
    ]
];

ob_start();
?>

<div class="px-6 py-10">
    <!-- Welcome Section -->
    <div class="text-center mb-12">
        <h2 class="text-4xl lg:text-5xl font-bold text-gray-800 dark:text-gray-100 mb-4">
            ¡Bienvenido a Dyslexia App! 🎉
        </h2>
        <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
            Aprende de manera divertida con nuestros juegos especialmente diseñados para niños con dislexia.
            ¡Elige un juego y comienza tu aventura educativa!
        </p>
    </div>

    <!-- Games Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 max-w-7xl mx-auto">
        <?php foreach ($games as $id => $game): ?>
            <a href="games/<?= $id ?>/"
               class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl p-6 text-center transition-all duration-300 transform hover:-translate-y-2 border border-gray-200 dark:border-gray-700 hover:border-<?= $game['color'] ?>-300 dark:hover:border-<?= $game['color'] ?>-600">
                <div class="text-6xl mb-4 transform group-hover:scale-110 transition-transform duration-300">
                    <?= $game['icon'] ?>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-3 group-hover:text-<?= $game['color'] ?>-600 dark:group-hover:text-<?= $game['color'] ?>-400 transition-colors duration-300">
                    <?= $game['name'] ?>
                </h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">
                    <?= $game['description'] ?>
                </p>
                <div class="mt-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-<?= $game['color'] ?>-100 dark:bg-<?= $game['color'] ?>-900 text-<?= $game['color'] ?>-800 dark:text-<?= $game['color'] ?>-200">
                        <i class="fas fa-play mr-1"></i>
                        Jugar ahora
                    </span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Stats Section -->
    <div class="mt-16 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-700 rounded-3xl p-8 border border-blue-200 dark:border-gray-600">
        <div class="text-center mb-8">
            <h3 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-2">
                Tu Progreso
            </h3>
            <p class="text-gray-600 dark:text-gray-300">
                Sigue aprendiendo y mejora tus habilidades
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center shadow-md border border-gray-200 dark:border-gray-600">
                <div class="text-4xl mb-3">🎯</div>
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-1">
                    <?php
                    // Get total games played from database
                    global $db;
                    $user_id = $_SESSION['user_id'];
                    $stmt = $db->prepare("SELECT COUNT(DISTINCT game_type) as games_played FROM user_progress WHERE user_id = ?");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result()->fetch_assoc();
                    echo $result['games_played'] ?? 0;
                    ?>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Juegos jugados</div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center shadow-md border border-gray-200 dark:border-gray-600">
                <div class="text-4xl mb-3">⭐</div>
                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mb-1">
                    <?php
                    $stmt = $db->prepare("SELECT SUM(score) as total_score FROM user_progress WHERE user_id = ?");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result()->fetch_assoc();
                    echo $result['total_score'] ?? 0;
                    ?>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Puntos totales</div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center shadow-md border border-gray-200 dark:border-gray-600">
                <div class="text-4xl mb-3">🏆</div>
                <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-1">
                    <?php
                    $total_score = $result['total_score'] ?? 0;
                    echo floor($total_score / 100) + 1; // Simple level calculation
                    ?>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Nivel actual</div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/base_layout.php';
?>
