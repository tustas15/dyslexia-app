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

// Obtener nivel desde parámetro GET
$level = isset($_GET['level']) ? (int)$_GET['level'] : 1;
$next_level = min(3, $level + 1);

// Guardar desbloqueo de siguiente nivel
$stmt = $db->prepare("UPDATE users SET level = GREATEST(level, ?) WHERE id = ?");
$stmt->bind_param("ii", $next_level, $_SESSION['user_id']);
$stmt->execute();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Nivel Completado - Dyslexia App</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        @keyframes confettiFall {
            0% { transform: translateY(-100vh) rotate(0deg); }
            100% { transform: translateY(100vh) rotate(360deg); }
        }
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            animation: confettiFall 5s linear forwards;
            z-index: 1000;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Confetti animation -->
    <div id="confetti-container" class="absolute inset-0 pointer-events-none"></div>
    
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-2xl w-full text-center relative z-10">
        <div class="mb-6">
            <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-trophy text-yellow-500 text-5xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-blue-700 mb-2">¡Nivel <?= $level ?> Completado!</h1>
            <p class="text-xl text-gray-600">Has completado 3 palabras en el nivel <?= $level ?></p>
        </div>
        
        <div class="bg-blue-50 rounded-xl p-6 mb-8">
            <div class="flex justify-center mb-6">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <p class="text-gray-600">Palabras completadas</p>
                    <p class="text-3xl font-bold text-blue-700">3/3</p>
                </div>
            </div>
            
            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                <div class="bg-green-500 h-4" style="width: 100%"></div>
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="index.php?level=<?= $level ?>" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition flex items-center justify-center">
                <i class="fas fa-redo mr-2"></i> Repetir nivel
            </a>
            
            <?php if ($next_level > $level): ?>
                <a href="index.php?level=<?= $next_level ?>" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition flex items-center justify-center">
                    <i class="fas fa-arrow-right mr-2"></i> Nivel <?= $next_level ?>
                </a>
            <?php else: ?>
                <a href="../../index.php" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-3 px-6 rounded-lg transition flex items-center justify-center">
                    <i class="fas fa-home mr-2"></i> Volver al inicio
                </a>
            <?php endif; ?>
        </div>
    </div>

    
</body>
</html>