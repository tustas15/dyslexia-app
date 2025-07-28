<?php
// index.php
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

// Identificar marcadores de posición en el template PRIMERO
preg_match_all('/\{([^}]+)\}/', $story['template'], $matches);
$placeholders = $matches[1] ?? [];

// Decodificar opciones
$options = json_decode($story['options'], true);

// Determinar categorías según el formato
if (isset($options['categories'])) {
    // Formato nuevo: usar la lista de categorías
    $categories = $options['categories'];
} else {
    // Formato antiguo: usar las claves del JSON
    $categories = array_keys($options);
}

// Filtrar categorías que están en el template
$categories = array_intersect($categories, $placeholders);

// Obtener elementos para la historia
$elements = [];

// Obtener elementos solo si existen categorías válidas
if (!empty($categories)) {
    $placeholders_str = implode(',', array_fill(0, count($categories), '?'));
    $sql = "SELECT * FROM story_elements 
            WHERE story_id = ? AND category IN ($placeholders_str)";
    
    $stmt = $db->prepare($sql);
    $types = str_repeat('s', count($categories) + 1);
    $params = array_merge([$story['id']], $categories);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $elements_result = $stmt->get_result();
    
    while ($row = $elements_result->fetch_assoc()) {
        $elements[$row['category']][] = [
            'word' => $row['word'],
            'image_path' => $row['image_path'] // Corregido para usar image_path
        ];
    }
}

// Si no hay elementos, usar palabras del JSON (formato antiguo)
if (empty($elements) && is_array($options)) {
    foreach ($categories as $category) {
        if (isset($options[$category])) {
            foreach ($options[$category] as $word) {
                $elements[$category][] = [
                    'word' => $word,
                    'image_path' => null
                ];
            }
        }
    }
}

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