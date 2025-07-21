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

$sql = "SELECT id, word, syllables, image_path 
        FROM words 
        WHERE difficulty = ? 
        ORDER BY RAND() LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->bind_param("s", $level);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No hay datos disponibles para este nivel.");
}

$row = $result->fetch_assoc();

// Separar las sílabas y desordenarlas
$syllables = explode('-', $row['syllables']);
shuffle($syllables);

$game_data = [
    'word' => $row['word'],
    'syllables' => $syllables,
    'image' => get_image('games', $row['image_path'])
];

include 'view.php';