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

$level = $_GET['level'] ?? 1;

// Inicializar progreso de nivel si no existe
if (!isset($_SESSION['syllable_progress'])) {
    $_SESSION['syllable_progress'] = [
        'current_level' => $level,
        'words_completed' => 0,
        'total_words' => 3
    ];
}

// Manejar actualización de progreso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_progress') {
        $_SESSION['syllable_progress']['words_completed']++;

        // Verificar si se completó el nivel
        if ($_SESSION['syllable_progress']['words_completed'] >= 3) {
            // Guardar progreso en la base de datos
            $score = 30; // 10 puntos por palabra * 3 palabras
            $details = json_encode([
                'level' => $level,
                'completed' => true,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

            $stmt = $db->prepare("INSERT INTO user_progress 
                                (user_id, game_type, score, details) 
                                VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isis", $_SESSION['user_id'], 'syllable-hunt', $score, $details);
            $stmt->execute();

            // Redirigir a pantalla de nivel completado
            header('Content-Type: application/json');
            echo json_encode([
                'completed' => true,
                'redirect' => "level_complete.php?level=$level"
            ]);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'completed' => false,
            'words_completed' => $_SESSION['syllable_progress']['words_completed']
        ]);
        exit;
    }
}

// Si cambia de nivel, reiniciar progreso
if ($_SESSION['syllable_progress']['current_level'] != $level) {
    $_SESSION['syllable_progress'] = [
        'current_level' => $level,
        'words_completed' => 0,
        'total_words' => 3
    ];
}

// Verificar si el nivel está completo
if ($_SESSION['syllable_progress']['words_completed'] >= 3) {
    header("Location: level_complete.php?level=$level");
    exit;
}

// Mapa de niveles a dificultad
$difficulty_map = [
    1 => 'easy',
    2 => 'medium',
    3 => 'hard'
];
$difficulty = $difficulty_map[$level] ?? 'easy';

// Determinar número de sílabas según nivel
$syllable_count = 2;
if ($level == 2) $syllable_count = 3;
if ($level == 3) $syllable_count = 4;

// Consulta para palabras con el número de sílabas requerido
$sql = "SELECT id, word, syllables, image_path, audio_path 
        FROM words 
        WHERE difficulty = ? 
        AND LENGTH(syllables) - LENGTH(REPLACE(syllables, '-', '')) + 1 = ?
        ORDER BY RAND() LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->bind_param("si", $difficulty, $syllable_count);
$stmt->execute();
$result = $stmt->get_result();

// Fallback si no hay palabras
if ($result->num_rows === 0) {
    $stmt->bind_param("si", 'easy', $syllable_count);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("No hay datos disponibles para este nivel.");
    }
}

$row = $result->fetch_assoc();

// Procesar sílabas
$syllables = explode('-', $row['syllables']);
$correct_syllables = $syllables;
shuffle($syllables);

$game_data = [
    'word' => $row['word'],
    'syllables' => $syllables,
    'correct_syllables' => $correct_syllables,
    'image' => get_image('games', $row['image_path']),
    'audio' => get_audio('syllables', $row['audio_path']),
    'level' => $level
];

include 'view.php';
