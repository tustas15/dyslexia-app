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

// Obtener una palabra con un error común
$sql = "SELECT w.id, w.word, go.option_text AS incorrect_word 
        FROM words w
        JOIN game_options go ON w.id = go.word_id 
        WHERE go.game_type = 'word-robot' AND w.difficulty = ?
        ORDER BY RAND() LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->bind_param("s", $level);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No hay datos disponibles para este nivel.");
}

$row = $result->fetch_assoc();

$game_data = [
    'correct_word' => $row['word'],
    'incorrect_word' => $row['incorrect_word']
];

include 'view.php';