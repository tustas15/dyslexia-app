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

$sql = "SELECT id, word, audio_path, syllables 
        FROM words 
        WHERE difficulty = ? 
        ORDER BY RAND() LIMIT 1";

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

$row = $result->fetch_assoc();

// Generar letras de la palabra mezcladas con letras aleatorias
$word = $row['word'];
$word_letters = str_split($word);
$all_letters = array_merge($word_letters, generate_random_letters(10, $word_letters));
shuffle($all_letters);

// Determinar colores por sílaba
$syllables = explode('-', $row['syllables']);
$syllable_colors = [];
$colors = ['red', 'blue', 'green', 'yellow', 'orange', 'purple'];
foreach ($syllables as $index => $syllable) {
    $syllable_colors[$syllable] = $colors[$index % count($colors)];
}

// Asignar colores a las letras
$letter_data = [];
foreach ($all_letters as $letter) {
    $color = '';
    foreach ($syllables as $syllable) {
        if (strpos($syllable, $letter) !== false) {
            $color = $syllable_colors[$syllable];
            break;
        }
    }
    
    $letter_data[] = [
        'char' => $letter,
        'color' => $color,
        'is_target' => in_array($letter, $word_letters)
    ];
}

$game_data = [
    'word' => $word,
    'syllables' => $syllables,
    'audio' => get_audio('painting', $row['audio_path']),
    'letters' => $letter_data,
    'level' => $level
];

include 'view.php';

// Función para generar letras aleatorias que no estén en la palabra
function generate_random_letters($count, $exclude_letters) {
    $letters = range('a', 'z');
    $random_letters = [];
    
    // Eliminar letras de la palabra y duplicados
    $exclude_letters = array_unique($exclude_letters);
    $available_letters = array_diff($letters, $exclude_letters);
    
    // Si no hay suficientes letras, repetir algunas
    if (count($available_letters) < $count) {
        $available_letters = array_merge($available_letters, $available_letters);
    }
    
    for ($i = 0; $i < $count; $i++) {
        $random_letters[] = $available_letters[array_rand($available_letters)];
    }
    
    return $random_letters;
}