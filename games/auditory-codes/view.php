<?php
$page_title = "Rompecódigos Auditivos - Nivel {$game_data['level']}";
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <!-- Game Header -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
        <div class="flex flex-col lg:flex-row justify-between items-center gap-6">
            <div class="text-center lg:text-left">
                <h1 class="text-3xl lg:text-4xl font-bold text-blue-700 mb-2">
                    <i class="fas fa-volume-up mr-3 text-purple-600"></i>
                    Rompecódigos Auditivos
                </h1>
                <p class="text-lg text-gray-600">Nivel <?= $game_data['level'] ?></p>
            </div>

            <!-- Progress Section -->
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 p-4 rounded-xl border border-blue-200 min-w-[250px]">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-blue-700 font-semibold text-sm">Progreso del Nivel</span>
                    <span class="text-blue-700 font-bold text-lg">
                        <?= $game_data['words_completed'] ?>/<?= $game_data['words_per_level'] ?>
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-4 rounded-full transition-all duration-700 ease-out"
                         style="width: <?= min(100, ($game_data['words_completed'] / $game_data['words_per_level']) * 100) ?>%">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructions Card -->
    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-6 mb-8 border border-purple-200">
        <div class="text-center">
            <h2 class="text-xl font-semibold text-purple-800 mb-3">
                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                ¿Cómo jugar?
            </h2>
            <p class="text-gray-700 text-lg mb-4">Escucha la palabra y selecciona la opción correcta de las que aparecen abajo.</p>
            <div class="flex justify-center gap-4 text-sm text-gray-600">
                <span class="flex items-center gap-2">
                    <i class="fas fa-volume-up text-purple-600"></i>
                    Haz clic en el botón de audio
                </span>
                <span class="flex items-center gap-2">
                    <i class="fas fa-mouse-pointer text-blue-600"></i>
                    Selecciona la palabra correcta
                </span>
            </div>
        </div>
    </div>

    <!-- Audio Control -->
    <div class="text-center mb-8">
        <button id="play-btn" class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white text-xl font-bold py-4 px-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <i class="fas fa-volume-up mr-3"></i>
            <span id="play-text">Escuchar palabra</span>
        </button>
        <p class="text-sm text-gray-500 mt-2">Haz clic para escuchar la pronunciación</p>
    </div>

    <!-- Answer Options -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <?php foreach ($game_data['options'] as $index => $opt): ?>
            <button class="option group relative w-full py-6 px-8 border-3 border-blue-300 rounded-2xl text-xl font-bold bg-white hover:bg-blue-50 hover:border-blue-500 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2 focus:outline-none focus:ring-4 focus:ring-blue-300"
                data-correct="<?= $opt['correct'] ? '1' : '0' ?>"
                data-index="<?= $index ?>"
                onclick="checkAnswer(this)">
                <span class="relative z-10"><?= htmlspecialchars($opt['text']) ?></span>
                <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-500 rounded-2xl opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Feedback Modal -->
    <div id="feedback-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-3xl p-8 max-w-md w-full mx-4 text-center shadow-2xl">
            <div id="result-icon" class="text-6xl mb-6"></div>
            <h3 id="result-title" class="text-2xl font-bold mb-4"></h3>
            <p id="message" class="text-lg text-gray-700 mb-6"></p>
            <button id="next-btn" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <i class="fas fa-arrow-right mr-2"></i>
                Continuar
            </button>
        </div>
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
                const playText = document.getElementById('play-text');
                playText.textContent = 'Audio no disponible';
                document.getElementById('play-btn').classList.remove('from-purple-500', 'to-purple-600', 'hover:from-purple-600', 'hover:to-purple-700');
                document.getElementById('play-btn').classList.add('from-yellow-500', 'to-yellow-600', 'hover:from-yellow-600', 'hover:to-yellow-700');
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
                const playText = document.getElementById('play-text');
                playText.textContent = 'Reproduciendo...';
                document.getElementById('play-btn').classList.add('from-purple-700', 'to-purple-800');
            };

            this.utterance.onend = () => {
                const playText = document.getElementById('play-text');
                playText.textContent = 'Escuchar de nuevo';
                document.getElementById('play-btn').classList.remove('from-purple-700', 'to-purple-800');
            };

            this.utterance.onerror = (event) => {
                console.error('Error en TTS:', event.error);
                const playText = document.getElementById('play-text');
                playText.textContent = 'Error de audio';
                document.getElementById('play-btn').classList.remove('from-purple-700', 'to-purple-800');
                document.getElementById('play-btn').classList.add('from-yellow-500', 'to-yellow-600');
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
            setTimeout(() => TTS.speak('<?= $game_data['word'] ?>'), 1000);
        }
    });


    function checkAnswer(btn) {
        const isCorrect = btn.dataset.correct === "1";

        // Visual feedback for selected answer
        if (isCorrect) {
            btn.classList.add('bg-green-100', 'border-green-500', 'text-green-800');
        } else {
            btn.classList.add('bg-red-100', 'border-red-500', 'text-red-800');
        }

        // Disable all options and highlight correct answer
        document.querySelectorAll('.option').forEach(optionBtn => {
            optionBtn.disabled = true;
            if (optionBtn.dataset.correct === "1") {
                optionBtn.classList.add('bg-green-200', 'border-green-600', 'text-green-900', 'ring-4', 'ring-green-300');
            }
        });

        // Show feedback modal
        const modal = document.getElementById('feedback-modal');
        const resultIcon = document.getElementById('result-icon');
        const resultTitle = document.getElementById('result-title');
        const message = document.getElementById('message');
        const nextBtn = document.getElementById('next-btn');

        modal.classList.remove('hidden');

        if (isCorrect) {
            resultIcon.className = 'fas fa-check-circle text-green-500';
            resultTitle.textContent = '¡Excelente!';
            resultTitle.className = 'text-2xl font-bold text-green-600 mb-4';
            message.textContent = 'Has identificado correctamente la palabra auditiva.';
            nextBtn.innerHTML = '<i class="fas fa-arrow-right mr-2"></i> Siguiente Palabra';
        } else {
            resultIcon.className = 'fas fa-times-circle text-red-500';
            resultTitle.textContent = '¡Inténtalo de nuevo!';
            resultTitle.className = 'text-2xl font-bold text-red-600 mb-4';
            message.textContent = `La palabra correcta era "${<?= json_encode($game_data['word']) ?>}". ¡No te preocupes, sigue practicando!`;
            nextBtn.innerHTML = '<i class="fas fa-redo mr-2"></i> Reintentar';
        }

        // Save progress
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
                selected: btn.textContent.trim()
            })
        }).catch(error => console.error('Error saving progress:', error));
    }

    // Handle next button or retry
    document.getElementById('next-btn').addEventListener('click', () => {
        const modal = document.getElementById('feedback-modal');
        const isCorrect = document.getElementById('result-icon').classList.contains('fa-check-circle');

        modal.classList.add('hidden');
        TTS.stop();

        if (isCorrect) {
            // Load next word
            location.reload();
        } else {
            // Reset for retry
            document.querySelectorAll('.option').forEach(optionBtn => {
                optionBtn.disabled = false;
                optionBtn.classList.remove('bg-green-100', 'border-green-500', 'text-green-800',
                                         'bg-red-100', 'border-red-500', 'text-red-800',
                                         'bg-green-200', 'border-green-600', 'text-green-900', 'ring-4', 'ring-green-300');
            });
            TTS.speak('<?= $game_data['word'] ?>');
        }
    });

    // Detener el audio al salir de la página
    window.addEventListener('beforeunload', () => {
        TTS.stop();
    });
</script>

<style>
    /* Typography for dyslexia-friendly fonts */
    .option {
        font-family: 'OpenDyslexic', 'Comic Sans MS', cursive, sans-serif;
    }

    /* Smooth transitions for better UX */
    .option {
        transition: all 0.3s ease;
    }

    /* Custom focus styles for accessibility */
    .option:focus {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }

    /* Modal backdrop blur effect */
    #feedback-modal {
        backdrop-filter: blur(4px);
    }
</style>
