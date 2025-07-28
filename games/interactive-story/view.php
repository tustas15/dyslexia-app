<?php
// view.php (actualizado para usar image_path)
ob_start();
?>
<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <!-- Header con progreso -->
    <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold"><?= htmlspecialchars($game_data['title']) ?></h1>
            <p class="text-gray-600">Nivel <?= $game_data['level'] ?></p>
        </div>

        <div class="progress-container bg-blue-50 p-3 rounded-lg">
            <div class="flex justify-between mb-2">
                <span class="text-blue-700 font-medium">Historias completadas</span>
                <span class="text-blue-700 font-bold">
                    <?= $game_data['completed_count'] ?>/3
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                <div class="bg-blue-600 h-3 transition-all duration-500"
                    style="width: <?= ($game_data['completed_count'] / 3) * 100 ?>%">
                </div>
            </div>
        </div>
    </div>

    <!-- Instrucciones -->
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <strong>Instrucciones:</strong>
                    <span id="instructions-text">Arrastra las palabras desde el área inferior hasta los espacios en blanco en la historia.</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Imagen de la historia (actualizada para usar image_path) -->
    <?php if (!empty($game_data['image_path'])): ?>
        <div class="mb-6 rounded-lg overflow-hidden shadow-md max-w-md mx-auto">
            <img src="<?= htmlspecialchars($game_data['image_path']) ?>"
                alt="Ilustración de la historia: <?= htmlspecialchars($game_data['title']) ?>"
                class="w-full h-auto object-cover">
        </div>
    <?php endif; ?>

    <!-- Texto de la historia con espacios en blanco como drop targets -->
    <div class="border-2 border-blue-400 rounded-lg p-6 mb-6 text-lg leading-relaxed min-h-[200px] bg-white" id="story-text">
        <?php
        $template = $game_data['template'];
        $parts = preg_split('/(\{[^}]+\})/', $template, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach ($parts as $part) {
            if (preg_match('/\{([^}]+)\}/', $part, $match)) {
                $category = $match[1];
                echo '<div class="inline-block align-middle min-w-[120px] relative">';
                echo '<div class="border-b-4 border-dashed border-red-400 py-1 px-2 text-center min-h-[40px] cursor-pointer drop-target inline-block" data-category="' . htmlspecialchars($category) . '">______</div>';
                echo '<div class="absolute -top-5 left-0 right-0 text-xs text-blue-700 font-bold text-center">' . htmlspecialchars($category) . '</div>';
                echo '</div>';
            } else {
                echo '<span class="inline">' . htmlspecialchars($part) . '</span>';
            }
        }
        ?>
    </div>

    <!-- Palabras disponibles (actualizado para usar image_path) -->
    <div class="bg-blue-100 rounded-lg p-5 mb-6">
        <h3 class="text-xl font-bold text-center mb-4">Palabras Disponibles</h3>
        <div class="flex flex-wrap gap-4 justify-center" id="words-container">
            <?php
            // Recolectar todas las palabras de todas las categorías
            $allWords = [];
            foreach ($game_data['elements'] as $category => $items) {
                foreach ($items as $item) {
                    $allWords[] = [
                        'word' => $item['word'],
                        'category' => $category,
                        'image_path' => $item['image_path'] ?? null
                    ];
                }
            }

            // Si no hay palabras en story_elements, usar las del JSON
            if (empty($allWords) && is_array($options)) {
                foreach ($options as $category => $words) {
                    if (in_array($category, $placeholders)) {
                        foreach ($words as $word) {
                            $allWords[] = [
                                'word' => $word,
                                'category' => $category,
                                'image_path' => null
                            ];
                        }
                    }
                }
            }

            // Mezclar las palabras
            shuffle($allWords);

            foreach ($allWords as $word):
            ?>
                <div class="draggable-word bg-white border-2 border-blue-400 rounded-lg p-3 w-36 text-center cursor-move shadow hover:-translate-y-1 transform transition"
                    draggable="true"
                    data-word="<?= htmlspecialchars($word['word']) ?>"
                    data-category="<?= htmlspecialchars($word['category']) ?>"
                    data-text="<?= htmlspecialchars($word['word']) ?>">
                    <?php if (!empty($word['image_path'])): ?>
                        <img src="<?= htmlspecialchars($word['image_path']) ?>"
                            alt="<?= htmlspecialchars($word['word']) ?>"
                            class="w-24 h-24 object-contain mx-auto mb-2">
                    <?php else: ?>
                        <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center mb-2">
                            <i class="fas fa-image text-gray-400 text-3xl"></i>
                        </div>
                    <?php endif; ?>
                    <div class="font-bold h-10 flex items-center justify-center mb-1"><?= htmlspecialchars($word['word']) ?></div>
                    <button class="text-red-500 hover:text-red-700 text-lg audio-btn element-audio" data-text="<?= htmlspecialchars($word['word']) ?>">
                        <i class="fas fa-volume-up"></i>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Botones -->
    <div class="flex justify-center gap-4 mb-6">
        <button id="reset-story" class="bg-blue-500 hover:bg-blue-700 text-white px-6 py-2 rounded-full flex items-center gap-2">
            <i class="fas fa-redo"></i> Reiniciar
        </button>
        <button id="save-story" class="bg-green-500 hover:bg-green-700 text-white px-6 py-2 rounded-full flex items-center gap-2">
            <i class="fas fa-check"></i> Completar Historia
        </button>
    </div>

    <!-- Resultado -->
    <div class="bg-green-100 border-2 border-green-600 rounded-lg p-6 hidden" id="result-container">
        <h3 class="text-lg font-bold mb-3 text-center">¡Historia Completa!</h3>
        <div class="bg-white p-4 rounded-md text-lg leading-relaxed mb-4" id="final-story"></div>

        <div class="voice-selector mb-4">
            <span class="voice-label font-medium text-gray-700 mr-2">Voz:</span>
            <select id="voice-select" class="voice-select border border-gray-300 rounded px-3 py-2">
                <option value="">Cargando voces...</option>
            </select>
        </div>

        <div class="flex justify-center gap-4">
            <button id="read-story" class="bg-blue-500 hover:bg-blue-700 text-white px-6 py-2 rounded-full flex items-center gap-2">
                <i class="fas fa-volume-up"></i> Leer Historia
            </button>
            <button id="pause-story" class="bg-yellow-500 hover:bg-yellow-700 text-white px-6 py-2 rounded-full flex items-center gap-2 hidden">
                <i class="fas fa-pause"></i> Pausar
            </button>
            <button id="new-story" class="bg-green-500 hover:bg-green-700 text-white px-6 py-2 rounded-full flex items-center gap-2">
                <i class="fas fa-plus"></i> Nueva Historia
            </button>
        </div>
    </div>
</div>

<script>
    // Variables globales
    let draggedWord = null;
    let speechSynthesis = window.speechSynthesis;
    let voices = [];
    let currentUtterance = null;
    let selectedVoice = null;

    // Cargar voces disponibles
    function loadVoices() {
        voices = speechSynthesis.getVoices();
        const voiceSelect = document.getElementById('voice-select');
        voiceSelect.innerHTML = '';

        // Filtrar voces en español
        const spanishVoices = voices.filter(voice => voice.lang.includes('es'));

        if (spanishVoices.length === 0) {
            voiceSelect.innerHTML = '<option value="">No hay voces en español disponibles</option>';
            return;
        }

        spanishVoices.forEach((voice, index) => {
            const option = document.createElement('option');
            option.value = index;
            option.textContent = `${voice.name} (${voice.lang})`;
            voiceSelect.appendChild(option);
        });

        // Seleccionar la primera voz por defecto
        voiceSelect.value = 0;
        selectedVoice = spanishVoices[0];
    }

    // Inicializar eventos de arrastre
    function initializeDragAndDrop() {
        // Palabras arrastrables
        document.querySelectorAll('.draggable-word').forEach(word => {
            word.addEventListener('dragstart', function(e) {
                draggedWord = this;
                this.classList.add('dragging');
                e.dataTransfer.setData('text/plain', this.dataset.word);
            });

            word.addEventListener('dragend', function() {
                this.classList.remove('dragging');
                draggedWord = null;
            });
        });

        // Áreas de destino (espacios en blanco)
        document.querySelectorAll('.drop-target').forEach(target => {
            target.addEventListener('dragover', function(e) {
                e.preventDefault();
                if (draggedWord) {
                    this.classList.add('drag-over');
                }
            });

            target.addEventListener('dragleave', function() {
                this.classList.remove('drag-over');
            });

            target.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');

                if (draggedWord && this.dataset.category === draggedWord.dataset.category) {
                    // Guardar referencia a la palabra que se está colocando
                    const currentWord = draggedWord.dataset.word;

                    // Si ya hay una palabra en este espacio, restaurarla
                    if (this.querySelector('.word-placed')) {
                        const previousWord = this.querySelector('.word-placed').dataset.word;

                        // Habilitar la palabra previamente usada
                        const prevWordElement = document.querySelector(`.draggable-word[data-word="${previousWord}"]`);
                        if (prevWordElement) {
                            prevWordElement.style.opacity = '1';
                            prevWordElement.style.pointerEvents = 'auto';
                        }

                        // Eliminar la palabra existente
                        this.innerHTML = '';
                    }

                    // Crear elemento para la palabra colocada
                    const wordElement = document.createElement('div');
                    wordElement.className = 'word-placed bg-blue-100 border border-blue-300 rounded px-2 py-1 inline-block';
                    wordElement.textContent = currentWord;
                    wordElement.dataset.word = currentWord;

                    // Botón para eliminar
                    const deleteBtn = document.createElement('button');
                    deleteBtn.className = 'delete-word text-red-500 ml-2';
                    deleteBtn.innerHTML = '<i class="fas fa-times"></i>';
                    deleteBtn.addEventListener('click', function() {
                        // Eliminar la palabra colocada
                        wordElement.remove();

                        // Restaurar el espacio en blanco
                        this.innerHTML = '______';
                        this.dataset.word = '';

                        // Habilitar la palabra en la lista
                        const wordToEnable = document.querySelector(`.draggable-word[data-word="${currentWord}"]`);
                        if (wordToEnable) {
                            wordToEnable.style.opacity = '1';
                            wordToEnable.style.pointerEvents = 'auto';
                        }
                    }.bind(this));

                    wordElement.appendChild(deleteBtn);
                    this.appendChild(wordElement);
                    this.dataset.word = currentWord;

                    // Marcar la palabra como usada
                    draggedWord.style.opacity = '0.3';
                    draggedWord.style.pointerEvents = 'none';
                }
            });
        });
    }

    // Inicializar audio con TTS
    function initializeTTS() {
        document.querySelectorAll('.element-audio').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const text = this.dataset.text;
                speakText(text);
            });
        });

        // Evento para cambiar la voz
        document.getElementById('voice-select').addEventListener('change', function() {
            const voiceIndex = this.value;
            if (voiceIndex !== '' && voices.length > 0) {
                selectedVoice = voices[voiceIndex];
            }
        });
    }

    // Hablar texto usando TTS
    function speakText(text) {
        if (speechSynthesis.speaking) {
            speechSynthesis.cancel();
        }

        if (!selectedVoice) {
            alert('Por favor, selecciona una voz primero');
            return;
        }

        const utterance = new SpeechSynthesisUtterance(text);
        utterance.voice = selectedVoice;
        utterance.rate = 0.9; // Velocidad ligeramente reducida
        utterance.pitch = 1.2; // Tono más alto para sonido infantil
        utterance.lang = 'es-ES';

        utterance.onstart = function() {
            document.getElementById('pause-story').classList.remove('hidden');
        };

        utterance.onend = function() {
            document.getElementById('pause-story').classList.add('hidden');
        };

        speechSynthesis.speak(utterance);
        currentUtterance = utterance;
    }

    // Botón de reinicio
    document.getElementById('reset-story').addEventListener('click', function() {
        // Restaurar palabras disponibles
        document.querySelectorAll('.draggable-word').forEach(word => {
            word.style.opacity = '1';
            word.style.pointerEvents = 'auto';
        });

        // Limpiar espacios en blanco
        document.querySelectorAll('.drop-target').forEach(target => {
            target.innerHTML = '______';
            target.dataset.word = '';
        });

        // Detener cualquier audio en reproducción
        if (speechSynthesis.speaking) {
            speechSynthesis.cancel();
        }
    });

    // Botón para completar historia
    document.getElementById('save-story').addEventListener('click', function() {
        // Verificar que todos los espacios tengan palabra
        let allFilled = true;
        document.querySelectorAll('.drop-target').forEach(target => {
            if (!target.dataset.word || target.dataset.word === '') {
                allFilled = false;
                target.classList.add('bg-red-100', 'border-red-500');
                setTimeout(() => {
                    target.classList.remove('bg-red-100', 'border-red-500');
                }, 2000);
            }
        });

        if (!allFilled) {
            speakText("Por favor, completa todos los espacios en blanco.");
            return;
        }

        // Construir historia final
        let finalStory = `<?= addslashes($game_data['template']) ?>`;
        document.querySelectorAll('.drop-target').forEach(target => {
            const word = target.dataset.word;
            const category = target.dataset.category;
            finalStory = finalStory.replace(`{${category}}`, `<strong class="text-blue-700">${word}</strong>`);
        });

        // Mostrar resultado
        document.getElementById('final-story').innerHTML = finalStory.replace(/\n/g, '<br>');
        document.getElementById('result-container').classList.remove('hidden');

        // Desplazarse al resultado
        document.getElementById('result-container').scrollIntoView({
            behavior: 'smooth'
        });

        // Guardar progreso
        fetch('../../api/save-progress.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                game: 'interactive-story',
                level: <?= $game_data['level'] ?>,
                story_id: <?= $game_data['id'] ?>,
                completed: true
            })
        });
    });

    // Leer la historia completa
    document.getElementById('read-story').addEventListener('click', function() {
        const storyText = document.getElementById('final-story').textContent;
        speakText(storyText);
    });

    // Pausar/reanudar la lectura
    document.getElementById('pause-story').addEventListener('click', function() {
        if (speechSynthesis.speaking && !speechSynthesis.paused) {
            speechSynthesis.pause();
            this.innerHTML = '<i class="fas fa-play"></i> Reanudar';
        } else if (speechSynthesis.paused) {
            speechSynthesis.resume();
            this.innerHTML = '<i class="fas fa-pause"></i> Pausar';
        }
    });

    // Nueva historia
    document.getElementById('new-story').addEventListener('click', function() {
        location.reload();
    });

    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', () => {
        initializeDragAndDrop();
        initializeTTS();

        // Cargar voces cuando estén disponibles
        if (speechSynthesis.onvoiceschanged !== undefined) {
            speechSynthesis.onvoiceschanged = loadVoices;
        }

        // Inicializar TTS después de cargar las voces
        setTimeout(loadVoices, 500);
    });
</script>

<style>
    .draggable-word {
        transition: all 0.3s;
    }

    .dragging {
        opacity: 0.5;
        transform: scale(1.05);
    }

    .drag-over {
        background-color: #DBEAFE;
        border-color: #3B82F6 !important;
    }

    .word-placed {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: relative;
        padding-right: 24px;
    }

    .delete-word {
        position: absolute;
        top: 2px;
        right: 2px;
        font-size: 12px;
        padding: 2px;
    }

    @media (max-width: 640px) {
        .draggable-word {
            width: 120px;
            padding: 10px;
        }

        .draggable-word img {
            width: 60px;
            height: 60px;
        }

        .drop-target {
            min-width: 80px;
        }
    }
</style>
<?php
$content = ob_get_clean();
include '../../includes/game_layout.php';
?>