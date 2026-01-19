<?php
$page_title = "Saltarima - Nivel {$game_data['level']}";
$targetWord = $game_data['target_word'] ?? 'sol';
$words = $game_data['words'] ?? ['col', 'gol', 'pan', 'sal', 'pez'];
$rhymes = $game_data['rhymes'] ?? ['col', 'gol'];
$level = $game_data['level'] ?? 1;
ob_start();
?>

<div class="max-w-5xl mx-auto">
    <!-- Game Header -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl p-6 mb-8 transition-colors duration-300">
        <div class="flex flex-col lg:flex-row justify-between items-center gap-6">
            <div class="text-center lg:text-left">
                <h1 class="text-4xl lg:text-5xl font-bold text-blue-700 dark:text-blue-400 mb-2">
                    <i class="fas fa-frog mr-3 text-green-600 dark:text-green-400"></i>
                    Saltarima
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300">Nivel <?= $level ?> - Encuentra las rimas</p>
            </div>

            <!-- Stats Panel -->
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 p-4 rounded-2xl border border-blue-200 dark:border-gray-500 min-w-[280px]">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-3 shadow-md">
                        <div class="flex items-center justify-center mb-1">
                            <i class="fas fa-star text-yellow-500 text-xl"></i>
                        </div>
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="score">0</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Puntos</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-3 shadow-md">
                        <div class="flex items-center justify-center mb-1">
                            <i class="fas fa-heart text-red-500 text-xl"></i>
                        </div>
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400" id="lives">3</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Vidas</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-3 shadow-md">
                        <div class="flex items-center justify-center mb-1">
                            <i class="fas fa-bullseye text-green-500 text-xl"></i>
                        </div>
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400" id="found-rhymes">0</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Encontradas</div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300 mb-1">
                        <span>Progreso</span>
                        <span id="progress-text">0/<?= count($rhymes) ?></span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3 overflow-hidden">
                        <div id="progress-bar" class="h-full bg-gradient-to-r from-green-500 to-blue-500 rounded-full transition-all duration-700 ease-out" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructions Card -->
    <div class="bg-gradient-to-r from-green-50 to-blue-50 dark:from-gray-700 dark:to-gray-600 rounded-3xl p-6 mb-8 border border-green-200 dark:border-gray-500 transition-colors duration-300">
        <div class="text-center">
            <h2 class="text-2xl font-semibold text-green-800 dark:text-green-300 mb-4">
                <i class="fas fa-lightbulb text-yellow-500 mr-3"></i>
                ¿Cómo jugar?
            </h2>
            <p class="text-lg text-gray-700 dark:text-gray-300 mb-4">
                ¡Ayuda al personaje a saltar solo sobre las palabras que <strong>riman</strong> con la palabra objetivo!
            </p>
            <p class="text-md text-gray-600 dark:text-gray-400 mb-4">
                Las plataformas verdes contienen rimas, las naranjas no riman.
            </p>
            <div class="flex justify-center gap-4">
                <button id="play-instructions" class="bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white font-bold py-3 px-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-volume-up"></i> Escuchar instrucciones
                </button>
                <button id="hint-btn" class="bg-yellow-500 hover:bg-yellow-600 dark:bg-yellow-600 dark:hover:bg-yellow-700 text-white font-bold py-3 px-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-lightbulb"></i> Pista
                </button>
            </div>
        </div>
    </div>

    <!-- Target Word Display -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl p-8 mb-8 text-center border border-gray-200 dark:border-gray-600 transition-colors duration-300">
        <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-4">Palabra objetivo:</h3>
        <div class="flex items-center justify-center gap-4">
            <div class="target-word-display text-5xl lg:text-6xl font-bold text-indigo-700 dark:text-indigo-400 py-4 px-8 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 rounded-2xl border-4 border-indigo-200 dark:border-indigo-600">
                <?= htmlspecialchars($targetWord) ?>
            </div>
            <button id="play-target-word" class="bg-indigo-500 hover:bg-indigo-600 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white p-4 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                <i class="fas fa-volume-up text-2xl"></i>
            </button>
        </div>
    </div>

    <!-- Game Area -->
    <div class="platform-game relative bg-gradient-to-b from-sky-200 via-blue-100 to-cyan-100 dark:from-gray-800 dark:to-gray-900 rounded-3xl p-8 shadow-2xl min-h-[600px] border border-blue-200 dark:border-gray-600 transition-colors duration-300">
        <!-- Platforms -->
        <?php foreach ($words as $index => $word): ?>
            <?php $isRhyme = in_array($word, $rhymes); ?>
            <div class="platform absolute cursor-pointer transform transition-all duration-300 hover:scale-110 group"
                 style="left: <?= rand(8, 85) ?>%; top: <?= rand(120, 400) ?>px;"
                 data-rhyme="<?= $isRhyme ? '1' : '0' ?>"
                 data-word="<?= htmlspecialchars($word) ?>"
                 onclick="jumpToPlatform(this)">
                <div class="platform-inner bg-gradient-to-r <?= $isRhyme ? 'from-green-400 to-emerald-500 dark:from-green-600 dark:to-emerald-700' : 'from-orange-400 to-yellow-500 dark:from-orange-600 dark:to-yellow-700' ?> rounded-2xl px-6 py-3 shadow-xl border-4 border-white dark:border-gray-800 group-hover:shadow-2xl transition-all duration-300">
                    <span class="platform-word text-white font-bold text-xl drop-shadow-lg"><?= htmlspecialchars($word) ?></span>
                </div>
                <button class="play-word-btn absolute -right-3 -top-3 bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white rounded-full w-10 h-10 flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-300 opacity-0 group-hover:opacity-100">
                    <i class="fas fa-volume-up text-sm"></i>
                </button>
            </div>
        <?php endforeach; ?>

        <!-- Character -->
        <div class="character absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20" id="character">
            <div class="text-7xl filter drop-shadow-xl">🤸‍♀️</div>
        </div>

        <!-- Floating particles for celebration -->
        <div id="particles" class="absolute inset-0 pointer-events-none hidden"></div>
    </div>

    <!-- Feedback Modal -->
    <div id="feedback-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 max-w-md w-full mx-4 text-center shadow-2xl border border-gray-200 dark:border-gray-600 transition-colors duration-300">
            <div id="result-icon" class="text-6xl mb-6"></div>
            <h3 id="result-title" class="text-2xl font-bold mb-4"></h3>
            <p id="message" class="text-lg text-gray-700 dark:text-gray-300 mb-6"></p>
            <button id="next-btn" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 dark:from-green-600 dark:to-green-700 dark:hover:from-green-700 dark:hover:to-green-800 text-white font-bold py-3 px-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                <i class="fas fa-arrow-right mr-2"></i> Continuar
            </button>
        </div>
    </div>
