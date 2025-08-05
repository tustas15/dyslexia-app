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
$difficulty_map = [1 => 'easy', 2 => 'medium', 3 => 'hard'];
$difficulty = $difficulty_map[$level] ?? 'easy';

// Obtener palabras para el nivel actual usando la nueva tabla
$sql = "SELECT wrd.id, wrd.correct_word, wrd.incorrect_word, 
               w.audio_path, w.image_path 
        FROM word_robot_data wrd
        JOIN words w ON wrd.word_id = w.id
        WHERE wrd.difficulty = ?
        ORDER BY RAND() LIMIT 3";

$stmt = $db->prepare($sql);
if (!$stmt) {
    die("Error en la preparación: " . $db->error);
}

// Variable temporal para bind_param
$temp_difficulty = $difficulty;
$stmt->bind_param("s", $temp_difficulty);
$stmt->execute();
$result = $stmt->get_result();

$words = [];
while ($row = $result->fetch_assoc()) {
    $words[] = [
        'correct_word' => $row['correct_word'],
        'incorrect_word' => $row['incorrect_word'],
        'audio' => get_audio('robot', $row['audio_path']),
        'image' => get_word_image($row['correct_word']) // Usar API para imagen
    ];
}

if (empty($words)) {
    // Fallback a dificultad fácil
    $stmt->bind_param("s", 'easy');
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $words[] = [
            'correct_word' => $row['correct_word'],
            'incorrect_word' => $row['incorrect_word'],
            'audio' => get_audio('robot', $row['audio_path']),
            'image' => get_word_image($row['correct_word']) // Usar API para imagen
        ];
    }

    if (empty($words)) {
        die("No hay datos disponibles para este nivel.");
    }
}

// Inicializar sesión de progreso si no existe
if (!isset($_SESSION['robot_progress'])) {
    $_SESSION['robot_progress'] = [
        'level' => $level,
        'current_word' => 0,
        'score' => 0,
        'words' => $words
    ];
}

// Manejar reinicio de progreso si cambia el nivel
if ($_SESSION['robot_progress']['level'] != $level) {
    $_SESSION['robot_progress'] = [
        'level' => $level,
        'current_word' => 0,
        'score' => 0,
        'words' => $words
    ];
}

$current_index = $_SESSION['robot_progress']['current_word'];
$current_word = $_SESSION['robot_progress']['words'][$current_index] ?? null;

// Redirigir si se completó el nivel
if ($current_index >= count($words)) {
    header("Location: level_complete.php?level=$level");
    exit;
}

$content = ''; // Se generará en view.php
include 'view.php';
