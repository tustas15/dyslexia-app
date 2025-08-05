<?php
$base_url = BASE_URL;

// Definir imágenes SVG para el robot
$robot_img = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Crect width='200' height='200' fill='%234e89ae'/%3E%3Ctext x='50%25' y='50%25' font-size='80' text-anchor='middle' fill='white'%3E🤖%3C/text%3E%3C/svg%3E";
$robot_talking = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Crect width='200' height='200' fill='%2343658b'/%3E%3Ctext x='50%25' y='50%25' font-size='80' text-anchor='middle' fill='white'%3E🗣️%3C/text%3E%3C/svg%3E";
$robot_happy = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Crect width='200' height='200' fill='%234CAF50'/%3E%3Ctext x='50%25' y='50%25' font-size='80' text-anchor='middle' fill='white'%3E😄%3C/text%3E%3C/svg%3E";
$robot_sad = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Crect width='200' height='200' fill='%23f44336'/%3E%3Ctext x='50%25' y='50%25' font-size='80' text-anchor='middle' fill='white'%3E😢%3C/text%3E%3C/svg%3E";

$current_index = $_SESSION['robot_progress']['current_word'];
$total_words = count($_SESSION['robot_progress']['words']);
$progress = ($current_index / $total_words) * 100;

$correctWord = $current_word['correct_word'] ?? 'zapato';
$incorrectWord = $current_word['incorrect_word'] ?? 'sapato';
$image = $current_word['image'] ?? get_word_image($correctWord);
$level = $_SESSION['robot_progress']['level'] ?? 1;
?>

<?php ob_start(); ?>

