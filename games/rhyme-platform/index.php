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

// Validar nivel
if ($level < 1 || $level > 3) {
    header('Location: index.php?level=1');
    exit;
}

// Convertir nivel numérico a dificultad textual
$difficulty_map = [
    1 => 'easy',
    2 => 'medium',
    3 => 'hard'
];
$difficulty = $difficulty_map[$level] ?? 'easy';

// Determinar límite de no rimas según nivel
$nonRhymesLimit = 5; // Nivel 1
if ($level == 2) $nonRhymesLimit = 7;
if ($level == 3) $nonRhymesLimit = 10;

// Consulta optimizada para obtener palabra objetivo de rhymes
$sql = "SELECT r.word AS target_word,
        (SELECT GROUP_CONCAT(rhyme_word SEPARATOR '||') 
         FROM rhymes 
         WHERE word = r.word AND difficulty = ?) AS rhymes,
        (SELECT GROUP_CONCAT(word SEPARATOR '||') 
         FROM words 
         WHERE difficulty = ? 
           AND word != r.word 
           AND word NOT IN (SELECT rhyme_word FROM rhymes WHERE word = r.word)
         ORDER BY RAND() 
         LIMIT ?) AS non_rhymes
        FROM rhymes r
        WHERE r.difficulty = ?
        GROUP BY r.word  -- Evitar duplicados
        ORDER BY RAND()
        LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->bind_param("ssis", $difficulty, $difficulty, $nonRhymesLimit, $difficulty);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Respaldos por dificultad
    $backupData = [
        'easy' => [
            'target_word' => 'sol',
            'rhymes' => ['col', 'gol', 'mol'],
            'non_rhymes' => ['pan', 'mesa', 'luz', 'flor', 'pez']
        ],
        'medium' => [
            'target_word' => 'perro',
            'rhymes' => ['hierro', 'cerro', 'terco'],
            'non_rhymes' => ['gato', 'libro', 'casa', 'silla', 'arbol', 'nube', 'cielo']
        ],
        'hard' => [
            'target_word' => 'elefante',
            'rhymes' => ['cantante', 'vigilante', 'pariente'],
            'non_rhymes' => ['computadora', 'ventana', 'paraguas', 'biblioteca', 'astronauta', 'refrigerador', 'helicoptero', 'universidad', 'television', 'automovil']
        ]
    ];
    
    $row = $backupData[$difficulty];
    $rhymes = $row['rhymes'];
    $non_rhymes = $row['non_rhymes'];
} else {
    $row = $result->fetch_assoc();
    
    // Procesar rimas y no rimas
    $rhymes = !empty($row['rhymes']) ? explode('||', $row['rhymes']) : [];
    $non_rhymes = !empty($row['non_rhymes']) ? explode('||', $row['non_rhymes']) : [];
}

// Completar no rimas si es necesario
if (count($non_rhymes) < $nonRhymesLimit) {
    $sql = "SELECT word FROM words 
            WHERE difficulty = ? 
              AND word != ?
              AND word NOT IN (SELECT rhyme_word FROM rhymes WHERE word = ?)
            ORDER BY RAND()
            LIMIT " . ($nonRhymesLimit - count($non_rhymes));
    
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

$content = ''; // Se generará en view.php
include 'view.php';