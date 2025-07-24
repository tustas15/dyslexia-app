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
$difficulty_map = [
    1 => 'easy',
    2 => 'medium',
    3 => 'hard'
];
$difficulty = $difficulty_map[$level] ?? 'easy';

// Verificar progreso del nivel
$stmt = $db->prepare("SELECT COUNT(*) AS completed_count 
                     FROM user_progress 
                     WHERE user_id = ? 
                     AND game_type = 'interactive-story'
                     AND JSON_EXTRACT(details, '$.level') = ?");
$stmt->bind_param("ii", $user_id, $level);
$stmt->execute();
$progress = $stmt->get_result()->fetch_assoc();
$completed_count = $progress['completed_count'] ?? 0;

// Comprobar si el nivel está completo (3 historias por nivel)
if ($completed_count >= 3) {
    header("Location: level_complete.php?level=$level");
    exit;
}

// Obtener una historia aleatoria de la dificultad actual
$sql = "SELECT s.id, s.title, s.template, s.image_path, s.options 
        FROM stories s
        WHERE s.difficulty = ?
        ORDER BY RAND() LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->bind_param("s", $difficulty);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Intentar con cualquier historia como respaldo
    $stmt = $db->prepare("SELECT s.id, s.title, s.template, s.image_path, s.options 
                          FROM stories s
                          ORDER BY RAND() LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        die("No hay historias disponibles.");
    }
}

$story = $result->fetch_assoc();
$options = json_decode($story['options'], true);
$categories = $options['categories'] ?? [];

// Obtener elementos para la historia
$elements = [];
if (!empty($categories)) {
    $placeholders = implode(',', array_fill(0, count($categories), '?'));
    $sql = "SELECT * FROM story_elements 
            WHERE story_id = ? AND category IN ($placeholders)";
    
    $stmt = $db->prepare($sql);
    $types = str_repeat('s', count($categories) + 1);
    $params = array_merge([$story['id']], $categories);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $elements_result = $stmt->get_result();
    
    while ($row = $elements_result->fetch_assoc()) {
        $elements[$row['category']][] = [
            'word' => $row['word'],
            'image' => get_image('stories', $row['image_path']),
            'audio' => get_audio('stories', $row['audio_path'])
        ];
    }
}

// Identificar marcadores de posición en el template
preg_match_all('/\{([^}]+)\}/', $story['template'], $matches);
$placeholders = $matches[1] ?? [];

$game_data = [
    'id' => $story['id'],
    'title' => $story['title'],
    'template' => $story['template'],
    'image' => get_image('stories', $story['image_path']),
    'placeholders' => $placeholders,
    'categories' => $categories,
    'elements' => $elements,
    'level' => $level,
    'completed_count' => $completed_count
];

$content = '';
include 'view.php';