<div class="min-h-screen bg-gradient-to-b from-blue-50 to-indigo-100 py-8 px-4">
    <div class="max-w-3xl mx-auto bg-white rounded-3xl shadow-xl overflow-hidden">
        <!-- Encabezado con progreso -->
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl md:text-3xl font-bold text-white">
                    Palabrabot <span class="text-yellow-300">Nivel <?= $level ?></span>
                </h1>
                <span class="bg-yellow-400 text-blue-900 font-bold py-1 px-3 rounded-full">
                    <?= $current_index + 1 ?>/<?= $total_words ?>
                </span>
            </div>
            
            <div class="w-full bg-blue-300 rounded-full h-4 mb-2">
                <div class="bg-yellow-400 h-4 rounded-full" style="width: <?= $progress ?>%"></div>
            </div>
            <p class="text-blue-100 text-sm">Progreso del nivel</p>
        </div>
        
        <!-- Instrucciones -->
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <p class="text-lg text-gray-700 font-medium">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    ¡Ayuda al robot a corregir las palabras que pronuncia mal!
                </p>
                <button id="play-instructions" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-full transition-all">
                    <i class="fas fa-volume-up mr-2"></i> Instrucciones
                </button>
            </div>
        </div>
        
        <!-- Área principal del juego -->
        <div class="p-6 md:p-8">
            <!-- Robot y burbuja de diálogo -->
            <div class="flex flex-col md:flex-row items-center justify-between mb-8">
                <!-- Imagen del robot -->
                <div class="mb-6 md:mb-0 md:mr-6">
                    <div class="bg-blue-50 rounded-full p-4 shadow-md">
                        <img src="<?= $robot_img ?>" id="robot-img" alt="Robot amigable" class="w-40 h-40">
                    </div>
                </div>
                
                <!-- Burbuja de diálogo -->
                <div class="flex-1">
                    <div class="relative bg-white rounded-2xl p-5 shadow-lg max-w-md mx-auto">
                        <div class="absolute -bottom-4 left-1/4 transform -translate-x-1/2 w-0 h-0 border-l-8 border-r-8 border-t-8 border-l-transparent border-r-transparent border-t-white"></div>
                        <p id="robot-text" class="text-2xl font-bold text-center text-gray-800">
                            <?= htmlspecialchars($incorrectWord) ?>
                        </p>
                    </div>
                    
                    <!-- Controles de audio -->
                    <div class="flex justify-center space-x-4 mt-6">
                        <button id="play-robot-word" class="bg-indigo-500 hover:bg-indigo-600 text-white px-5 py-2 rounded-full flex items-center transition-all">
                            <i class="fas fa-volume-up mr-2"></i> Escuchar robot
                        </button>
                        <button id="play-correct-word" class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-full flex items-center transition-all">
                            <i class="fas fa-question-circle mr-2"></i> ¿Cómo se dice?
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Panel de corrección -->
            <div class="bg-blue-50 rounded-2xl p-6 mb-8">
                <div class="mb-4">
                    <label for="correction-input" class="block text-lg font-medium text-blue-800 mb-2">
                        <i class="fas fa-edit mr-2"></i> Escribe la palabra correcta:
                    </label>
                    <input 
                        type="text" 
                        id="correction-input"
                        placeholder="Escribe aquí la corrección..."
                        autocomplete="off"
                        autocapitalize="off"
                        spellcheck="false"
                        class="w-full px-5 py-3 text-xl text-center font-bold border-4 border-blue-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all"
                    >
                </div>
                
                <div class="flex flex-col sm:flex-row justify-center gap-4 mt-6">
                    <button id="check-btn" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-xl flex items-center justify-center transition-all shadow-md hover:shadow-lg">
                        <i class="fas fa-check-circle mr-2"></i> Corregir
                    </button>
                    <button id="hint-btn" class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold py-3 px-6 rounded-xl flex items-center justify-center transition-all shadow-md hover:shadow-lg">
                        <i class="fas fa-lightbulb mr-2"></i> Pista
                    </button>
                </div>
            </div>
            
            <!-- Área de retroalimentación -->
            <div class="feedback bg-blue-50 rounded-2xl p-6 mb-8">
                <div class="result-icon text-center text-6xl mb-4">
                    <!-- Icono se llenará dinámicamente -->
                </div>
                <p class="message text-center text-xl font-medium mb-6">
                    <!-- Mensaje se llenará dinámicamente -->
                </p>
                <button id="next-btn" class="next-btn hidden w-full bg-gradient-to-r from-green-500 to-teal-500 hover:from-green-600 hover:to-teal-600 text-white font-bold py-4 px-6 rounded-xl flex items-center justify-center transition-all shadow-md hover:shadow-lg">
                    <i class="fas fa-arrow-right mr-2"></i> Siguiente palabra
                </button>
            </div>
            
            <!-- Imagen de la palabra -->
            <div class="bg-blue-50 rounded-2xl p-6 text-center">
                <h3 class="text-lg font-semibold text-blue-800 mb-4">
                    <i class="fas fa-image mr-2"></i> ¿Qué es esto?
                </h3>
                <div class="flex justify-center">
                    <div class="bg-white rounded-xl p-4 shadow-md inline-block">
                        <img src="<?= $image ?>" alt="<?= htmlspecialchars($correctWord) ?>" class="w-48 h-48 object-contain mx-auto">
                        <p class="mt-2 text-gray-600 font-medium"><?= $correctWord ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Variables globales
    const correctWord = "<?= $correctWord ?>";
    const incorrectWord = "<?= $incorrectWord ?>";
    let attempts = 0;
    const maxAttempts = 3;
    const ttsSupported = 'speechSynthesis' in window;
    
    // Definir imágenes del robot
    const robotNormalImg = "<?= $robot_img ?>";
    const robotTalkingImg = "<?= $robot_talking ?>";
    const robotHappyImg = "<?= $robot_happy ?>";
    const robotSadImg = "<?= $robot_sad ?>";
    
    // Hablar texto usando TTS
    function speakText(text, rate = 0.9) {
        if (!ttsSupported) {
            showMessage('Tu navegador no soporta Text-to-Speech. Te recomendamos usar Chrome o Edge.', 'error');
            return;
        }
        
        // Cancelar cualquier discurso previo
        window.speechSynthesis.cancel();
        
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'es-ES';
        utterance.rate = rate;
        window.speechSynthesis.speak(utterance);
    }
    
    // Función para hablar con el acento del robot (más lento)
    function speakLikeRobot(text) {
        speakText(text, 0.7);
    }

    // Event listeners
    document.getElementById('play-instructions').addEventListener('click', () => {
        speakText('¡Ayuda al robot a corregir las palabras que pronuncia mal!');
    });

    document.getElementById('play-robot-word').addEventListener('click', () => {
        // Cambiar imagen a robot hablando
        document.getElementById('robot-img').src = robotTalkingImg;
        speakLikeRobot(incorrectWord);
        
        // Restaurar imagen después de un tiempo (estimamos 2 segundos)
        setTimeout(() => {
            document.getElementById('robot-img').src = robotNormalImg;
        }, 2000);
    });

    document.getElementById('play-correct-word').addEventListener('click', () => {
        speakText(correctWord);
    });

    document.getElementById('check-btn').addEventListener('click', checkCorrection);
    document.getElementById('hint-btn').addEventListener('click', showHint);
    document.getElementById('next-btn').addEventListener('click', nextWord);

    // Función para verificar la corrección
    function checkCorrection() {
        const input = document.getElementById('correction-input');
        const userInput = input.value.trim().toLowerCase();

        if (!userInput) {
            showMessage('Por favor escribe una corrección', 'error');
            return;
        }

        attempts++;
        const isCorrect = userInput === correctWord;

        const resultIcon = document.querySelector('.result-icon');
        const message = document.querySelector('.message');
        const nextBtn = document.getElementById('next-btn');

        if (isCorrect) {
            // Éxito
            document.getElementById('robot-img').src = robotHappyImg;
            resultIcon.innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
            message.textContent = '¡Excelente! Has ayudado al robot a hablar correctamente.';
            message.className = 'message text-center text-xl font-bold text-green-700';
            nextBtn.classList.remove('hidden');

            // Hablar mensaje de éxito
            speakText('¡Excelente!');
            saveProgress(true);
        } else {
            // Error
            document.getElementById('robot-img').src = robotSadImg;
            resultIcon.innerHTML = '<i class="fas fa-times-circle text-red-500"></i>';
            message.className = 'message text-center text-xl font-bold text-red-700';
            
            // Hablar mensaje de error
            speakText('Inténtalo de nuevo');

            if (attempts >= maxAttempts) {
                message.textContent = `¡Oh no! La palabra correcta es "${correctWord}".`;
                nextBtn.classList.remove('hidden');
                saveProgress(false);
            } else {
                message.textContent = `Intento ${attempts} de ${maxAttempts}. ¡Sigue intentando!`;
                showHint();
            }
        }
    }

    // Mostrar pista
    function showHint() {
        if (attempts === 1) {
            const firstLetter = correctWord.charAt(0);
            const msg = `Pista: La palabra empieza con "${firstLetter.toUpperCase()}"`;
            showMessage(msg, 'info');
            speakText(`Pista: La palabra empieza con ${firstLetter.toUpperCase()}`);
        } else if (attempts === 2) {
            const lastLetter = correctWord.charAt(correctWord.length - 1);
            const msg = `Pista: La palabra termina con "${lastLetter.toUpperCase()}"`;
            showMessage(msg, 'info');
            speakText(`Pista: La palabra termina con ${lastLetter.toUpperCase()}`);
        } else {
            const msg = `Pista: La palabra tiene ${correctWord.length} letras`;
            showMessage(msg, 'info');
            speakText(`Pista: La palabra tiene ${correctWord.length} letras`);
        }
    }

    // Mostrar mensaje
    function showMessage(text, type) {
        const message = document.querySelector('.message');
        message.textContent = text;
        
        if (type === 'error') {
            message.className = 'message text-center text-xl font-bold text-red-700';
        } else if (type === 'info') {
            message.className = 'message text-center text-xl font-bold text-blue-700';
        } else {
            message.className = 'message text-center text-xl font-bold text-gray-700';
        }
    }

    // Avanzar a la siguiente palabra
    function nextWord() {
        fetch('update_progress.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                correct: attempts < maxAttempts
            })
        }).then(() => {
            window.location.reload();
        });
    }

    // Guardar progreso
    function saveProgress(isCorrect) {
        fetch('/dyslexia-app/api/save-progress.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                game: 'word-robot',
                level: <?= $level ?>,
                correct: isCorrect,
                word: correctWord,
                attempts: attempts
            })
        });
    }
    
    // Advertencia inicial si no hay soporte TTS
    if (!ttsSupported) {
        showMessage('Tu navegador no soporta Text-to-Speech. Algunas funciones de sonido no estarán disponibles.', 'error');
    }
    
    // Enfocar el campo de entrada al cargar
    document.getElementById('correction-input').focus();
