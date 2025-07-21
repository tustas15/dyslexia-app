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

// Obtener pares de letras para el nivel
$sql = "SELECT id, letter1, letter2, correct_letter 
        FROM letter_pairs 
        WHERE difficulty = ? 
        ORDER BY RAND() LIMIT 5";

$stmt = $db->prepare($sql);
$stmt->bind_param("s", $level);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No hay datos disponibles para este nivel.");
}

$pairs = [];
while ($row = $result->fetch_assoc()) {
    $pairs[] = [
        'pair' => [$row['letter1'], $row['letter2']],
        'correct' => $row['correct_letter']
    ];
}

$game_data = [
    'pairs' => $pairs
];

include 'view.php';