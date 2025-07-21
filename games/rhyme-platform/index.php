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

// Consulta corregida - Usando JOIN en lugar de subconsultas con word_id
$sql = "SELECT w.word AS target_word, 
        GROUP_CONCAT(r.rhyme_word SEPARATOR '||') AS rhymes,
        GROUP_CONCAT(nr.word SEPARATOR '||') AS non_rhymes
        FROM words w
        LEFT JOIN rhymes r ON w.word = r.word AND r.difficulty = ?
        LEFT JOIN (
            SELECT word FROM words 
            WHERE difficulty = ? 
            AND word NOT IN (SELECT rhyme_word FROM rhymes WHERE difficulty = ?)
            LIMIT 5
        ) nr ON 1=1
        WHERE w.difficulty = ?
        GROUP BY w.id
        ORDER BY RAND() LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->bind_param("ssss", $level, $level, $level, $level);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No hay datos disponibles para este nivel.");
}

$row = $result->fetch_assoc();

// Manejar posibles valores nulos
$rhymes = !empty($row['rhymes']) ? explode('||', $row['rhymes']) : [];
$non_rhymes = !empty($row['non_rhymes']) ? explode('||', $row['non_rhymes']) : [];

$all_words = array_merge($rhymes, $non_rhymes);
shuffle($all_words);

$game_data = [
    'target_word' => $row['target_word'],
    'words' => $all_words,
    'rhymes' => $rhymes
];

include 'view.php';