<?php
if (!isset($game_data)) die("Error: Datos del juego no disponibles.");

$targetWord = $game_data['target_word'] ?? 'sol';
$words = $game_data['words'] ?? ['col', 'gol', 'pan', 'sal', 'pez'];
$rhymes = $game_data['rhymes'] ?? ['col', 'gol'];
$level = $game_data['level'] ?? 1;
?>
<?php ob_start(); ?>

<!-- Incluir Anime.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

<div class="game-container platform min-h-screen py-8 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Encabezado con progreso -->
        <div class="flex flex-wrap items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-blue-700">Saltarima</h1>
                <p class="text-lg text-gray-600">Nivel <?= $level ?></p>
            </div>

            <div class="w-full md:w-auto mt-4 md:mt-0">
                <div class="flex items-center mb-2">
                    <span class="text-gray-700 mr-2">Progreso:</span>
                    <div class="w-48 h-4 bg-gray-200 rounded-full overflow-hidden">
                        <div id="progress-bar" class="h-full bg-green-500 transition-all duration-500" style="width:0%"></div>
                    </div>
                </div>

                <div class="flex space-x-4">
                    <div class="flex items-center">
                        <i class="fas fa-star text-yellow-400 mr-2"></i>
                        <span id="score" class="font-bold">0</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-heart text-red-500 mr-2"></i>
                        <span id="lives" class="font-bold">3</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instrucciones con audio -->
        <div class="bg-blue-50 rounded-xl p-6 mb-8 text-center">
            <p class="text-lg mb-4">¡Salta solo sobre las palabras que riman con la palabra objetivo!</p>
            <button id="play-instructions" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg flex items-center mx-auto">
                <i class="fas fa-volume-up mr-2"></i> Escuchar instrucciones
            </button>
        </div>

        <!-- Palabra objetivo con audio -->
        <div class="target-box bg-white rounded-xl shadow-md p-6 mb-8 text-center">
            <p class="text-xl font-semibold text-gray-700 mb-2">Palabra objetivo:</p>
            <div class="flex items-center justify-center">
                <div class="target-word text-4xl font-bold text-indigo-700 py-3 px-6 bg-indigo-50 rounded-lg">
                    <?= htmlspecialchars($targetWord) ?>
                </div>
                <button id="play-target-word" class="ml-4 bg-indigo-500 hover:bg-indigo-600 text-white p-3 rounded-full">
                    <i class="fas fa-volume-up text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Área de juego -->
        <div class="platform-game relative bg-gradient-to-b from-blue-100 to-cyan-100 rounded-2xl p-6 shadow-lg min-h-[500px]">
            <?php foreach ($words as $index => $word): ?>
                <?php $isRhyme = in_array($word, $rhymes); ?>
                <div class="platform absolute cursor-pointer transform transition-transform duration-300 hover:scale-105"
                    style="left: <?= rand(5, 85) ?>%; top: <?= rand(100, 300) ?>px;"
                    data-rhyme="<?= $isRhyme ? '1' : '0' ?>"
                    data-word="<?= htmlspecialchars($word) ?>"
                    onclick="jump(this)">
                    <div class="platform-inner bg-gradient-to-r <?= $isRhyme ? 'from-green-400 to-emerald-400' : 'from-yellow-400 to-orange-400' ?> rounded-lg px-4 py-2 shadow-md">
                        <span class="platform-word text-white font-bold text-lg"><?= htmlspecialchars($word) ?></span>
                    </div>
                    <button class="play-word-btn absolute -right-2 -top-2 bg-blue-500 hover:bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-md">
                        <i class="fas fa-volume-up text-xs"></i>
                    </button>
                </div>
            <?php endforeach; ?>

            <div class="character absolute bottom-10 left-1/2 transform -translate-x-1/2" id="character">
                <div class="text-6xl">🤸</div>
            </div>
        </div>

        <!-- Feedback y controles -->
        <div class="feedback mt-8 text-center">
            <div class="result-icon mb-4">
                <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center mx-auto">
                    <i class="fas fa-question text-2xl text-gray-500"></i>
                </div>
            </div>
            <p class="message text-xl font-semibold text-gray-700 mb-4">Encuentra las palabras que riman con <?= htmlspecialchars($targetWord) ?></p>

            <div class="flex justify-center space-x-4">
                <button id="hint-btn" class="bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-6 rounded-lg">
                    <i class="fas fa-lightbulb mr-2"></i> Pista
                </button>
                <button id="next-btn" class="bg-green-500 hover:bg-green-600 text-white py-2 px-6 rounded-lg hidden">
                    <i class="fas fa-arrow-right mr-2"></i> Siguiente
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Estado del juego
    const gameState = {
        score: 0,
        lives: 3,
        currentLevel: <?= $level ?>,
        targetWord: "<?= $targetWord ?>",
        rhymes: <?= json_encode($rhymes) ?>,
        foundRhymes: 0,
        totalRhymes: <?= count($rhymes) ?>,
        character: document.getElementById('character'),
        speech: window.speechSynthesis,
        jumping: false, // Para evitar saltos múltiples simultáneos
        completedWords: [] // Palabras objetivo ya completadas
    };

    // Elementos DOM
    const dom = {
        score: document.getElementById('score'),
        lives: document.getElementById('lives'),
        nextBtn: document.getElementById('next-btn'),
        message: document.querySelector('.message'),
        resultIcon: document.querySelector('.result-icon'),
        progressBar: document.getElementById('progress-bar'),
        hintBtn: document.getElementById('hint-btn'),
        targetWord: document.querySelector('.target-word'),
        playTargetWord: document.getElementById('play-target-word')
    };

    // Inicializar juego
    function initGame() {
        updateUI();

        // Configurar eventos
        document.getElementById('play-instructions').addEventListener('click', playInstructions);
        dom.playTargetWord.addEventListener('click', () => speakWord(gameState.targetWord));

        // Configurar botones de audio para cada palabra
        document.querySelectorAll('.play-word-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const word = btn.parentElement.dataset.word;
                speakWord(word);
            });
        });

        // Configurar botón de pista
        dom.hintBtn.addEventListener('click', showHint);

        // Configurar botón siguiente
        dom.nextBtn.addEventListener('click', loadNextWord);
    }

    // Actualizar UI con estado actual
    function updateUI() {
        dom.score.textContent = gameState.score;
        dom.lives.textContent = gameState.lives;
        dom.progressBar.style.width = `${(gameState.foundRhymes / gameState.totalRhymes) * 100}%`;
    }

    // Hablar una palabra usando TTS
    function speakWord(word) {
        if (gameState.speech.speaking) gameState.speech.cancel();

        const utterance = new SpeechSynthesisUtterance(word);
        utterance.lang = 'es-ES';
        utterance.rate = 0.9;
        utterance.pitch = 1.2; // Más amigable para niños
        gameState.speech.speak(utterance);
    }

    // Reproducir instrucciones
    function playInstructions() {
        const text = "Salta solo sobre las palabras que riman con " + gameState.targetWord;
        speakWord(text);
    }

    // Mostrar pista
    function showHint() {
        const randomRhyme = gameState.rhymes[Math.floor(Math.random() * gameState.rhymes.length)];
        speakWord("Busca palabras como " + randomRhyme);

        // Destacar una rima aleatoria
        const platforms = document.querySelectorAll('.platform[data-rhyme="1"]');
        if (platforms.length > 0) {
            const randomPlatform = platforms[Math.floor(Math.random() * platforms.length)];

            anime({
                targets: randomPlatform,
                scale: [1, 1.3, 1],
                duration: 1000,
                easing: 'easeInOutQuad'
            });
        }
    }

    // Manejar salto a plataforma
    function jump(platform) {
        // Evitar saltos múltiples simultáneos
        if (gameState.jumping) return;
        gameState.jumping = true;

        const isRhyme = platform.dataset.rhyme === "1";
        const word = platform.dataset.word;

        // Obtener posición de la plataforma
        const platformRect = platform.getBoundingClientRect();
        const gameArea = document.querySelector('.platform-game');
        const gameRect = gameArea.getBoundingClientRect();

        // Calcular posición relativa dentro del área de juego
        const relativeY = platformRect.top - gameRect.top;

        // Animación de salto
        anime({
            targets: gameState.character,
            bottom: [30, relativeY + 20], // Ajuste de posición
            easing: 'easeOutQuad',
            duration: 800,
            complete: function() {
                if (isRhyme) {
                    handleCorrectJump(platform, word);
                } else {
                    handleIncorrectJump(platform, word);
                }

                // Volver a posición inicial
                anime({
                    targets: gameState.character,
                    bottom: 30,
                    duration: 500,
                    complete: () => gameState.jumping = false
                });
            }
        });
    }

    // Manejar salto correcto
    function handleCorrectJump(platform, word) {
        // Actualizar estado
        gameState.score += 10;
        gameState.foundRhymes++;

        // Actualizar UI
        updateUI();

        // Feedback visual
        platform.style.opacity = '0';
        platform.style.pointerEvents = 'none';
        dom.resultIcon.innerHTML = '<div class="w-16 h-16 rounded-full bg-green-500 flex items-center justify-center mx-auto"><i class="fas fa-check text-2xl text-white"></i></div>';
        dom.message.textContent = `¡Correcto! "${word}" rima con "${gameState.targetWord}"`;
        dom.message.className = 'message text-xl font-semibold text-green-600 mb-4';

        // Sonido
        playSuccessSound();

        // Función para avanzar al siguiente nivel
        function advanceToNextLevel() {
            const nextLevel = gameState.currentLevel + 1;

            if (nextLevel > 3) {
                // Si es el último nivel, mostrar pantalla de finalización
                window.location.href = 'level_complete.php?level=3';
            } else {
                // Recargar la página con el nuevo nivel
                window.location.href = `index.php?level=${nextLevel}`;
            }
        }
        // Verificar si se completó el nivel
        if (gameState.foundRhymes >= gameState.totalRhymes) {
            dom.message.textContent = '¡Palabras completadas!';
            dom.nextBtn.classList.remove('hidden');

            // Cambiar el botón "Siguiente" para avanzar de nivel
            dom.nextBtn.textContent = 'Siguiente Nivel';
            dom.nextBtn.onclick = advanceToNextLevel;
            dom.nextBtn.classList.remove('hidden');

            // Guardar progreso
            saveProgress(true);
        }
    }

    // Manejar salto incorrecto
    function handleIncorrectJump(platform, word) {
        // Actualizar estado
        gameState.lives--;

        // Actualizar UI
        updateUI();

        // Feedback visual
        anime({
            targets: platform,
            translateX: [0, 10, -10, 10, -10, 0],
            duration: 600,
            easing: 'easeInOutQuad'
        });

        dom.resultIcon.innerHTML = '<div class="w-16 h-16 rounded-full bg-red-500 flex items-center justify-center mx-auto"><i class="fas fa-times text-2xl text-white"></i></div>';
        dom.message.textContent = `¡Error! "${word}" no rima con "${gameState.targetWord}"`;
        dom.message.className = 'message text-xl font-semibold text-red-600 mb-4';

        // Sonido
        playErrorSound();

        // Verificar fin del juego
        if (gameState.lives <= 0) {
            setTimeout(() => {
                dom.message.textContent = '¡Juego terminado! Puntuación final: ' + gameState.score;
                dom.nextBtn.textContent = 'Reintentar';
                dom.nextBtn.classList.remove('hidden');
                dom.nextBtn.onclick = restartGame;

                // Guardar progreso
                saveProgress(false);
            }, 1000);
        }
    }

    // Función para generar un beep simple (alternativa)
    function beep(frequency, duration) {
        try {
            const audioCtx = new(window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);

            oscillator.type = 'sine';
            oscillator.frequency.value = frequency;
            gainNode.gain.value = 0.3;

            oscillator.start();
            setTimeout(() => {
                oscillator.stop();
            }, duration);
        } catch (e) {
            console.error('No se pudo generar beep:', e);
        }
    }

    // Sonidos en formato base64
    const sounds = {
        success: "data:audio/mp3;base64,SUQzBAAAAAABEVRYWFgAAAAtAAADY29tbWVudABCaWdTb3VuZEJhbmsuY29tIC8gTGFTb25vdGhlcXVlLm9yZwBURU5DAAAAHQAAA1N3aXRjaCBQbHVzIMKpIE5DSCBTb2Z0d2FyZQBUSVQyAAAABgAAAzIyMzUAVFNTRQAAAA8AAANMYXZmNTcuODMuMTAwAAAAAAAAAAAAAAD/80DEAAAAA0gAAAAATEFNRTMuMTAwVVVVVVVVVVVVVUxBTUUzLjEwMFVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVf/zQsRbAAADSAAAAABVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVf/zQMSkAAADSAAAAABVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVV",
        error: "data:audio/mp3;base64,SUQzBAAAAAABEVRYWFgAAAAtAAADY29tbWVudABCaWdTb3VuZEJhbmsuY29tIC8gTGFTb25vdGhlcXVlLm9yZwBURU5DAAAAHQAAA1N3aXRjaCBQbHVzIMKpIE5DSC CBTb2Z0d2FyZQBUSVQyAAAABgAAAzIyMzUAVFNTRQAAAA8AAANMYXZmNTcuODMuMTAwAAAAAAAAAAAAAAD/80DEAAAAA0gAAAAATEFNRTMuMTAwVVVVVVVVVVVVVUxBTUUzLjEwMFVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVf/zQsRbAAADSAAAAABVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVf/zQMSkAAADSAAAAABVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVV"
    };

    // Reproducir sonido de éxito
    function playSuccessSound() {
        try {
            const audio = new Audio(sounds.success);
            audio.play().catch(e => {
                console.error('Error al reproducir sonido de éxito:', e);
                beep(523.25, 200); // Do como respaldo
            });
        } catch (e) {
            console.error('Error al reproducir sonido de éxito:', e);
            beep(523.25, 200); // Do como respaldo
        }
    }

    // Reproducir sonido de error
    function playErrorSound() {
        try {
            const audio = new Audio(sounds.error);
            audio.play().catch(e => {
                console.error('Error al reproducir sonido de error:', e);
                beep(261.63, 300); // Do bajo como respaldo
            });
        } catch (e) {
            console.error('Error al reproducir sonido de error:', e);
            beep(261.63, 300); // Do bajo como respaldo
        }
    }

    // Guardar progreso en el servidor
    function saveProgress(levelCompleted) {
        fetch('../../api/save-progress.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                game: 'rhyme-platform',
                level: gameState.currentLevel,
                score: gameState.score,
                lives: gameState.lives,
                completed: levelCompleted
            })
        }).catch(error => console.error('Error al guardar progreso:', error));
    }

    // Cargar nueva palabra
    function loadNextWord() {
        // Ocultar botón siguiente
        dom.nextBtn.classList.add('hidden');

        // Restablecer estado para nueva palabra
        gameState.foundRhymes = 0;

        // Mostrar mensaje de carga
        dom.message.textContent = 'Cargando nueva palabra...';
        dom.resultIcon.innerHTML = '<div class="w-16 h-16 rounded-full bg-blue-500 flex items-center justify-center mx-auto"><i class="fas fa-spinner fa-spin text-2xl text-white"></i></div>';

        // Obtener nueva palabra del servidor
        fetch(`../../api/game-data.php?game=rhyme-platform&level=${gameState.currentLevel}`)
            .then(response => response.json())
            .then(data => {
                // Actualizar estado del juego con nueva palabra
                gameState.targetWord = data.target_word;
                gameState.rhymes = data.rhymes;
                gameState.totalRhymes = data.rhymes.length;

                // Actualizar UI
                dom.targetWord.textContent = gameState.targetWord;
                dom.message.textContent = `Encuentra las palabras que riman con ${gameState.targetWord}`;
                dom.resultIcon.innerHTML = '<div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center mx-auto"><i class="fas fa-question text-2xl text-gray-500"></i></div>';

                // Actualizar área de juego
                const gameArea = document.querySelector('.platform-game');
                gameArea.querySelectorAll('.platform').forEach(platform => platform.remove());

                // Agregar nuevas plataformas
                data.words.forEach(word => {
                    const isRhyme = data.rhymes.includes(word);
                    const topPosition = Math.floor(Math.random() * 300) + 50;
                    const leftPosition = Math.floor(Math.random() * 80) + 5;

                    const platform = document.createElement('div');
                    platform.className = 'platform absolute cursor-pointer transform transition-transform duration-300 hover:scale-105';
                    platform.style.left = `${leftPosition}%`;
                    platform.style.top = `${topPosition}px`;
                    platform.dataset.rhyme = isRhyme ? '1' : '0';
                    platform.dataset.word = word;
                    platform.onclick = function() {
                        jump(this);
                    };

                    platform.innerHTML = `
                    <div class="platform-inner bg-gradient-to-r ${isRhyme ? 'from-green-400 to-emerald-400' : 'from-yellow-400 to-orange-400'} rounded-lg px-4 py-2 shadow-md">
                        <span class="platform-word text-white font-bold text-lg">${word}</span>
                    </div>
                    <button class="play-word-btn absolute -right-2 -top-2 bg-blue-500 hover:bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-md">
                        <i class="fas fa-volume-up text-xs"></i>
                    </button>
                `;

                    gameArea.appendChild(platform);
                });

                // Configurar botones de audio para las nuevas palabras
                document.querySelectorAll('.play-word-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const word = btn.parentElement.dataset.word;
                        speakWord(word);
                    });
                });

                // Reproducir nueva palabra objetivo
                speakWord(gameState.targetWord);

                // Actualizar barra de progreso
                updateUI();
            })
            .catch(error => {
                console.error('Error al cargar nueva palabra:', error);
                dom.message.textContent = 'Error al cargar nueva palabra. Intenta de nuevo.';
                dom.nextBtn.classList.remove('hidden');
            });
    }

    // Reiniciar juego
    function restartGame() {
        location.reload();
    }

    // Inicializar el juego cuando se cargue la página
    document.addEventListener('DOMContentLoaded', initGame);
</script>

<style>
    .platform-game {
        position: relative;
        overflow: hidden;
        height: 500px;
        /* Altura fija para mejor cálculo */
    }

    .platform {
        transition: transform 0.3s, opacity 0.5s;
        z-index: 10;
        transform: translateY(0);
    }

    .platform-inner {
        transition: all 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        padding: 12px 20px;
    }

    .character {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 20;
        will-change: bottom;
        transition: bottom 0.5s ease;
    }

    .result-icon {
        transition: all 0.5s ease;
    }

    .platform-word {
        user-select: none;
        font-size: 1.25rem;
    }

    /* Animación de salto para el personaje */
    @keyframes jump {
        0% {
            bottom: 30px;
        }

        50% {
            bottom: 200px;
        }

        100% {
            bottom: 30px;
        }
    }

    .character.jumping {
        animation: jump 0.8s ease;
    }
</style>
<?php
$content = ob_get_clean();
include '../../includes/game_layout.php';
?>