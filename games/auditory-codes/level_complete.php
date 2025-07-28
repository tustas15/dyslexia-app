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
$next_level = min(3, $level + 1); // Máximo 3 niveles

// Obtener puntuación del nivel
$user_id = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT SUM(score) AS level_score 
                     FROM user_progress 
                     WHERE user_id = ? 
                     AND game_type = 'auditory-codes' 
                     AND JSON_UNQUOTE(JSON_EXTRACT(details, '$.level')) = ?");
$stmt->bind_param("ii", $user_id, $level);
$stmt->execute();
$progress = $stmt->get_result()->fetch_assoc();
$level_score = $progress['level_score'] ?? 0;

// En level-complete.php
if (isset($_GET['restart'])) {
    $_SESSION['auditory_codes_level_' . $level . '_score'] = 0; // Resetear puntuación
    header("Location: index.php?level=$level");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Nivel Completado - Dyslexia App</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-2xl w-full text-center">
        <div class="mb-6">
            <i class="fas fa-trophy text-yellow-500 text-6xl mb-4"></i>
            <h1 class="text-3xl font-bold text-blue-700 mb-2">¡Nivel <?= $level ?> Completado!</h1>
            <p class="text-xl text-gray-600">Has completado el nivel <?= $level ?> de Rompecódigos Auditivos</p>
        </div>
        
        <div class="bg-blue-50 rounded-xl p-6 mb-8">
            <div class="flex justify-center gap-8 mb-4">
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <p class="text-gray-600">Palabras completadas</p>
                    <p class="text-3xl font-bold text-blue-700">10/10</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <p class="text-gray-600">Puntuación</p>
                    <p class="text-3xl font-bold text-blue-700"><?= $level_score ?></p>
                </div>
            </div>
            
            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                <div class="bg-green-500 h-4" style="width: 100%"></div>
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="index.php?level=<?= $level ?>" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition">
                <i class="fas fa-redo mr-2"></i> Repetir nivel
            </a>
            
            <?php if ($next_level > $level): ?>
                <a href="index.php?level=<?= $next_level ?>" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition">
                    <i class="fas fa-arrow-right mr-2"></i> Nivel <?= $next_level ?>
                </a>
            <?php else: ?>
                <a href="../../index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition">
                    <i class="fas fa-home mr-2"></i> Volver al inicio
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>