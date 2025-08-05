<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';

safe_session_start();

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'error' => 'No autenticado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
    exit;
}

$user_id = $_SESSION['user_id'];
$game_type = $data['game'] ?? '';
$level = $data['level'] ?? 1;

if (empty($game_type)) {
    echo json_encode(['success' => false, 'error' => 'Tipo de juego no especificado']);
    exit;
}

// Calcular puntuación para Detective de Letras
if ($game_type === 'letter-detective') {
    if (isset($data['level_completed'])) {
        // Guardar cuando se completa un nivel
        $score = $data['score'];
        $details = json_encode([
            'level' => $data['level'],
            'correct_answers' => $data['correct_answers'],
            'total_pairs' => $data['total_pairs'],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } else {
        // Guardar progreso por cada respuesta
        $score = $data['correct'] ? 2 : 0;
        $details = json_encode([
            'level' => $level,
            'correct' => $data['correct'],
            'selected' => $data['selected'] ?? '',
            'correct_letter' => $data['correctLetter'] ?? '',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
} else if ($game_type === 'auditory-codes') {
    $score = $data['correct'] ? 10 : 0;
    $details = json_encode([
        'level' => $level,
        'correct' => $data['correct'],
        'word' => $data['word'] ?? '',
        'selected' => $data['selected'] ?? '',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} else if ($game_type === 'interactive-story') {
    $score = $data['completed'] ? 50 : 0; // 50 puntos por historia completada
    $details = json_encode([
        'level' => $level,
        'story_id' => $data['story_id'] ?? 0,
        'completed' => $data['completed'] ?? false,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} else if ($game_type === 'syllable-hunt') {
    $score = $data['correct'] ? 10 : 0;
    $details = json_encode([
        'level' => $level,
        'correct' => $data['correct'],
        'word' => $data['word'] ?? '',
        'timestamp' => date('Y-m-d H:i:s')
    ]);

    // Actualizar progreso en sesión
    if ($data['correct'] && isset($_SESSION['syllable_progress'])) {
        $_SESSION['syllable_progress']['words_completed']++;
    }
} else if ($game_type === 'word-painting') {
    // Lógica específica para Pintando Palabras
    $score = $data['correct'] ? 15 : 0;
    $details = json_encode([
        'level' => $level,
        'correct' => $data['correct'],
        'word' => $data['word'] ?? '',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} else if ($game_type === 'word-robot') {
    $score = $data['correct'] ? 15 : 5; // 15 puntos por acierto, 5 por intento
    $details = json_encode([
        'level' => $level,
        'correct' => $data['correct'],
        'word' => $data['word'] ?? '',
        'attempts' => $data['attempts'] ?? 0,
        'timestamp' => date('Y-m-d H:i:s')
    ]);

    // Actualizar progreso en sesión
    if ($data['correct'] && isset($_SESSION['robot_progress'])) {
        $_SESSION['robot_progress']['current_word']++;
        $_SESSION['robot_progress']['score'] += $score;
    }
} else {
    // Lógica para otros juegos
    $score = $data['correct'] ? 10 : 0;
    $details = json_encode($data);
}


// Guardar en la base de datos
$stmt = $db->prepare("INSERT INTO user_progress 
                    (user_id, game_type, score, details) 
                    VALUES (?, ?, ?, ?)");
$stmt->bind_param("isis", $user_id, $game_type, $score, $details);

if ($stmt->execute()) {
    // Actualizar la sesión para el progreso en tiempo real
    $session_key = 'auditory_codes_level_' . $level . '_score';
    $_SESSION[$session_key] = ($_SESSION[$session_key] ?? 0) + $score;

    echo json_encode(['success' => true, 'message' => 'Progreso guardado']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al guardar: ' . $db->error]);
}
