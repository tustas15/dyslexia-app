<?php
function get_asset($type, $path)
{
    return ASSETS_PATH . "/$type/$path";
}

function get_audio($game, $file)
{
    // Añadir extensión .mp3 si no la tiene
    if (pathinfo($file, PATHINFO_EXTENSION) === '') {
        $file .= '.mp3';
    }

    $game_path = "{$_SERVER['DOCUMENT_ROOT']}/dyslexia-app/assets/audios/$game/$file";
    $default_path = "{$_SERVER['DOCUMENT_ROOT']}/dyslexia-app/assets/audios/default.mp3";

    if (file_exists($game_path)) {
        return BASE_URL . "/assets/audios/$game/$file";
    } elseif (file_exists($default_path)) {
        return BASE_URL . "/assets/audios/default.mp3";
    }

    return '';
}

function get_image($category, $file)
{
    return IMAGE_PATH . "/$category/$file";
}

function load_game_data($game_type, $level = 1)
{
    global $db;

    $stmt = $db->prepare("SELECT * FROM game_data 
                         WHERE game_type = ? AND difficulty_level = ? 
                         ORDER BY RAND() LIMIT 1");
    $stmt->bind_param("si", $game_type, $level);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function save_progress($user_id, $game_type, $score, $details)
{
    global $db;

    $details_json = json_encode($details);
    $stmt = $db->prepare("INSERT INTO user_progress 
                         (user_id, game_type, score, details) 
                         VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $user_id, $game_type, $score, $details_json);
    return $stmt->execute();
}

function get_story_image($title)
{
    // Palabras clave optimizadas para ilustraciones infantiles
    $keywords = urlencode($title . ' children illustration');

    // Usar la API de Unsplash con tu key
    $access_key = 'aLWVSlw4IrYuL_x7Pkr3OodCkNwORHTUmj9-RigOI28';
    $url = "https://api.unsplash.com/search/photos?page=1&query=$keywords&per_page=1&client_id=$access_key";

    $options = [
        'http' => [
            'timeout' => 2,
            'header' => "Accept: application/json\r\n"
        ]
    ];

    $context = stream_context_create($options);

    try {
        $response = @file_get_contents($url, false, $context);
        if ($response === FALSE) {
            throw new Exception('Error en la API');
        }

        $data = json_decode($response, true);

        if (!empty($data['results'][0]['urls']['regular'])) {
            return $data['results'][0]['urls']['regular'];
        }
    } catch (Exception $e) {
        error_log("Error en Unsplash API: " . $e->getMessage());
    }

    return "https://source.unsplash.com/featured/800x600/?$keywords";
}

function get_word_image($word) {
    // Limpiar y preparar la palabra para la búsqueda
    $clean_word = urlencode(trim($word) . ' objeto item');
    
    // Usar Unsplash con parámetros para imágenes más específicas
    $access_key = 'aLWVSlw4IrYuL_x7Pkr3OodCkNwORHTUmj9-RigOI28';
    $url = "https://api.unsplash.com/search/photos?page=1&query=$clean_word&per_page=1&orientation=squarish&client_id=$access_key";
    
    $options = [
        'http' => [
            'timeout' => 3,
            'header' => "Accept: application/json\r\n"
        ]
    ];
    
    $context = stream_context_create($options);
    
    try {
        $response = @file_get_contents($url, false, $context);
        if ($response !== false) {
            $data = json_decode($response, true);
            if (!empty($data['results'][0]['urls']['regular'])) {
                return $data['results'][0]['urls']['regular'];
            }
        }
    } catch (Exception $e) {
        error_log("Unsplash API error for word '$word': " . $e->getMessage());
    }
    
    // Fallback a source.unsplash.com (más rápido, menos específico)
    return "https://source.unsplash.com/featured/400x400/?$clean_word";
}

function get_word_image_enhanced($word) {
    $clean_word = strtolower(trim($word));
    
    // Mapeo más extenso de palabras
    $word_mappings = [
        'casa' => 'house',
        'sol' => 'sun',
        'flor' => 'flower',
        'pato' => 'duck',
        'luna' => 'moon',
        'gato' => 'cat',
        'mesa' => 'table',
        'perro' => 'dog',
        'libro' => 'book',
        'silla' => 'chair',
        'ventana' => 'window',
        'elefante' => 'elephant',
        'computadora' => 'computer',
        'paraguas' => 'umbrella',
        'astronauta' => 'astronaut',
        'biblioteca' => 'library',
        'refrigerador' => 'refrigerator'
    ];
    
    $search_term = $word_mappings[$clean_word] ?? $clean_word;
    
    // Intentar múltiples estrategias de búsqueda
    $search_strategies = [
        $search_term . ' children illustration',
        $search_term . ' clipart',
        $search_term . ' object',
        $search_term // término simple como último recurso
    ];
    
    foreach ($search_strategies as $strategy) {
        $keywords = urlencode($strategy);
        $image_url = try_unsplash_api($keywords);
        if ($image_url) {
            return $image_url;
        }
    }
    
    // Fallback final
    return "https://source.unsplash.com/featured/400x400/?$search_term,object";
}

function try_unsplash_api($keywords) {
    $access_key = 'aLWVSlw4IrYuL_x7Pkr3OodCkNwORHTUmj9-RigOI28';
    $url = "https://api.unsplash.com/search/photos?page=1&query=$keywords&per_page=1&orientation=squarish&client_id=$access_key";
    
    $options = [
        'http' => [
            'timeout' => 3,
            'header' => "Accept: application/json\r\n"
        ]
    ];
    
    $context = stream_context_create($options);
    
    try {
        $response = @file_get_contents($url, false, $context);
        if ($response !== false) {
            $data = json_decode($response, true);
            if (!empty($data['results'][0]['urls']['regular'])) {
                return $data['results'][0]['urls']['regular'];
            }
        }
    } catch (Exception $e) {
        error_log("Unsplash API error: " . $e->getMessage());
    }
    
    return null;
}

// Función para verificar soporte TTS (versión PHP)
function tts_supported()
{
    // En PHP no podemos verificar directamente el soporte del navegador
    // Asumimos soporte y manejamos la compatibilidad en el cliente
    return true;
}

// Función para obtener idioma del usuario
function get_user_language()
{
    $lang = 'es-ES'; // Default

    if (isset($_SESSION['language'])) {
        return $_SESSION['language'];
    }

    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5);
    }

    return $lang;
}

function get_word_painting_data($level = 1)
{
    global $db;

    $difficulty_map = [
        1 => 'easy',
        2 => 'medium',
        3 => 'hard'
    ];
    $difficulty = $difficulty_map[$level] ?? 'easy';

    $sql = "SELECT w.id, w.word, w.audio_path, 
                   wp.syllables, wp.syllable_colors
            FROM words w
            JOIN word_painting_data wp ON w.id = wp.word_id
            WHERE wp.difficulty = ? 
            ORDER BY RAND() LIMIT 1";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $difficulty);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Reintentar con dificultad fácil si no hay resultados
        $stmt->bind_param("s", 'easy');
        $stmt->execute();
        $result = $stmt->get_result();
    }

    if ($result->num_rows === 0) {
        return null;
    }

    $row = $result->fetch_assoc();
    $syllables = explode('-', $row['syllables']);
    $syllable_colors = json_decode($row['syllable_colors'], true);

    return [
        'word' => $row['word'],
        'syllables' => $syllables,
        'syllable_colors' => $syllable_colors,
        'audio' => get_audio('painting', $row['audio_path']),
        'level' => $level
    ];
}
