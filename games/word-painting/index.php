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
$game_data = get_word_painting_data($level);

if (!$game_data) {
    die("No hay datos disponibles para este nivel.");
}

$word = $game_data['word'];
$syllables = $game_data['syllables'];
$syllable_colors = $game_data['syllable_colors'];
$level = $game_data['level'];

// Paleta de colores por defecto
$default_colors = ['#FF6B6B', '#4D96FF', '#6BC777', '#FFD93D', '#FF9C6B', '#9B5DE5'];

function hex2rgb($hex)
{
    $hex = str_replace('#', '', $hex);

    if (strlen($hex) == 3) {
        $r = hexdec($hex[0] . $hex[0]);
        $g = hexdec($hex[1] . $hex[1]);
        $b = hexdec($hex[2] . $hex[2]);
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    return "rgb($r,$g,$b)";
}

// Crear mapa de sílabas para la palabra
$syllableMap = [];
$currentPosition = 0;
foreach ($syllables as $index => $syllable) {
    $length = mb_strlen($syllable, 'UTF-8');
    for ($i = 0; $i < $length; $i++) {
        $syllableMap[$currentPosition] = $index;
        $currentPosition++;
    }
}

// Crear array de letras objetivo con su índice de sílaba
$target_letters_with_syllable = [];
$word_letters = preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY);
for ($i = 0; $i < count($word_letters); $i++) {
    $target_letters_with_syllable[] = [
        'char' => $word_letters[$i],
        'syllable_index' => $syllableMap[$i] ?? 0,
        'position' => $i // Identificador único de posición
    ];
}

// Generar letras distractoras
$distractor_count = 10 + ($level * 3);
$distractors = [];

$alphabet = range('a', 'z');
$available_letters = array_diff($alphabet, $word_letters);

for ($i = 0; $i < $distractor_count; $i++) {
    if (empty($available_letters)) {
        $available_letters = $alphabet;
    }
    $distractors[] = $available_letters[array_rand($available_letters)];
}

// Crear array de todas las letras
$all_letters = [];
foreach ($target_letters_with_syllable as $letter_info) {
    $all_letters[] = [
        'char' => $letter_info['char'],
        'is_target' => true,
        'syllable_index' => $letter_info['syllable_index']
    ];
}
foreach ($distractors as $letter) {
    $all_letters[] = [
        'char' => $letter,
        'is_target' => false,
        'syllable_index' => null
    ];
}
shuffle($all_letters);

// Preparar datos de letras para la vista
$letter_data = [];
foreach ($all_letters as $item) {
    $color = '';
    $is_target = $item['is_target'];

    if ($is_target) {
        $syllable_index = $item['syllable_index'];
        $color_hex = $syllable_colors[$syllable_index] ?? ($default_colors[$syllable_index % count($default_colors)] ?? '#CCCCCC');
        $color = hex2rgb($color_hex);
    }

    $letter_data[] = [
        'char' => $item['char'],
        'color' => $color,
        'is_target' => $is_target,
        'position' => $item['position'] ?? null // Nuevo campo
    ];
}

// Pasar datos a la vista
$game_data['letters'] = $letter_data;
include 'view.php';