</script>

<style>
    /* Fuente OpenDyslexic para mejorar accesibilidad */
    @font-face {
        font-family: 'OpenDyslexic';
        src: url('/dyslexia-app/assets/fonts/OpenDyslexic-Regular.otf') format('opentype');
    }
    
    /* Mejorar legibilidad para dislexia */
    body {
        font-family: 'OpenDyslexic', 'Comic Sans MS', sans-serif;
        letter-spacing: 0.5px;
        line-height: 1.6;
    }
    
    /* Estilos adicionales para botones */
    button {
        transition: all 0.3s ease;
    }
    
    button:active {
        transform: translateY(2px);
    }
    
    /* Animación para el robot */
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    
    .robot-character img {
        transition: transform 0.3s ease;
    }
    
    .robot-character:hover img {
        animation: bounce 0.5s ease infinite;
    }
    
    /* Estilo para el campo de entrada */
    #correction-input {
        font-family: 'OpenDyslexic', 'Comic Sans MS', sans-serif;
        background-color: #f0f9ff;
    }
    
    /* Estilo para mensajes */
    .feedback {
        transition: all 0.5s ease;
    }
    
    /* Efecto de pulso para botones importantes */
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(78, 137, 174, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(78, 137, 174, 0); }
        100% { box-shadow: 0 0 0 0 rgba(78, 137, 174, 0); }
    }
    
    #check-btn {
        animation: pulse 2s infinite;
    }
</style>
<?php
$content = ob_get_clean();
include '../../includes/game_layout.php';
?>