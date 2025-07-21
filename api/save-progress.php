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
$correct = $data['correct'] ?? false;
$level = $data['level'] ?? 1;
$word = $data['word'] ?? '';

if (empty($game_type)) {
    echo json_encode(['success' => false, 'error' => 'Tipo de juego no especificado']);
    exit;
}

// Calcular puntuación
$score = $correct ? 10 : 0;
$details = json_encode([
    'level' => $level,
    'word' => $word,
    'correct' => $correct,
    'timestamp' => date('Y-m-d H:i:s')
]);

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