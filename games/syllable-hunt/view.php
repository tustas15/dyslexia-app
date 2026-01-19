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

<div class="max-w-4xl mx-auto">
    <!-- Game Header -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl p-6 mb-8 transition-colors duration-300">
        <div class="flex flex-col lg:flex-row justify-between items-center gap-6">
            <div class="text-center lg:text-left">
                <h1 class="text-4xl lg:text-5xl font-bold text-blue-700 dark:text-blue-400 mb-2">
                    <i class="fas fa-puzzle-piece mr-3 text-orange-600 dark:text-orange-400"></i>
                    Caza Sílabas
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300">Nivel <?= $level ?> - Ordena las sílabas</p>
            </div>

            <!-- Progress Section -->
            <div class="bg-gradient-to-r from-orange-50 to-blue-50 dark:from-gray-700 dark:to-gray-600 p-4 rounded-xl border border-orange-200 dark:border-gray-500 min-w-[280px]">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-orange-700 dark:text-orange-300 font-semibold text-sm">Progreso del Nivel</span>
                    <span class="text-orange-700 dark:text-orange-300 font-bold text-lg">
                        <?= $words_completed ?>/<?= $total_words ?>
                    </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-4 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-500 to-blue-500 h-4 rounded-full transition-all duration-700 ease-out"
                         style="width: <?= $progress_percent ?>%">
                    </div>
                </div>
                <div class="text-center mt-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Palabras completadas</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructions Card -->
    <div class="bg-gradient-to-r from-orange-50 to-blue-50 dark:from-gray-700 dark:to-gray-600 rounded-3xl p-6 mb-8 border border-orange-200 dark:border-gray-500 transition-colors duration-300">
        <div class="text-center">
            <h2 class="text-2xl font-semibold text-orange-800 dark:text-orange-300 mb-4">
                <i class="fas fa-lightbulb text-yellow-500 mr-3"></i>
                ¿Cómo jugar?
            </h2>
            <p class="text-lg text-gray-700 dark:text-gray-300 mb-4">
                Arrastra las sílabas desde abajo hacia los espacios en blanco para formar la palabra correcta.
            </p>
            <div class="flex justify-center gap-4">
                <button id="play-word-tts" class="bg-orange-500 hover:bg-orange-600 dark:bg-orange-600 dark:hover:bg-orange-700 text-white font-bold py-3 px-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-volume-up"></i> Escuchar palabra
                </button>
                <button id="play-instructions" class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i> Instrucciones
                </button>
            </div>
        </div>
    </div>

    <!-- Game Area -->
    <div class="bg-gradient-to-b from-blue-50 via-white to-orange-50 dark:from-gray-800 dark:via-gray-700 dark:to-gray-600 rounded-3xl p-8 shadow-2xl border border-blue-200 dark:border-gray-500 transition-colors duration-300">
        <!-- Target Word Display -->
        <div class="text-center mb-8">
            <?php if (!empty($image)): ?>
                <div class="inline-block p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-lg mb-4">
                    <img src="<?= $image ?>" alt="<?= htmlspecialchars($word) ?>" class="w-32 h-32 object-contain rounded-xl">
                </div>
            <?php endif; ?>
            <h3 class="text-2xl font-bold text-blue-700 dark:text-blue-300 mb-4">Forma la palabra: <span class="text-3xl">"<?= htmlspecialchars($word) ?>"</span></h3>
        </div>

        <!-- Drop Zone -->
        <div class="flex justify-center gap-4 mb-8 flex-wrap">
            <?php for ($i = 0; $i < count($correct_syllables); $i++): ?>
                <div class="w-20 h-20 border-3 border-dashed border-blue-400 dark:border-blue-600 rounded-xl flex items-center justify-center text-2xl font-bold bg-white dark:bg-gray-800 shadow-lg transition-all duration-300 hover:shadow-xl"
                     data-index="<?= $i ?>">
                </div>
            <?php endfor; ?>
        </div>

        <!-- Syllables Container -->
        <div class="flex justify-center gap-4 flex-wrap">
            <?php foreach ($syllables as $syl): ?>
                <div class="w-16 h-16 bg-gradient-to-r from-orange-400 to-blue-500 dark:from-orange-600 dark:to-blue-600 rounded-xl flex items-center justify-center text-2xl font-bold text-white shadow-lg cursor-move transform hover:scale-110 hover:-translate-y-1 transition-all duration-300 border-2 border-white dark:border-gray-800"
                     draggable="true"
                     data-syllable="<?= $syl ?>">
                    <?= $syl ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Game Controls -->
    <div class="flex justify-center gap-6 mt-8">
        <button id="check-btn" class="bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white font-bold py-4 px-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2">
            <i class="fas fa-check"></i> Comprobar
        </button>
        <button id="reset-btn" class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-4 px-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2">
            <i class="fas fa-redo"></i> Reiniciar
        </button>
        <button id="next-btn" class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2 hidden">
            <i class="fas fa-arrow-right"></i> Siguiente palabra
        </button>
    </div>

    <!-- Feedback Modal -->
    <div id="feedback-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 max-w-md w-full mx-4 text-center shadow-2xl border border-gray-200 dark:border-gray-600 transition-colors duration-300">
            <div id="result-icon" class="text-6xl mb-6"></div>
            <h3 id="result-title" class="text-2xl font-bold mb-4"></h3>
            <p id="message" class="text-lg text-gray-700 dark:text-gray-300 mb-6"></p>
            <button id="modal-next-btn" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 dark:from-green-600 dark:to-green-700 dark:hover:from-green-700 dark:hover:to-green-800 text-white font-bold py-3 px-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                <i class="fas fa-arrow-right mr-2"></i> Continuar
            </button>
        </div>
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
    document.querySelectorAll('[draggable="true"]').forEach(syllable => {
        syllable.addEventListener('dragstart', (e) => {
            e.dataTransfer.setData('text/plain', syllable.dataset.syllable);
            syllable.classList.add('opacity-50', 'scale-105');
        });

        syllable.addEventListener('dragend', () => {
            syllable.classList.remove('opacity-50', 'scale-105');
        });
    });

    // Permitir soltar en los slots
    document.querySelectorAll('[data-index]').forEach(slot => {
        slot.addEventListener('dragover', (e) => {
            e.preventDefault();
            slot.classList.add('ring-4', 'ring-blue-300', 'scale-105');
        });

        slot.addEventListener('dragleave', () => {
            slot.classList.remove('ring-4', 'ring-blue-300', 'scale-105');
        });

        slot.addEventListener('drop', (e) => {
            e.preventDefault();
            slot.classList.remove('ring-4', 'ring-blue-300', 'scale-105');

            const syllable = e.dataTransfer.getData('text/plain');
            const index = parseInt(slot.dataset.index);

            // Asignar la sílaba al slot
            slot.textContent = syllable;
            slot.dataset.syllable = syllable;
            slot.classList.add('text-blue-700', 'dark:text-blue-300', 'font-bold');
            userOrder[index] = syllable;
        });
    });

    // Comprobar resultado
    document.getElementById('check-btn').addEventListener('click', () => {
        const isCorrect = userOrder.every((syl, index) => syl === correctSyllables[index]);

        // Show feedback modal
        const modal = document.getElementById('feedback-modal');
        const resultIcon = document.getElementById('result-icon');
        const resultTitle = document.getElementById('result-title');
        const message = document.getElementById('message');
        const nextBtn = document.getElementById('modal-next-btn');

        modal.classList.remove('hidden');

        if (isCorrect) {
            resultIcon.className = 'fas fa-check-circle text-6xl text-green-500';
            resultTitle.textContent = '¡Excelente!';
            resultTitle.className = 'text-2xl font-bold text-green-600 mb-4';
            message.textContent = '¡Perfecto! Has ordenado correctamente las sílabas.';
            nextBtn.innerHTML = '<i class="fas fa-arrow-right mr-2"></i> Siguiente Palabra';

            // Sonido de éxito
            successAudio.play();

            // Guardar progreso
            saveProgress(true);
        } else {
            resultIcon.className = 'fas fa-times-circle text-6xl text-red-500';
            resultTitle.textContent = '¡Inténtalo de nuevo!';
            resultTitle.className = 'text-2xl font-bold text-red-600 mb-4';
            message.textContent = 'El orden de las sílabas no es correcto. ¡Sigue intentando!';
            nextBtn.innerHTML = '<i class="fas fa-redo mr-2"></i> Reintentar';

            // Sonido de error
            errorAudio.play();

            // Resaltar errores
            userOrder.forEach((syl, index) => {
                if (syl && syl !== correctSyllables[index]) {
                    const slot = document.querySelector(`[data-index="${index}"]`);
                    slot.classList.add('bg-red-100', 'dark:bg-red-900/20', 'border-red-500');
                    setTimeout(() => {
                        slot.classList.remove('bg-red-100', 'dark:bg-red-900/20', 'border-red-500');
                    }, 2000);
                }
            });
        }
    });

    // Reiniciar
    document.getElementById('reset-btn').addEventListener('click', () => {
        userOrder = Array(correctSyllables.length).fill(null);
        document.querySelectorAll('[data-index]').forEach(slot => {
            slot.textContent = '';
            delete slot.dataset.syllable;
            slot.classList.remove('text-blue-700', 'dark:text-blue-300', 'font-bold',
                                 'bg-red-100', 'dark:bg-red-900/20', 'border-red-500');
        });

        // Hide modal
        document.getElementById('feedback-modal').classList.add('hidden');
    });

    // Modal next button
    document.getElementById('modal-next-btn').addEventListener('click', () => {
        const modal = document.getElementById('feedback-modal');
        const isCorrect = document.getElementById('result-icon').classList.contains('fa-check-circle');

        modal.classList.add('hidden');

        if (isCorrect) {
            // Verificar si el nivel está completo
            const wordsCompleted = <?= $_SESSION['syllable_progress']['words_completed'] ?? 0 ?>;

            if (wordsCompleted >= 2) { // Ya completó 3 con la actual
                window.location.href = 'level_complete.php?level=' + <?= $level ?>;
            } else {
                window.location.reload();
            }
        } else {
            // Reset for retry
            userOrder = Array(correctSyllables.length).fill(null);
            document.querySelectorAll('[data-index]').forEach(slot => {
                slot.textContent = '';
                delete slot.dataset.syllable;
                slot.classList.remove('text-blue-700', 'dark:text-blue-300', 'font-bold');
            });
        }
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
    fetch('index.php', {
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

                document.getElementById('next-btn').classList.remove('hidden');
            }
        });

    // MEJORADO: Ruta corregida para save-progress.php
    fetch('../../api/save-progress.php', {
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
    }).catch(error => {
        console.log('Error guardando progreso:', error);
        // No bloquear la experiencia si falla el guardado
    });
}
</script>

<style>
    /* Custom styles for drag and drop effects */
    [draggable="true"] {
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }

    /* Modal backdrop blur effect for better UX */
    #feedback-modal {
        backdrop-filter: blur(4px);
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

    /* Accessibility improvements */
    [data-index]:focus {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }

    [draggable="true"]:focus {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }
</style>
<?php
$content = ob_get_clean();
include '../../includes/game_layout.php';
