<?php ob_start(); ?>

<div class="max-w-3xl mx-auto px-4 py-10">
    <div class="game-header flex flex-wrap justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-blue-700">Rompecódigos Auditivos</h1>
            <p class="text-gray-600">Nivel <?= $game_data['level'] ?></p>
        </div>

        <div class="progress-container bg-blue-50 p-3 rounded-lg">
            <div class="flex justify-between mb-2">
                <span class="text-blue-700 font-medium">Progreso</span>
                <span class="text-blue-700 font-bold">
                    <?= $game_data['words_completed'] ?>/<?= $game_data['words_per_level'] ?>
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                <div class="bg-blue-600 h-3 transition-all duration-500"
                    style="width: <?= min(100, ($game_data['words_completed'] / $game_data['words_per_level']) * 100) ?>%">
                </div>
            </div>
        </div>
    </div>

    <!-- Botón de audio -->
    <div class="flex justify-center mb-8">
        <button id="play-btn" class="audio-btn bg-purple-500 text-white text-lg sm:text-xl px-6 py-3 rounded-lg shadow hover:bg-purple-600 transition w-full max-w-xs sm:max-w-none">
            <i class="fas fa-volume-up mr-2"></i> Escuchar palabra
        </button>
    </div>

    <!-- Opciones -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
        <?php foreach ($game_data['options'] as $index => $opt): ?>
            <button class="option w-full py-4 px-6 border-2 border-blue-400 rounded-xl text-base sm:text-lg font-semibold bg-white hover:bg-blue-50 transition max-w-full"
                data-correct="<?= $opt['correct'] ? '1' : '0' ?>"
                data-index="<?= $index ?>"
                onclick="checkAnswer(this)">
                <?= htmlspecialchars($opt['text']) ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Feedback -->
    <div class="feedback text-center hidden">
        <div class="feedback-content mb-4">
            <span class="result-icon flex items-center justify-center w-16 h-16 rounded-full text-3xl mx-auto mb-3"></span>
            <p class="message text-lg font-medium text-gray-700"></p>
        </div>
        <button class="next-btn bg-green-500 text-white text-lg px-6 py-3 rounded-lg shadow hover:bg-green-600 transition w-full max-w-xs sm:max-w-none">
            Siguiente palabra
        </button>
    </div>
