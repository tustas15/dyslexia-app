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

$user_id = $_SESSION['user_id'];
$level = $_GET['level'] ?? 1;

// Determinar dificultad según nivel
$difficulty = 'easy';
if ($level == 2) $difficulty = 'medium';
elseif ($level >= 3) $difficulty = 'hard';

// Obtener progreso actual del nivel
$stmt_progress = $db->prepare("SELECT SUM(score) AS level_score 
                     FROM user_progress 
                     WHERE user_id = ? 
                     AND game_type = 'auditory-codes' 
                     AND JSON_UNQUOTE(JSON_EXTRACT(details, '$.level')) = ?");
$stmt_progress->bind_param("ii", $user_id, $level);
$stmt_progress->execute();
$progress = $stmt_progress->get_result()->fetch_assoc();
$current_score = $progress['level_score'] ?? 0;
$stmt_progress->close();

// Calcular palabras completadas en este nivel
$words_per_level = 10;
$words_completed = floor($current_score / 10);

// Verificar si el nivel está completo
if ($words_completed >= $words_per_level) {
    header("Location: level_complete.php?level=$level");
    exit;
}

// Consulta optimizada y corregida
$sql = "SELECT w.id, w.word, 
        GROUP_CONCAT(CONCAT(go.option_text, '||', go.is_correct) SEPARATOR '|||') AS options_data
        FROM words w
        JOIN game_options go ON w.id = go.word_id
        WHERE w.difficulty = ? 
          AND go.game_type = 'auditory-codes'
          AND go.difficulty = ?
        GROUP BY w.id
        ORDER BY RAND() 
        LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->bind_param("ss", $difficulty, $difficulty);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Intentar con dificultad fácil como respaldo
    $fallback_sql = "SELECT w.id, w.word, 
                     GROUP_CONCAT(CONCAT(go.option_text, '||', go.is_correct) SEPARATOR '|||') AS options_data
                     FROM words w
                     JOIN game_options go ON w.id = go.word_id
                     WHERE w.difficulty = 'easy' 
                       AND go.game_type = 'auditory-codes'
                       AND go.difficulty = 'easy'
                     GROUP BY w.id
                     ORDER BY RAND() 
                     LIMIT 1";

    $fallback_stmt = $db->prepare($fallback_sql);
    $fallback_stmt->execute();
    $result = $fallback_stmt->get_result();
    
    if ($result->num_rows === 0) {
        die("No hay datos disponibles para este nivel.");
    }
    $row = $result->fetch_assoc();
    $fallback_stmt->close();
} else {
    $row = $result->fetch_assoc();
}
$stmt->close();

// Depuración
error_log("Nivel: $level, Dificultad: $difficulty");
error_log("Palabra seleccionada: " . $row['word']);

// Procesar opciones
$options = [];
$correct_option_exists = false;

if (!empty($row['options_data'])) {
    $options_raw = explode('|||', $row['options_data']);
    foreach ($options_raw as $opt) {
        $parts = explode('||', $opt);
        if (count($parts) >= 2) {
            $option_text = trim($parts[0]);
            $is_correct = (bool)$parts[1];
            
            $options[] = [
                'text' => $option_text,
                'correct' => $is_correct
            ];
            
            if ($is_correct && $option_text === $row['word']) {
                $correct_option_exists = true;
            }
        }
    }
}

// Si no existe opción correcta o no coincide con la palabra, reconstruir opciones
if (!$correct_option_exists) {
    // Reconstruir opciones correctamente
    $clean_options = [];
    $distractors = ['casa', 'perro', 'gato', 'sol', 'flor', 'mesa', 'silla', 'libro', 'ventana'];
    shuffle($distractors);
    
    // Agregar opción correcta
    $clean_options[] = [
        'text' => $row['word'],
        'correct' => true
    ];
    
    // Agregar opciones incorrectas
    $count = 0;
    while (count($clean_options) < 3 && $count < count($distractors)) {
        $opt_text = $distractors[$count];
        
        // Evitar duplicados y palabra correcta
        if ($opt_text !== $row['word']) {
            $clean_options[] = [
                'text' => $opt_text,
                'correct' => false
            ];
        }
        $count++;
    }
    
    $options = $clean_options;
}

// Mezclar opciones
shuffle($options);

$game_data = [
    'word' => $row['word'],
    'options' => $options,
    'level' => $level,
    'current_score' => $current_score,
    'words_completed' => $words_completed,
    'words_per_level' => $words_per_level
];

error_log("Opciones finales: " . print_r($options, true));

$content = '';
include 'view.php';
?>