</div>

<script>
    // Estado del juego simplificado
    const gameState = {
        score: 0,
        lives: 3,
        level: <?= $level ?>,
        targetWord: "<?= $targetWord ?>",
        rhymes: <?= json_encode($rhymes) ?>,
        foundRhymes: 0,
        totalRhymes: <?= count($rhymes) ?>,
        character: document.getElementById('character'),
        speech: window.speechSynthesis,
        jumping: false,
        completedPlatforms: new Set()
    };

    // Inicializar juego
    document.addEventListener('DOMContentLoaded', function() {
        setupEventListeners();
        updateUI();

        // Auto-reproducir palabra objetivo después de 1 segundo
        setTimeout(() => speakWord(gameState.targetWord), 1000);
    });

    // Configurar event listeners
    function setupEventListeners() {
        // Botones de audio
        document.getElementById('play-instructions').addEventListener('click', () =>
            speakWord("Salta solo sobre las palabras que riman con " + gameState.targetWord));

        document.getElementById('play-target-word').addEventListener('click', () =>
            speakWord(gameState.targetWord));

        // Botones de audio de plataformas
        document.querySelectorAll('.play-word-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const word = btn.closest('.platform').dataset.word;
                speakWord(word);
            });
        });

        // Botón de pista
        document.getElementById('hint-btn').addEventListener('click', showHint);

        // Botón siguiente
        document.getElementById('next-btn').addEventListener('click', nextLevel);
    }

    // Función para saltar a plataforma
    function jumpToPlatform(platform) {
        if (gameState.jumping || gameState.completedPlatforms.has(platform)) return;

        gameState.jumping = true;
        const word = platform.dataset.word;
        const isRhyme = platform.dataset.rhyme === '1';

        // Calcular posición de destino
        const platformRect = platform.getBoundingClientRect();
        const gameArea = document.querySelector('.platform-game');
        const gameRect = gameArea.getBoundingClientRect();
        const targetBottom = platformRect.bottom - gameRect.bottom + 20;

        // Animación de salto
        gameState.character.style.transition = 'bottom 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        gameState.character.style.bottom = targetBottom + 'px';

        setTimeout(() => {
            if (isRhyme) {
                handleCorrectJump(platform, word);
            } else {
                handleIncorrectJump(platform, word);
            }

            // Regresar a posición inicial
            setTimeout(() => {
                gameState.character.style.bottom = '2rem';
                setTimeout(() => {
                    gameState.jumping = false;
                    gameState.character.style.transition = '';
                }, 500);
            }, 800);
        }, 800);
    }

    // Manejar salto correcto
    function handleCorrectJump(platform, word) {
        gameState.score += 10;
        gameState.foundRhymes++;
        gameState.completedPlatforms.add(platform);

        // Efectos visuales
        platform.style.transform = 'scale(0.8)';
        platform.style.opacity = '0.3';
        platform.style.pointerEvents = 'none';

        createParticles(platform, '#10b981');
        updateUI();
        showFeedback('success', `¡Excelente! "${word}" rima con "${gameState.targetWord}"`);

        // Verificar nivel completado
        if (gameState.foundRhymes >= gameState.totalRhymes) {
            setTimeout(() => {
                showLevelComplete();
            }, 1500);
        }
    }

    // Manejar salto incorrecto
    function handleIncorrectJump(platform, word) {
        gameState.lives--;

        // Efectos visuales
        platform.style.animation = 'shake 0.5s ease-in-out';
        createParticles(platform, '#ef4444');

        updateUI();
        showFeedback('error', `¡Cuidado! "${word}" no rima con "${gameState.targetWord}"`);

        if (gameState.lives <= 0) {
            setTimeout(() => showGameOver(), 1500);
        }

        setTimeout(() => {
            platform.style.animation = '';
        }, 500);
    }

    // Mostrar pista
    function showHint() {
        const availableRhymes = gameState.rhymes.filter(rhyme =>
            !Array.from(gameState.completedPlatforms).some(platform =>
                platform.dataset.word === rhyme));

        if (availableRhymes.length > 0) {
            const hintWord = availableRhymes[Math.floor(Math.random() * availableRhymes.length)];
            speakWord(`Pista: busca una palabra como ${hintWord}`);

            // Resaltar plataformas de rima disponibles
            document.querySelectorAll('.platform[data-rhyme="1"]').forEach(platform => {
                if (!gameState.completedPlatforms.has(platform)) {
                    platform.style.animation = 'pulse 1s ease-in-out 3';
                    setTimeout(() => platform.style.animation = '', 3000);
                }
            });
        }
    }

    // Crear partículas de celebración
    function createParticles(element, color) {
        const particlesContainer = document.getElementById('particles');
        particlesContainer.classList.remove('hidden');

        for (let i = 0; i < 8; i++) {
            const particle = document.createElement('div');
            particle.className = 'absolute w-2 h-2 rounded-full pointer-events-none';
            particle.style.backgroundColor = color;
            particle.style.left = element.offsetLeft + element.offsetWidth / 2 + 'px';
            particle.style.top = element.offsetTop + element.offsetHeight / 2 + 'px';

            particlesContainer.appendChild(particle);

            // Animar partícula
            const angle = (i / 8) * Math.PI * 2;
            const distance = 50 + Math.random() * 50;
            const x = Math.cos(angle) * distance;
            const y = Math.sin(angle) * distance;

            particle.animate([
                { transform: 'translate(0, 0) scale(1)', opacity: 1 },
                { transform: `translate(${x}px, ${y}px) scale(0)`, opacity: 0 }
            ], {
                duration: 1000,
                easing: 'ease-out'
            });

            setTimeout(() => particle.remove(), 1000);
        }

        setTimeout(() => particlesContainer.classList.add('hidden'), 1000);
    }

    // Mostrar feedback
    function showFeedback(type, message) {
        const modal = document.getElementById('feedback-modal');
        const icon = document.getElementById('result-icon');
        const title = document.getElementById('result-title');
        const msg = document.getElementById('message');

        if (type === 'success') {
            icon.className = 'fas fa-check-circle text-6xl text-green-500';
            title.textContent = '¡Correcto!';
            title.className = 'text-2xl font-bold text-green-600 mb-4';
        } else {
            icon.className = 'fas fa-times-circle text-6xl text-red-500';
            title.textContent = '¡Inténtalo de nuevo!';
            title.className = 'text-2xl font-bold text-red-600 mb-4';
        }

        msg.textContent = message;

        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('hidden'), 2000);
    }

    // Mostrar nivel completado
    function showLevelComplete() {
        const modal = document.getElementById('feedback-modal');
        const icon = document.getElementById('result-icon');
        const title = document.getElementById('result-title');
        const msg = document.getElementById('message');
        const nextBtn = document.getElementById('next-btn');

        icon.className = 'fas fa-trophy text-6xl text-yellow-500';
        title.textContent = '¡Nivel Completado!';
        title.className = 'text-2xl font-bold text-yellow-600 mb-4';
        msg.textContent = `¡Felicitaciones! Has encontrado todas las rimas. Puntuación: ${gameState.score} puntos`;
        nextBtn.innerHTML = '<i class="fas fa-arrow-right mr-2"></i> Siguiente Nivel';

        modal.classList.remove('hidden');

        // Guardar progreso
        saveProgress(true);
    }

    // Mostrar game over
    function showGameOver() {
        const modal = document.getElementById('feedback-modal');
        const icon = document.getElementById('result-icon');
        const title = document.getElementById('result-title');
        const msg = document.getElementById('message');
        const nextBtn = document.getElementById('next-btn');

        icon.className = 'fas fa-heart-broken text-6xl text-gray-500';
        title.textContent = 'Juego Terminado';
        title.className = 'text-2xl font-bold text-gray-600 mb-4';
        msg.textContent = `Puntuación final: ${gameState.score} puntos. ¡Mejor suerte la próxima vez!`;
        nextBtn.innerHTML = '<i class="fas fa-redo mr-2"></i> Jugar de Nuevo';

        modal.classList.remove('hidden');

        // Guardar progreso
        saveProgress(false);
    }

    // Avanzar al siguiente nivel
    function nextLevel() {
        const nextLevel = gameState.level + 1;
        if (nextLevel > 3) {
            window.location.href = 'level_complete.php?level=3';
        } else {
            window.location.href = `index.php?level=${nextLevel}`;
        }
    }

    // Actualizar UI
    function updateUI() {
        document.getElementById('score').textContent = gameState.score;
        document.getElementById('lives').textContent = gameState.lives;
        document.getElementById('found-rhymes').textContent = gameState.foundRhymes;
        document.getElementById('progress-text').textContent = `${gameState.foundRhymes}/${gameState.totalRhymes}`;

        const progressPercent = (gameState.foundRhymes / gameState.totalRhymes) * 100;
        document.getElementById('progress-bar').style.width = progressPercent + '%';
    }

    // Función TTS
    function speakWord(word) {
        if (!gameState.speech) return;

        if (gameState.speech.speaking) gameState.speech.cancel();

        const utterance = new SpeechSynthesisUtterance(word);
        utterance.lang = 'es-ES';
        utterance.rate = 0.9;
        utterance.pitch = 1.1;
        utterance.volume = 1;

        gameState.speech.speak(utterance);
    }

    // Guardar progreso
    function saveProgress(completed) {
        fetch('../../api/save-progress.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                game: 'rhyme-platform',
                level: gameState.level,
                score: gameState.score,
                lives: gameState.lives,
                completed: completed
            })
        }).catch(error => console.error('Error saving progress:', error));
    }
