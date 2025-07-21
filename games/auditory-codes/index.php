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
    'level' => $level
];

include 'view.php';