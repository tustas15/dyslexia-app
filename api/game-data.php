<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';

// Configurar para mostrar errores solo en desarrollo
if (ENVIRONMENT === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

header('Content-Type: application/json');
safe_session_start();

// Verificar autenticación
if (!is_logged_in()) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

// Obtener parámetros
$game_type = $_GET['game'] ?? '';
$level = $_GET['level'] ?? 1;

// Validar juego
if (empty($game_type)) {
    echo json_encode(['error' => 'Juego no especificado']);
    exit;
}

try {
    // Manejar diferentes tipos de juegos
    switch ($game_type) {
        case 'auditory-codes':
            $game_data = load_game_data($game_type, $level);
            if (!$game_data) {
                throw new Exception('Datos no disponibles para auditory-codes');
            }
            
            $options = json_decode($game_data['options'], true);
            shuffle($options);
            $response = [
                'word' => $game_data['word'],
                'audio' => get_audio('auditory', $game_data['audio_path']),
                'options' => $options
            ];
            break;
            
        case 'syllable-hunt':
            $game_data = load_game_data($game_type, $level);
            if (!$game_data) {
                throw new Exception('Datos no disponibles para syllable-hunt');
            }
            
            $syllables = explode('-', $game_data['syllables']);
            shuffle($syllables);
            $response = [
                'word' => $game_data['word'],
                'syllables' => $syllables,
                'image' => get_image('games', $game_data['image_path'])
            ];
            break;
            
        case 'rhyme-platform':
            // Mapa de dificultad
            $difficulty_map = [
                1 => 'easy',
                2 => 'medium',
                3 => 'hard'
            ];
            $difficulty = $difficulty_map[$level] ?? 'easy';

            // Determinar límite de no rimas según nivel
            $nonRhymesLimit = 5;
            if ($level == 2) $nonRhymesLimit = 7;
            if ($level == 3) $nonRhymesLimit = 10;

            // Consulta para obtener palabra objetivo y rimas
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
                    GROUP BY r.word
                    ORDER BY RAND()
                    LIMIT 1";
            
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ssis", $difficulty, $difficulty, $nonRhymesLimit, $difficulty);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                throw new Exception('No se encontraron palabras para este nivel');
            }
            
            $row = $result->fetch_assoc();
            
            // Procesar rimas y no rimas
            $rhymes = !empty($row['rhymes']) ? explode('||', $row['rhymes']) : [];
            $non_rhymes = !empty($row['non_rhymes']) ? explode('||', $row['non_rhymes']) : [];
            
            // Si no hay suficientes no rimas, completar
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
            
            $response = [
                'target_word' => $row['target_word'],
                'words' => $all_words,
                'rhymes' => $rhymes
            ];
            break;
            
        // ... otros juegos
        
        default:
            throw new Exception('Juego no soportado');
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    // Respuesta de error en formato JSON
    echo json_encode(['error' => $e->getMessage()]);
}