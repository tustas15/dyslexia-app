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
            $score = 30;
            $details = json_encode([
                'level' => $level,
                'completed' => true,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

            // Crear variables para pasar por referencia
            $user_id = $_SESSION['user_id'];
            $game_type = 'syllable-hunt';

            $stmt = $db->prepare("INSERT INTO user_progress 
                            (user_id, game_type, score, details) 
                            VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isis", $user_id, $game_type, $score, $details);
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
if ($level == 3) $syllable_count = 3;  // Cambiado de 4 a 3

// Consulta para palabras con el número de sílabas requerido
$sql = "SELECT w.id, w.word, w.image_path, w.audio_path, wp.syllables 
        FROM words w
        JOIN word_painting_data wp ON w.id = wp.word_id
        WHERE wp.difficulty = ? 
        AND LENGTH(wp.syllables) - LENGTH(REPLACE(wp.syllables, '-', '')) + 1 = ?
        ORDER BY RAND() LIMIT 1";

// Primera consulta con dificultad original
$stmt1 = $db->prepare($sql);
$stmt1->bind_param("si", $difficulty, $syllable_count);
$stmt1->execute();
$result = $stmt1->get_result();

// Fallback si no hay palabras: buscar en cualquier dificultad
if ($result->num_rows === 0) {
    $sql_fallback = "SELECT w.id, w.word, w.image_path, w.audio_path, wp.syllables 
                     FROM words w
                     JOIN word_painting_data wp ON w.id = wp.word_id
                     WHERE LENGTH(wp.syllables) - LENGTH(REPLACE(wp.syllables, '-', '')) + 1 = ?
                     ORDER BY RAND() LIMIT 1";
    
    $stmt_fallback = $db->prepare($sql_fallback);
    $stmt_fallback->bind_param("i", $syllable_count);
    $stmt_fallback->execute();
    $result = $stmt_fallback->get_result();

    if ($result->num_rows === 0) {
        // Último recurso: usar cualquier palabra con el número de sílabas
        $sql_last_chance = "SELECT w.id, w.word, w.image_path, w.audio_path, wp.syllables 
                            FROM words w
                            JOIN word_painting_data wp ON w.id = wp.word_id
                            ORDER BY RAND() LIMIT 1";
        
        $stmt_last = $db->prepare($sql_last_chance);
        $stmt_last->execute();
        $result = $stmt_last->get_result();
        
        if ($result->num_rows === 0) {
            die("No hay datos disponibles para este nivel.");
        }
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