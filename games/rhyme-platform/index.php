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

// Consulta optimizada para obtener palabra objetivo, rimas y no rimas
$sql = "SELECT w.word AS target_word, 
        (SELECT GROUP_CONCAT(rhyme_word SEPARATOR '||') 
         FROM rhymes 
         WHERE word = w.word AND difficulty = ?) AS rhymes,
        (SELECT GROUP_CONCAT(word SEPARATOR '||') 
         FROM words 
         WHERE difficulty = ? 
           AND word != w.word 
           AND word NOT IN (SELECT rhyme_word FROM rhymes WHERE word = w.word)
         ORDER BY RAND() 
         LIMIT 5) AS non_rhymes
        FROM words w
        WHERE w.difficulty = ?
        ORDER BY RAND()
        LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->bind_param("sss", $difficulty, $difficulty, $difficulty);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Intentar con dificultad fácil como respaldo
    $stmt->bind_param("sss", 'easy', 'easy', 'easy');
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        die("No hay datos disponibles para este nivel.");
    }
}

$row = $result->fetch_assoc();

// Procesar rimas y no rimas
$rhymes = !empty($row['rhymes']) ? explode('||', $row['rhymes']) : [];
$non_rhymes = !empty($row['non_rhymes']) ? explode('||', $row['non_rhymes']) : [];

// Si no hay suficientes no rimas, completar con palabras aleatorias
if (count($non_rhymes) < 5) {
    $sql = "SELECT word FROM words 
            WHERE difficulty = ? 
              AND word NOT IN (SELECT rhyme_word FROM rhymes WHERE word = ?)
              AND word != ?
            ORDER BY RAND()
            LIMIT " . (5 - count($non_rhymes));
    $stmt2 = $db->prepare($sql);
    $stmt2->bind_param("sss", $difficulty, $row['target_word'], $row['target_word']);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    while ($row2 = $result2->fetch_assoc()) {
        $non_rhymes[] = $row2['word'];
    }
}

// Combinar y mezclar
$all_words = array_merge($rhymes, $non_rhymes);
shuffle($all_words);

$game_data = [
    'target_word' => $row['target_word'],
    'words' => $all_words,
    'rhymes' => $rhymes,
    'level' => $level
];

include 'view.php';