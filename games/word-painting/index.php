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

$sql = "SELECT id, word, audio_path 
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

// Generar letras de la palabra mezcladas con letras aleatorias
$word = $row['word'];
$word_letters = str_split($word);
$all_letters = array_merge($word_letters, generate_random_letters(10, $word_letters));
shuffle($all_letters);

$game_data = [
    'word' => $word,
    'audio' => get_audio('painting', $row['audio_path']),
    'letters' => $all_letters
];

include 'view.php';

// Función para generar letras aleatorias que no estén en la palabra
function generate_random_letters($count, $exclude_letters) {
    $letters = range('a', 'z');
    $random_letters = [];
    
    // Eliminar letras de la palabra
    $available_letters = array_diff($letters, $exclude_letters);
    
    for ($i = 0; $i < $count; $i++) {
        $random_letters[] = $available_letters[array_rand($available_letters)];
    }
    
    return $random_letters;
}