</div>
<?php
$content = ob_get_clean();
include '../../includes/game_layout.php';
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.3/howler.min.js"></script>
<script>
    // Sistema de Text-to-Speech
    const TTS = {
        utterance: null,
        voices: [],
        currentVoice: null,
        isSupported: false,

        init: function() {
            // Verificar soporte del navegador
            this.isSupported = 'speechSynthesis' in window;

            if (!this.isSupported) {
                document.getElementById('play-btn').innerHTML =
                    '<i class="fas fa-exclamation-triangle mr-2"></i> Audio no disponible';
                document.getElementById('play-btn').classList.add('bg-yellow-500', 'hover:bg-yellow-600');
                document.getElementById('play-btn').disabled = true;
                return;
            }

            // Cargar voces disponibles
            this.voices = speechSynthesis.getVoices();
            if (this.voices.length === 0) {
                speechSynthesis.addEventListener('voiceschanged', () => {
                    this.voices = speechSynthesis.getVoices();
                    this.selectVoice();
                });
            } else {
                this.selectVoice();
            }
        },

        selectVoice: function() {
            // Preferir voces en español
            const spanishVoices = this.voices.filter(v =>
                v.lang.startsWith('es') &&
                v.name.toLowerCase().includes('female')
            );

            // Seleccionar la mejor voz disponible
            this.currentVoice = spanishVoices.length > 0 ?
                spanishVoices[0] :
                this.voices.find(v => v.lang.startsWith('es')) || this.voices[0];
        },

        speak: function(text) {
            // Limpiar y normalizar el texto
            const cleanText = text
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // Remover acentos
                .toLowerCase()
                .trim();
            // Cancelar cualquier habla previa
            speechSynthesis.cancel();

            // Crear nuevo utterance
            this.utterance = new SpeechSynthesisUtterance(text);
            this.utterance.lang = 'es-ES';
            this.utterance.rate = 0.9; // Velocidad reducida
            this.utterance.pitch = 1.1; // Tono ligeramente más agudo
            this.utterance.volume = 1;

            if (this.currentVoice) {
                this.utterance.voice = this.currentVoice;
            }

            // Eventos para manejar estado
            this.utterance.onstart = () => {
                document.getElementById('play-btn').innerHTML =
                    '<i class="fas fa-volume-up mr-2"></i> Reproduciendo...';
                document.getElementById('play-btn').classList.add('bg-purple-600');
            };

            this.utterance.onend = () => {
                document.getElementById('play-btn').innerHTML =
                    '<i class="fas fa-volume-up mr-2"></i> Escuchar de nuevo';
                document.getElementById('play-btn').classList.remove('bg-purple-600');
            };

            this.utterance.onerror = (event) => {
                console.error('Error en TTS:', event.error);
                document.getElementById('play-btn').innerHTML =
                    '<i class="fas fa-exclamation-triangle mr-2"></i> Error de audio';
                document.getElementById('play-btn').classList.remove('bg-purple-600');
                document.getElementById('play-btn').classList.add('bg-yellow-500');
            };

            // Iniciar síntesis de voz
            speechSynthesis.speak(this.utterance);
        },

        stop: function() {
            speechSynthesis.cancel();
        }
    };

    // Inicializar TTS cuando se cargue la página
    document.addEventListener('DOMContentLoaded', () => {
        TTS.init();

        // Configurar botón de audio
        document.getElementById('play-btn').addEventListener('click', () => {
            TTS.speak('<?= $game_data['word'] ?>');
        });

        // Reproducir automáticamente la primera vez
        if (TTS.isSupported) {
            setTimeout(() => TTS.speak('<?= $game_data['word'] ?>'), 500);
        }
    });


    function checkAnswer(btn) {
        const isCorrect = btn.dataset.correct === "1";

        btn.classList.add(isCorrect ? 'correct' : 'incorrect');

        document.querySelectorAll('.option').forEach(optionBtn => {
            optionBtn.disabled = true;
            if (optionBtn.dataset.correct === "1") {
                optionBtn.classList.add('correct-answer');
            }
        });

        const resultIcon = document.querySelector('.result-icon');
        const message = document.querySelector('.message');
        const feedback = document.querySelector('.feedback');
        const nextBtn = document.querySelector('.next-btn');

        resultIcon.className = 'result-icon ' + (isCorrect ? 'correct' : 'incorrect');
        resultIcon.innerHTML = isCorrect ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>';

        message.textContent = isCorrect ?
            '¡Correcto! La palabra es <?= htmlspecialchars($game_data['word']) ?>' :
            'Incorrecto. La palabra correcta es "<?= htmlspecialchars($game_data['word']) ?>"';

        feedback.classList.remove('hidden');
        nextBtn.classList.remove('hidden');

        fetch('../../api/save-progress.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                game: 'auditory-codes',
                correct: isCorrect,
                level: <?= $game_data['level'] ?>,
                word: '<?= $game_data['word'] ?>',
                selected: btn.textContent
            })
        });
    }

    // Manejar siguiente palabra o reintento
    document.querySelector('.next-btn').addEventListener('click', () => {
        const isCorrect = document.querySelector('.result-icon').classList.contains('correct');

        if (isCorrect) {
            // Nueva palabra
            TTS.stop();
            location.reload();
        } else {
            // Reintentar la misma palabra
            document.querySelectorAll('.option').forEach(optionBtn => {
                optionBtn.disabled = false;
                optionBtn.classList.remove('correct', 'incorrect', 'correct-answer');
            });
            document.querySelector('.feedback').classList.add('hidden');
            TTS.speak('<?= $game_data['word'] ?>'); // Reproducir el audio nuevamente
        }
    });

    // Detener el audio al salir de la página
    window.addEventListener('beforeunload', () => {
        TTS.stop();
    });
</script>

<style>
    /* Estilos específicos para TTS */
    .audio-btn.bg-purple-500 {
        background-color: #9f7aea;
    }

    .audio-btn.bg-purple-500:hover {
        background-color: #805ad5;
    }

    .audio-btn.bg-purple-600 {
        background-color: #805ad5;
    }

    .audio-btn.bg-yellow-500 {
        background-color: #ecc94b;
    }

    .progress-container {
        min-width: 200px;
    }

    .option {
        font-family: 'OpenDyslexic', sans-serif;
        cursor: pointer;
        transition: all 0.3s;
    }

    .option:hover {
        background-color: #f0f9ff;
        transform: translateY(-3px);
    }

    .option.correct {
        background-color: #d4edda;
        border-color: #28a745;
    }

    .option.incorrect {
        background-color: #f8d7da;
        border-color: #dc3545;
    }

    .option.correct-answer {
        background-color: #d4edda;
        border-color: #28a745;
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    .audio-btn {
        background-color: #ed6663;
    }

    .feedback {
        margin-top: 25px;
        padding: 20px;
        border-radius: 15px;
        text-align: center;
    }

    .result-icon {
        display: inline-block;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        font-size: 2rem;
        line-height: 60px;
        margin-bottom: 15px;
    }

    .result-icon.correct {
        background-color: #d4edda;
        color: #28a745;
    }

    .result-icon.incorrect {
        background-color: #f8d7da;
        color: #dc3545;
    }
</style>