<?php
// Verificar si se cargaron los datos correctamente
if (!isset($game_data)) {
    die("Error: Datos del juego no disponibles.");
}

$page_title = "Pintando Palabras - Nivel $level";
$game_name = "Pintando Palabras";
$game_type = "word-painting";

// Definir variables necesarias
$word = $game_data['word'] ?? 'palabra';
$syllables = $game_data['syllables'] ?? [];
$syllable_colors = $game_data['syllable_colors'] ?? [];
$letters = $game_data['letters'] ?? [];
$level = $level ?? 1;

// Calcular el número de letras objetivo (la palabra)
$target_letter_count = count(str_split($word));
?>

<?php ob_start(); ?>

<div class="container mx-auto px-4 py-8 max-w-4xl">
    <h1 class="text-3xl font-bold text-center text-blue-700 mb-2">Pintando Palabras <span class="text-xl text-gray-600">Nivel <?= $level ?></span></h1>

    <div class="bg-blue-50 rounded-xl p-6 mb-6 flex flex-col sm:flex-row justify-between items-center">
        <div class="text-lg font-semibold text-gray-800 mb-4 sm:mb-0">
            <span>Palabra: </span>
            <span id="target-word-text" class="text-2xl"><?= htmlspecialchars($word) ?></span>
        </div>
        <button id="play-word" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition flex items-center">
            <i class="fas fa-volume-up mr-2"></i> Escuchar palabra
        </button>
    </div>

    <div class="bg-yellow-50 rounded-xl p-4 mb-6 text-center">
        <p class="text-gray-700 mb-2">Encuentra las letras de la palabra y colorealas con el color de cada sílaba</p>
        <button id="play-instructions" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg transition flex items-center mx-auto">
            <i class="fas fa-volume-up mr-2"></i> Escuchar instrucciones
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <h3 class="text-lg font-bold text-center mb-4">Guía de sílabas:</h3>
        <div class="flex flex-wrap justify-center gap-4">
            <?php foreach ($syllables as $index => $syllable): ?>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-md border-2 border-gray-200" style="background-color: <?= $syllable_colors[$index] ?? '#ccc' ?>;"></div>
                    <span class="ml-2 font-semibold"><?= $syllable ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-4 mb-6">
        <?php foreach ($letters as $letter): ?>
            <div class="letter-box flex items-center justify-center w-16 h-16 border-2 border-blue-400 rounded-lg text-3xl font-bold cursor-pointer transition-all duration-200 hover:shadow-lg"
                data-letter="<?= htmlspecialchars($letter['char']) ?>"
                data-position="<?= $letter['position'] ?? '' ?>"
                data-color="<?= $letter['color'] ?>"
                data-target="<?= $letter['is_target'] ? 'true' : 'false' ?>">
                <?= htmlspecialchars($letter['char']) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="flex flex-wrap justify-center gap-4 mb-6">
        <?php foreach ($syllable_colors as $color): ?>
            <div class="color w-12 h-12 rounded-full cursor-pointer shadow-md transition-all duration-200 hover:scale-110"
                style="background-color: <?= $color ?>;"
                data-color="<?= $color ?>"></div>
        <?php endforeach; ?>
    </div>

    <div class="flex justify-center gap-4 mb-6">
        <button id="check-btn" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition">
            Comprobar
        </button>
        <button id="reset-btn" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition">
            Reiniciar
        </button>
    </div>

    <div id="feedback" class="text-center hidden">
        <div id="result-icon" class="text-5xl mb-4"></div>
        <p id="message" class="text-xl mb-4"></p>
        <button id="next-btn" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition">
            Siguiente
        </button>
    </div>
</div>

