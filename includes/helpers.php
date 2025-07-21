<?php
function get_asset($type, $path)
{
    return ASSETS_PATH . "/$type/$path";
}

function get_audio($game, $file)
{
    // Verificar si el archivo existe
    $audio_path = $_SERVER['DOCUMENT_ROOT'] . "/dyslexia-app/assets/audios/$game/$file";

    if (!file_exists($audio_path)) {
        // Usar un archivo de respaldo si no existe
        return BASE_URL . "/assets/audios/default.mp3";
    }

    return BASE_URL . "/assets/audios/$game/$file";
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