</script>

<style>
    /* Animaciones para el juego */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        50% { transform: translateX(5px); }
        75% { transform: translateX(-5px); }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    @keyframes bounce {
        0%, 20%, 53%, 80%, 100% { transform: translate3d(0,0,0); }
        40%, 43% { transform: translate3d(0,-30px,0); }
        70% { transform: translate3d(0,-15px,0); }
        90% { transform: translate3d(0,-4px,0); }
    }

    /* Estilos del personaje */
    .character {
        position: absolute;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%);
        z-index: 20;
        will-change: bottom;
        transition: bottom 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    /* Estilos de las plataformas */
    .platform {
        user-select: none;
        transition: all 0.3s ease;
        z-index: 10;
    }

    .platform-inner {
        border-radius: 1rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        border: 4px solid rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(4px);
        transition: all 0.3s ease;
    }

    .platform-word {
        font-family: 'OpenDyslexic', 'Comic Sans MS', cursive, sans-serif;
        font-weight: 800;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        user-select: none;
        -webkit-text-stroke: 1px rgba(255, 255, 255, 0.5);
    }

    /* Efectos hover */
    .platform:hover .platform-inner {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
    }

    /* Botones de audio */
    .play-word-btn {
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .play-word-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
    }

    /* Área de juego */
    .platform-game {
        position: relative;
        overflow: hidden;
        min-height: 600px;
        background: linear-gradient(135deg, #87CEEB 0%, #98D8E8 25%, #B0E0E6 50%, #AFEEEE 75%, #E0F6FF 100%);
        box-shadow: inset 0 0 50px rgba(0, 0, 0, 0.1);
    }

    /* Modo nocturno para área de juego */
    .dark .platform-game {
        background: linear-gradient(135deg, #1a365d 0%, #2d3748 25%, #4a5568 50%, #2d3748 75%, #1a202c 100%);
    }

    /* Partículas */
    #particles div {
        animation: float 1s ease-out forwards;
    }

    @keyframes float {
        0% {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
        100% {
            transform: translateY(-100px) scale(0);
            opacity: 0;
        }
    }

    /* Modal de feedback */
    #feedback-modal {
        backdrop-filter: blur(8px);
        animation: modalFadeIn 0.3s ease-out;
    }

    @keyframes modalFadeIn {
        0% {
            opacity: 0;
            transform: scale(0.9);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .platform-inner {
            padding: 8px 16px;
        }

        .platform-word {
            font-size: 1rem;
        }

        .character {
            bottom: 1rem;
        }

        .platform-game {
            min-height: 500px;
        }
    }

    /* Accesibilidad */
    .platform:focus {
        outline: 3px solid #3b82f6;
        outline-offset: 2px;
    }

    .play-word-btn:focus {
        outline: 2px solid #ffffff;
        outline-offset: 2px;
    }

    /* Animación de celebración */
    .celebration {
        animation: bounce 1s ease-in-out;
    }
</style>
<?php
$content = ob_get_clean();
include '../../includes/game_layout.php';
?>
