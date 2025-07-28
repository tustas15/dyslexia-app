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

$user_id = $_SESSION['user_id'];
$level = $_GET['level'] ?? 1;
$difficulty = match($level) {
    1 => 'easy',
    2 => 'medium',
    3 => 'hard'
};

// Obtener pares según nivel
$pairsCount = match($level) {
    1 => 5,
    2 => 8,
    3 => 10
};

$stmt = $db->prepare("SELECT * FROM letter_pairs 
                     WHERE difficulty = ? 
                     ORDER BY RAND() 
                     LIMIT ?");
$stmt->bind_param("si", $difficulty, $pairsCount);
$stmt->execute();
$letter_pairs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$game_data = [
    'level' => $level,
    'pairs' => $letter_pairs,
    'current_pair' => 0,
    'score' => 0
];

// Determinar dificultad según nivel
$difficulty = 'easy';
if ($level == 2) $difficulty = 'medium';
elseif ($level >= 3) $difficulty = 'hard';

// Obtener datos del juego
$stmt = $db->prepare("SELECT * FROM letter_pairs WHERE difficulty = ? ORDER BY RAND() LIMIT 10");
$stmt->bind_param("s", $difficulty);
$stmt->execute();
$letter_pairs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$game_data = [
    'level' => $level,
    'pairs' => $letter_pairs,
    'lives' => 3,
    'score' => 0,
    'current_pair' => 0
];

$content = ''; // Se generará en view.php
include 'view.php';