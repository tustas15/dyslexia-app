<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';

safe_session_start();

if (!is_logged_in()) {
    header('Location: ../../user/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$level = $_GET['level'] ?? 1;

// Determinar dificultad según nivel
$difficulty = 'easy';
if ($level == 2) $difficulty = 'medium';
elseif ($level >= 3) $difficulty = 'hard';

// Obtener progreso actual del nivel
$stmt = $db->prepare("SELECT SUM(score) AS level_score 
                     FROM user_progress 
                     WHERE user_id = ? 
                     AND game_type = 'auditory-codes' 
                     AND JSON_EXTRACT(details, '$.level') = ?"); // Cambio aquí
$stmt->bind_param("ii", $user_id, $level);
$stmt->execute();
$progress = $stmt->get_result()->fetch_assoc();
$current_score = $progress['level_score'] ?? 0;

// Calcular palabras completadas en este nivel
$words_per_level = 10;
$words_completed = floor($current_score / 10); // Cada palabra correcta da 10 puntos

// Verificar si el nivel está completo
if ($words_completed >= $words_per_level) {
    header("Location: level_complete.php?level=$level");
    exit;
}

// Consulta optimizada y corregida
$sql = "SELECT w.id, w.word, w.audio_path, 
        GROUP_CONCAT(CONCAT(go.option_text, '||', go.is_correct) SEPARATOR '|||') AS options_data
        FROM words w
        JOIN game_options go ON w.id = go.word_id
        WHERE w.difficulty = ? 
          AND go.game_type = 'auditory-codes'
        GROUP BY w.id
        ORDER BY RAND() 
        LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->bind_param("s", $difficulty);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Intentar con dificultad fácil como respaldo
    $stmt->bind_param("s", 'easy');
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        die("No hay datos disponibles para este nivel.");
    }
}

$row = $result->fetch_assoc();

// Procesar opciones
$options = [];
if (!empty($row['options_data'])) {
    $options_raw = explode('|||', $row['options_data']);
    foreach ($options_raw as $opt) {
        $parts = explode('||', $opt);
        if (count($parts) >= 2) {
            $options[] = [
                'text' => $parts[0],
                'correct' => (bool)$parts[1]
            ];
        }
    }
}

// Mezclar opciones
shuffle($options);

$game_data = [
    'word' => $row['word'],
    'audio' => get_audio('auditory', $row['audio_path']),
    'options' => $options,
    'level' => $level,
    'current_score' => $current_score,
    'words_completed' => $words_completed,
    'words_per_level' => $words_per_level
];

$content = ''; // Se generará en view.php
include 'view.php';