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
    'rhyme-platform' => 'Saltarima'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Progreso</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/progress.css">
</head>
<body>
    <header>
        <a href="../index.php"><i class="fas fa-arrow-left"></i> Inicio</a>
        <h1>Tu Progreso</h1>
    </header>
    
    <main>
        <div class="stats-summary">
            <div class="stat-card">
                <h3>Puntuación Total</h3>
                <p class="big-number"><?= $stats['overall_score'] ?? 0 ?></p>
            </div>
            <div class="stat-card">
                <h3>Juegos Completados</h3>
                <p class="big-number"><?= $stats['games_unocked'] ?? 0 ?></p>
            </div>
            <div class="stat-card">
                <h3>Nivel Actual</h3>
                <p class="big-number"><?= floor(($stats['overall_score'] ?? 0) / 100) + 1 ?></p>
            </div>
        </div>
        
        <h2>Progreso por Juego</h2>
        <div class="progress-grid">
            <?php foreach ($progress as $game): ?>
                <div class="progress-card">
                    <h3><?= $game_names[$game['game_type']] ?? $game['game_type'] ?></h3>
                    <div class="progress-bar">
                        <div class="progress-fill" 
                             style="width: <?= min(100, $game['total_score'] / 50) ?>%">
                        </div>
                    </div>
                    <p>Puntos: <?= $game['total_score'] ?></p>
                    <p>Jugados: <?= $game['games_played'] ?></p>
                    <p>Último: <?= date('d/m/Y', strtotime($game['last_played'])) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
    
    <footer>
        <p>Sigue practicando para mejorar tus habilidades</p>
    </footer>
</body>
</html>