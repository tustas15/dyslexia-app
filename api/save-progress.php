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
    if (isset($data['final_score'])) {
        // Guardar puntuación final
        $score = $data['final_score'];
        $details = json_encode([
            'level' => $level,
            'score' => $score,
            'correct_answers' => $data['correct_answers'] ?? 0,
            'total_pairs' => $data['total_pairs'] ?? 0,
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
    echo json_encode(['success' => true, 'message' => 'Progreso guardado']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al guardar: ' . $db->error]);
}