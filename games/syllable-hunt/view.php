<?php
// Extraer variables del array $game_data
$word = $game_data['word'] ?? 'palabra';
$syllables = $game_data['syllables'] ?? ['pa', 'la', 'bra'];
$correct_syllables = $game_data['correct_syllables'] ?? ['pa', 'la', 'bra'];
$image = $game_data['image'] ?? '';
$audio = $game_data['audio'] ?? '';
$level = $game_data['level'] ?? 1;

// Obtener progreso actual
$words_completed = $_SESSION['syllable_progress']['words_completed'] ?? 0;
$total_words = $_SESSION['syllable_progress']['total_words'] ?? 3;
$progress_percent = min(100, ($words_completed / $total_words) * 100);

ob_start();
?>
<!-- Cargar Howler.js para audio -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.3/howler.min.js"></script>

<div class="game-container syllables">
    <!-- Cabecera con nivel y controles TTS -->
    <div class="game-header">
        <h1>Caza Sílabas <small>Nivel <?= $level ?></small></h1>
        <div class="tts-controls">
            <button class="audio-btn" id="play-word-tts">
                <i class="fas fa-volume-up"></i> Escuchar palabra
            </button>
            <button class="audio-btn" id="play-instructions">
                <i class="fas fa-info-circle"></i> Instrucciones
            </button>
        </div>
    </div>

    <!-- Barra de progreso del nivel -->
    <div class="level-progress">
        <div class="progress-info">
            <span>Progreso del nivel: <?= $words_completed ?>/<?= $total_words ?> palabras</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?= $progress_percent ?>%"></div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="game-content">
        <div class="target-area">
            <img src="<?= $image ?>" alt="<?= htmlspecialchars($word) ?>" class="target-image">
            <div class="target-word"><?= htmlspecialchars($word) ?></div>
        </div>

        <div class="drop-zone">
            <?php for ($i = 0; $i < count($correct_syllables); $i++): ?>
                <div class="slot" data-index="<?= $i ?>"></div>
            <?php endfor; ?>
        </div>

        <div class="syllables-container">
            <?php foreach ($syllables as $syl): ?>
                <div class="syllable" draggable="true" data-syllable="<?= $syl ?>">
                    <?= $syl ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Controles del juego -->
    <div class="game-controls">
        <button class="btn check-btn" id="check-btn">
            <i class="fas fa-check"></i> Comprobar
        </button>
        <button class="btn reset-btn" id="reset-btn">
            <i class="fas fa-redo"></i> Reiniciar
        </button>
        <button class="btn next-btn hidden" id="next-btn">
            <i class="fas fa-arrow-right"></i> Siguiente palabra
        </button>
    </div>

    <!-- Feedback -->
    <div class="feedback">
        <div class="result-icon"></div>
        <p class="message"></p>
    </div>
</div>