<script>
    // Variables globales
    const targetWord = "<?= $word ?>";
    let currentColor = '';
    let coloredCount = 0;
    const targetLetterCount = <?= $target_letter_count ?>;
    let isSpeaking = false;

    // Función para hablar texto usando Web Speech API
    function speakText(text) {
        if ('speechSynthesis' in window) {
            // Cancelar cualquier habla en progreso
            if (isSpeaking) {
                speechSynthesis.cancel();
            }

            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'es-ES'; // Idioma español
            utterance.rate = 0.9; // Velocidad un poco más lenta para niños
            utterance.pitch = 1.0; // Tono normal

            // Seleccionar una voz en español si está disponible
            const voices = speechSynthesis.getVoices();
            const spanishVoice = voices.find(voice =>
                voice.lang.startsWith('es') && voice.name.includes('Natural')
            );

            if (spanishVoice) {
                utterance.voice = spanishVoice;
            }

            // Actualizar estado
            isSpeaking = true;
            const playBtn = text === targetWord ? document.getElementById('play-word') : document.getElementById('play-instructions');
            const originalContent = playBtn.innerHTML;
            playBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Hablando...';
            playBtn.disabled = true;

            utterance.onend = function() {
                isSpeaking = false;
                playBtn.innerHTML = originalContent;
                playBtn.disabled = false;
            };

            utterance.onerror = function(event) {
                isSpeaking = false;
                playBtn.innerHTML = originalContent;
                playBtn.disabled = false;
                console.error('Error en síntesis de voz:', event.error);
            };

            speechSynthesis.speak(utterance);
        } else {
            alert('Tu navegador no soporta la función de texto a voz. Por favor usa Chrome, Edge o Safari.');
        }
    }

    // Reproducir palabra
    document.getElementById('play-word').addEventListener('click', () => {
        speakText(targetWord);
    });

    // Reproducir instrucciones
    document.getElementById('play-instructions').addEventListener('click', () => {
        speakText("Encuentra las letras de la palabra y colorealas con el color de cada sílaba");
    });

    // Verificar soporte de TTS al cargar la página
    document.addEventListener('DOMContentLoaded', () => {
        if (!('speechSynthesis' in window)) {
            document.getElementById('play-word').disabled = true;
            document.getElementById('play-instructions').disabled = true;
            alert('La función de texto a voz no está disponible en tu navegador. Algunas características pueden no funcionar correctamente.');
        }

        // Cargar voces disponibles (necesario para Firefox)
        if (speechSynthesis.onvoiceschanged !== undefined) {
            speechSynthesis.onvoiceschanged = () => {
                // Voces cargadas, no necesitamos hacer nada especial
            };
        }
    });

    // Seleccionar color
    document.querySelectorAll('.color').forEach(colorElement => {
        colorElement.addEventListener('click', () => {
            currentColor = colorElement.dataset.color;
            document.querySelectorAll('.color').forEach(c => {
                c.classList.remove('ring-4', 'ring-offset-2', 'ring-blue-400');
            });
            colorElement.classList.add('ring-4', 'ring-offset-2', 'ring-blue-400');
        });
    });

    // Seleccionar letra
    function selectLetter(letterBox) {
        if (!currentColor) {
            alert('Por favor selecciona un color primero');
            return;
        }

        const position = letterBox.dataset.position;
        const isTarget = letterBox.dataset.target === 'true';

        // Solo permitir colorear letras objetivo
        if (isTarget) {
            letterBox.style.backgroundColor = currentColor;
            letterBox.dataset.colored = "true";
            letterBox.dataset.userColor = currentColor; // Guardar color asignado
            coloredCount++;

            if (coloredCount === targetLetterCount) {
                checkSolution();
            }
        } else {
            letterBox.classList.add('animate-shake');
            setTimeout(() => {
                letterBox.classList.remove('animate-shake');
            }, 1000);
        }
    }

    // Asignar evento a las letras
    document.querySelectorAll('.letter-box').forEach(letterBox => {
        letterBox.addEventListener('click', () => {
            selectLetter(letterBox);
        });
    });

    // Comprobar solución
    document.getElementById('check-btn').addEventListener('click', checkSolution);

    function checkSolution() {
        // Verificar si todas las letras objetivo están coloreadas
        const coloredBoxes = document.querySelectorAll('.letter-box[data-target="true"][data-colored="true"]');


        // Verificar si los colores son correctos
        let allColored = true;
        let colorsCorrect = true;

        document.querySelectorAll('.letter-box[data-target="true"]').forEach(box => {
            const correctColor = box.dataset.color;
            const userColor = box.dataset.userColor || '';

            if (!box.dataset.colored) {
                allColored = false;
            } else {
                const normalizedCorrect = normalizeColor(correctColor);
                const normalizedUser = normalizeColor(userColor);

                if (normalizedUser !== normalizedCorrect) {
                    colorsCorrect = false;
                    box.classList.add('border-red-500', 'border-4');
                }
            }
        });

        const resultIcon = document.getElementById('result-icon');
        const message = document.getElementById('message');
        const nextBtn = document.getElementById('next-btn');
        const feedback = document.getElementById('feedback');

        feedback.classList.remove('hidden');

        if (allColored && colorsCorrect) {
            resultIcon.className = 'text-green-500 fas fa-check-circle';
            message.textContent = '¡Perfecto! Has coloreado todas las letras correctamente.';
            nextBtn.classList.remove('hidden');

            // Guardar progreso
            saveProgress(true);
        } else {
            resultIcon.className = 'text-red-500 fas fa-times-circle';

            if (!allColored) {
                message.textContent = `¡Faltan letras! ${coloredCount} de ${targetLetterCount} coloreadas.`;
            } else {
                message.textContent = '¡Colores incorrectos! Revisa la guía de sílabas.';
            }
        }
    }

    // Función para normalizar formatos de color
    function normalizeColor(color) {
        if (!color) return '';
        // Convertir todos los formatos a RGB sin espacios
        if (color.startsWith('#')) {
            return hexToRgb(color).replace(/\s+/g, '');
        }
        if (color.startsWith('rgb')) {
            return color.replace(/\s+/g, '').toLowerCase();
        }
        return color.replace(/\s+/g, '').toLowerCase();
    }

    // Función para convertir HEX a RGB
    function hexToRgb(hex) {
        hex = hex.replace(/^#/, '');
        const bigint = parseInt(hex, 16);
        const r = (bigint >> 16) & 255;
        const g = (bigint >> 8) & 255;
        const b = bigint & 255;
        return `rgb(${r},${g},${b})`;
    }

    // Mejorar el manejo de TTS
    let lastPlayTime = 0;
    const minPlayDelay = 1000; // 1 segundo entre reproducciones

    function speakText(text) {
        if ('speechSynthesis' in window) {
            // Cancelar cualquier habla en progreso
            if (isSpeaking) {
                speechSynthesis.cancel();
            }

            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'es-ES';
            utterance.rate = 0.9;
            utterance.pitch = 1.0;

            // Seleccionar cualquier voz en español disponible
            const voices = speechSynthesis.getVoices();
            const spanishVoice = voices.find(voice =>
                voice.lang.startsWith('es')
            );

            if (spanishVoice) {
                utterance.voice = spanishVoice;
            }

            // Actualizar estado
            isSpeaking = true;
            const playBtn = text === targetWord ?
                document.getElementById('play-word') :
                document.getElementById('play-instructions');

            const originalContent = playBtn.innerHTML;
            playBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Hablando...';
            playBtn.disabled = true;

            utterance.onend = function() {
                isSpeaking = false;
                playBtn.innerHTML = originalContent;
                playBtn.disabled = false;
            };

            utterance.onerror = function(event) {
                // Ignorar errores "interrupted" (usuario canceló)
                if (event.error !== 'interrupted') {
                    console.error('Error en síntesis de voz:', event.error);
                }
                isSpeaking = false;
                playBtn.innerHTML = originalContent;
                playBtn.disabled = false;
            };

            speechSynthesis.speak(utterance);
        } else {
            alert('Tu navegador no soporta la función de texto a voz. Por favor usa Chrome, Edge o Safari.');
        }
    }

    // Reiniciar
    document.getElementById('reset-btn').addEventListener('click', () => {
        document.querySelectorAll('.letter-box').forEach(box => {
            box.style.backgroundColor = '';
            delete box.dataset.colored;
            box.classList.remove('border-red-500', 'border-4', 'animate-shake');
        });
        coloredCount = 0;
        document.querySelectorAll('.color').forEach(c => {
            c.classList.remove('ring-4', 'ring-offset-2', 'ring-blue-400');
        });
        currentColor = '';
        feedback.classList.add('hidden');
    });

    // Guardar progreso
    function saveProgress(isCorrect) {
        fetch('../../api/save-progress.php', { // Corregido a 2 niveles
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                game: 'word-painting',
                level: <?= $level ?>,
                correct: isCorrect,
                word: targetWord
            })
        }).then(response => {
            if (!response.ok) {
                console.error('Error en la respuesta del servidor');
                return response.text(); // Leer respuesta como texto para depuración
            }
            return response.json();
        }).then(data => {
            if (data) {
                console.log('Progreso guardado:', data);
            }
        }).catch(error => {
            console.error('Error al guardar progreso:', error);
        });
    }

    // Siguiente palabra
    document.getElementById('next-btn').addEventListener('click', () => {
        window.location.reload();
    });
</script>

<style>
    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-5px);
        }

        50% {
            transform: translateX(5px);
        }

        75% {
            transform: translateX(-5px);
        }
    }

    .animate-shake {
        animation: shake 0.5s ease-in-out;
    }
</style>

<?php
$content = ob_get_clean();
include '../../includes/game_layout.php';
?>