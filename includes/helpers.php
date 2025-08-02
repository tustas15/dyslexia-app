<?php
function get_asset($type, $path)
{
    return ASSETS_PATH . "/$type/$path";
}

function get_audio($game, $file) {
    $game_path = "{$_SERVER['DOCUMENT_ROOT']}/dyslexia-app/assets/audios/$game/$file";
    $default_path = "{$_SERVER['DOCUMENT_ROOT']}/dyslexia-app/assets/audios/default.mp3";
    
    // Verificar si existe el archivo específico
    if (file_exists($game_path)) {
        return BASE_URL . "/assets/audios/$game/$file";
    }
    // Verificar si existe el respaldo
    elseif (file_exists($default_path)) {
        return BASE_URL . "/assets/audios/default.mp3";
    }
    // Retornar cadena vacía si no hay ningún audio
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

function get_story_image($title) {
    // Palabras clave optimizadas para ilustraciones infantiles
    $keywords = urlencode($title . ' children illustration');
    
    // Usar la API de Unsplash con tu key
    $access_key = 'aLWVSlw4IrYuL_x7Pkr3OodCkNwORHTUmj9-RigOI28';
    $url = "https://api.unsplash.com/search/photos?page=1&query=$keywords&per_page=1&client_id=$access_key";
    
    try {
        $context = stream_context_create([
            'http' => ['timeout' => 2] // Timeout de 2 segundos
        ]);
        
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        
        if (!empty($data['results'][0]['urls']['regular'])) {
            return $data['results'][0]['urls']['regular'];
        }
    } catch (Exception $e) {
        // Fallback silencioso
    }
    
    // Fallback a servicio público
    return "https://source.unsplash.com/featured/800x600/?$keywords";
}

function get_word_image($word) {
    // Palabras clave optimizadas para niños
    $keywords = urlencode($word . ' children');
    
    // Usar API para palabras si es necesario
    $access_key = 'aLWVSlw4IrYuL_x7Pkr3OodCkNwORHTUmj9-RigOI28';
    $url = "https://api.unsplash.com/search/photos?page=1&query=$keywords&per_page=1&client_id=$access_key";
    
    try {
        $context = stream_context_create([
            'http' => ['timeout' => 1] // Timeout más corto
        ]);
        
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        
        if (!empty($data['results'][0]['urls']['thumb'])) {
            return $data['results'][0]['urls']['thumb'];
        }
    } catch (Exception $e) {
        // Fallback silencioso
    }
    
    // Fallback a servicio público
    return "https://source.unsplash.com/featured/200x200/?$keywords";
}

// Función para verificar soporte TTS (versión PHP)
function tts_supported() {
    // En PHP no podemos verificar directamente el soporte del navegador
    // Asumimos soporte y manejamos la compatibilidad en el cliente
    return true;
}

// Función para obtener idioma del usuario
function get_user_language() {
    $lang = 'es-ES'; // Default
    
    if (isset($_SESSION['language'])) {
        return $_SESSION['language'];
    }
    
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5);
    }
    
    return $lang;
}