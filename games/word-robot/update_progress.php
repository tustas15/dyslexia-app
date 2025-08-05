<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/helpers.php';

safe_session_start();

if (!is_logged_in()) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'error' => 'No autenticado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$is_correct = $data['correct'] ?? false;

if (!isset($_SESSION['robot_progress'])) {
    echo json_encode(['success' => false, 'error' => 'Progreso no inicializado']);
    exit;
}

// Actualizar progreso en sesión
if ($is_correct) {
    $_SESSION['robot_progress']['current_word']++;
    $_SESSION['robot_progress']['score'] += 15;
} else {
    $_SESSION['robot_progress']['score'] += 5;
}

// Guardar en base de datos
$user_id = $_SESSION['user_id'];
$game_type = 'word-robot';
$level = $_SESSION['robot_progress']['level'];
$score = $is_correct ? 15 : 5;
$details = json_encode([
    'correct' => $is_correct,
    'level' => $level,
    'timestamp' => date('Y-m-d H:i:s')
]);

$stmt = $db->prepare("INSERT INTO user_progress 
                    (user_id, game_type, score, details) 
                    VALUES (?, ?, ?, ?)");
$stmt->bind_param("isis", $user_id, $game_type, $score, $details);
$success = $stmt->execute();

echo json_encode(['success' => $success]);