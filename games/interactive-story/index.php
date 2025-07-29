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
$sql = "SELECT s.id, s.title, s.template, s.options 
        FROM stories s
        WHERE s.difficulty = ?
        ORDER BY RAND() LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->bind_param("s", $difficulty);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Intentar con cualquier historia como respaldo
    $stmt = $db->prepare("SELECT s.id, s.title, s.template, s.options 
                        FROM stories s
                          ORDER BY RAND() LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("No hay historias disponibles.");
    }
}

// Después de obtener la historia
$story = $result->fetch_assoc();

// Obtener imagen desde internet
$story_image = get_story_image($story['title']);

// Identificar marcadores de posición
preg_match_all('/\{([^}]+)\}/', $story['template'], $matches);
$placeholders = $matches[1] ?? [];

// Decodificar opciones
$options = json_decode($story['options'], true);

// Determinar categorías
if (isset($options['categories'])) {
    $categories = $options['categories'];
} else {
    $categories = array_keys($options);
}

// Filtrar categorías presentes en el template
$categories = array_intersect($categories, $placeholders);

// Obtener elementos directamente desde JSON
$elements = [];
foreach ($categories as $category) {
    if (isset($options[$category])) {
        foreach ($options[$category] as $word) {
            $elements[$category][] = [
                'word' => $word,
                'image' => get_word_image($word) // Imagen para cada palabra
            ];
        }
    }
}

$game_data = [
    'id' => $story['id'],
    'title' => $story['title'],
    'template' => $story['template'],
    'image' => $story_image,
    'placeholders' => $placeholders,
    'categories' => $categories,
    'elements' => $elements,
    'level' => $level,
    'completed_count' => $completed_count
];


$content = '';
include 'view.php';
