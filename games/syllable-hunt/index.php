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

// Convertir nivel numérico a dificultad textual
$difficulty_map = [
    1 => 'easy',
    2 => 'medium',
    3 => 'hard'
];
$difficulty = $difficulty_map[$level] ?? 'easy';

$sql = "SELECT id, word, syllables, image_path, audio_path 
        FROM words 
        WHERE difficulty = ? 
        ORDER BY RAND() LIMIT 1";

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

// Separar las sílabas y desordenarlas
$syllables = explode('-', $row['syllables']);
$correct_syllables = $syllables; // Guardar el orden correcto
shuffle($syllables);

$game_data = [
    'word' => $row['word'],
    'syllables' => $syllables,
    'correct_syllables' => $correct_syllables,
    'image' => get_image('games', $row['image_path']),
    'audio' => get_audio('syllables', $row['audio_path']),
    'level' => $level
];

$content = ''; // Se generará en view.php
include 'view.php';