<script>
    // Text-to-Speech Configuration
    const tts = {
        init() {
            if ('speechSynthesis' in window) {
                this.supported = true;
                this.voices = speechSynthesis.getVoices();

                // Cargar voces cuando estén disponibles
                speechSynthesis.onvoiceschanged = () => {
                    this.voices = speechSynthesis.getVoices();
                };
            } else {
                this.supported = false;
                console.warn('TTS no soportado en este navegador');
            }
        },

        speak(text, lang = 'es-ES') {
            if (!this.supported) return;

            // Cancelar cualquier habla previa
            speechSynthesis.cancel();

            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = lang;
            utterance.rate = 0.9;
            utterance.pitch = 1.2;

            // Seleccionar voz adecuada
            const voice = this.voices.find(v => v.lang === lang) || this.voices[0];
            if (voice) utterance.voice = voice;

            speechSynthesis.speak(utterance);
        }
    };

    // Variables globales
    const correctSyllables = <?= json_encode($correct_syllables) ?>;
    const word = "<?= $word ?>";
    let userOrder = Array(correctSyllables.length).fill(null);
    document.addEventListener('DOMContentLoaded', () => {
       
    // Crear elemento de menú faltante
    if (!document.getElementById('translate-page')) {
        const translateItem = document.createElement('div');
        translateItem.id = 'translate-page';
        translateItem.classList.add('hidden'); // Ocultar si no es necesario
        document.body.appendChild(translateItem);
    }
});
    // Sonidos de feedback
    const successAudio = new Howl({
        src: ['<?= get_audio('common', 'success.mp3') ?>']
    });

    const errorAudio = new Howl({
        src: ['<?= get_audio('common', 'error.mp3') ?>']
    });

    // Inicializar TTS al cargar la página
    window.addEventListener('DOMContentLoaded', () => {
        tts.init();

        // Botón para escuchar la palabra
        document.getElementById('play-word-tts').addEventListener('click', () => {
            tts.speak("<?= $word ?>", "es-ES");
        });

        // Botón para instrucciones
        document.getElementById('play-instructions').addEventListener('click', () => {
            tts.speak("Arrastra las sílabas en el orden correcto para formar la palabra", "es-ES");
        });
    });

    // Hacer las sílabas arrastrables
    document.querySelectorAll('.syllable').forEach(syllable => {
        syllable.addEventListener('dragstart', (e) => {
            e.dataTransfer.setData('text/plain', syllable.dataset.syllable);
        });
    });

    // Permitir soltar en los slots
    document.querySelectorAll('.slot').forEach(slot => {
        slot.addEventListener('dragover', (e) => {
            e.preventDefault();
            slot.classList.add('drag-over');
        });

        slot.addEventListener('dragleave', () => {
            slot.classList.remove('drag-over');
        });

        slot.addEventListener('drop', (e) => {
            e.preventDefault();
            slot.classList.remove('drag-over');

            const syllable = e.dataTransfer.getData('text/plain');
            const index = parseInt(slot.dataset.index);

            // Asignar la sílaba al slot
            slot.textContent = syllable;
            slot.dataset.syllable = syllable;
            userOrder[index] = syllable;
        });
    });

    // Comprobar resultado
    document.getElementById('check-btn').addEventListener('click', () => {
        const isCorrect = userOrder.every((syl, index) => syl === correctSyllables[index]);

        // Feedback visual
        const resultIcon = document.querySelector('.result-icon');
        const message = document.querySelector('.message');
        const nextBtn = document.getElementById('next-btn');

        if (isCorrect) {
            resultIcon.className = 'result-icon correct';
            resultIcon.innerHTML = '<i class="fas fa-check"></i>';
            message.textContent = '¡Correcto! La palabra está bien formada.';
            nextBtn.classList.remove('hidden');

            // Sonido de éxito
            successAudio.play();

            // Guardar progreso
            saveProgress(true);
        } else {
            resultIcon.className = 'result-icon incorrect';
            resultIcon.innerHTML = '<i class="fas fa-times"></i>';
            message.textContent = '¡Inténtalo de nuevo! El orden no es correcto.';

            // Sonido de error
            errorAudio.play();

            // Resaltar errores
            userOrder.forEach((syl, index) => {
                if (syl !== correctSyllables[index]) {
                    const slot = document.querySelector(`.slot[data-index="${index}"]`);
                    slot.classList.add('error');
                }
            });
        }
    });

    // Reiniciar
    document.getElementById('reset-btn').addEventListener('click', () => {
        userOrder = Array(correctSyllables.length).fill(null);
        document.querySelectorAll('.slot').forEach(slot => {
            slot.textContent = '';
            delete slot.dataset.syllable;
            slot.classList.remove('error');
        });
        document.querySelector('.result-icon').className = 'result-icon';
        document.querySelector('.message').textContent = '';
        document.getElementById('next-btn').classList.add('hidden');
    });

    // Siguiente palabra
    document.getElementById('next-btn').addEventListener('click', () => {
        // Verificar si el nivel está completo
        const wordsCompleted = <?= $_SESSION['syllable_progress']['words_completed'] ?? 0 ?>;

        if (wordsCompleted >= 2) { // Ya completó 3 con la actual
            window.location.href = 'level_complete.php?level=' + <?= $level ?>;
        } else {
            window.location.reload();
        }
    });

    // Guardar progreso
    function saveProgress(isCorrect) {
        if (!isCorrect) return;

        // Actualizar progreso en sesión
        fetch('index.php?action=update_progress', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=update_progress'
            })
            .then(response => response.json())
            .then(data => {
                if (data.completed) {
                    window.location.href = data.redirect;
                } else {
                    // Actualizar barra de progreso
                    const progressBar = document.querySelector('.progress-fill');
                    const progressPercent = (data.words_completed / 3) * 100;
                    progressBar.style.width = `${progressPercent}%`;

                    // Actualizar contador
                    document.querySelector('.progress-info span').textContent =
                        `Progreso del nivel: ${data.words_completed}/3 palabras`;

                    // Mostrar botón de siguiente palabra
                    document.getElementById('next-btn').classList.remove('hidden');
                }
            });

        // Guardar progreso en la API
        fetch('/api/save-progress.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                game: 'syllable-hunt',
                level: <?= $level ?>,
                correct: true,
                word: word
            })
        });
    }
</script>

<style>
    .game-container.syllables {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }

    .game-header {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 20px;
    }

    .target-area {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 20px;
    }

    .target-image {
        max-width: 150px;
        max-height: 150px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 10px;
    }

    .target-word {
        font-size: 2rem;
        font-weight: bold;
        color: #43658b;
        text-align: center;
    }

    .tts-controls {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .audio-btn {
        background-color: #4e89ae;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.9rem;
    }

    .drop-zone {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .slot {
        width: 80px;
        height: 80px;
        border: 2px dashed #4e89ae;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: bold;
        background-color: #f0f9ff;
        transition: all 0.3s;
    }

    .slot.drag-over {
        background-color: #e3f2fd;
        box-shadow: 0 0 8px rgba(78, 137, 174, 0.5);
    }

    .slot.error {
        background-color: #f8d7da;
        border-color: #dc3545;
    }

    .syllables-container {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .syllable {
        width: 70px;
        height: 70px;
        border: 2px solid #4e89ae;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: bold;
        background-color: white;
        cursor: move;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.3s;
    }

    .syllable:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
    }

    .game-controls {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 15px;
    }

    .btn {
        background-color: #4e89ae;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 30px;
        font-size: 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: all 0.3s;
    }

    .btn:hover {
        background-color: #43658b;
        transform: translateY(-2px);
    }

    .next-btn {
        background-color: #4CAF50;
    }

    .feedback {
        text-align: center;
        margin-top: 15px;
    }

    .result-icon {
        font-size: 2.5rem;
        margin-bottom: 10px;
    }

    .result-icon.correct {
        color: #4CAF50;
    }

    .result-icon.incorrect {
        color: #f44336;
    }

    .message {
        font-size: 1.1rem;
        margin-bottom: 10px;
    }
</style>
<?php
$content = ob_get_clean();
include '../../includes/game_layout.php';
