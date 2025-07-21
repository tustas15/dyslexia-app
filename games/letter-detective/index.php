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

// Obtener pares de letras para el nivel
$sql = "SELECT id, letter1, letter2, correct_letter 
        FROM letter_pairs 
        WHERE difficulty = ? 
        ORDER BY RAND() LIMIT 5";

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

$pairs = [];
while ($row = $result->fetch_assoc()) {
    $pairs[] = [
        'id' => $row['id'],
        'letter1' => $row['letter1'],
        'letter2' => $row['letter2'],
        'correct' => $row['correct_letter']
    ];
}

$game_data = [
    'pairs' => $pairs,
    'level' => $level
];

include 'view.php';