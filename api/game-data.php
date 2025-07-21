<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';

header('Content-Type: application/json');

safe_session_start();

if (!is_logged_in()) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$game_type = $_GET['game'] ?? '';
$level = $_GET['level'] ?? 1;

if (empty($game_type)) {
    echo json_encode(['error' => 'Juego no especificado']);
    exit;
}

$game_data = load_game_data($game_type, $level);

if (!$game_data) {
    echo json_encode(['error' => 'Datos no disponibles']);
    exit;
}

// Procesar según el tipo de juego
switch ($game_type) {
    case 'auditory-codes':
        $options = json_decode($game_data['options'], true);
        shuffle($options);
        $response = [
            'word' => $game_data['word'],
            'audio' => get_audio('auditory', $game_data['audio_path']),
            'options' => $options
        ];
        break;
        
    case 'syllable-hunt':
        $syllables = explode('-', $game_data['syllables']);
        shuffle($syllables);
        $response = [
            'word' => $game_data['word'],
            'syllables' => $syllables,
            'image' => get_image('games', $game_data['image_path'])
        ];
        break;
        
    // ... otros juegos
        
    default:
        $response = ['error' => 'Juego no soportado'];
}

echo json_encode($response);