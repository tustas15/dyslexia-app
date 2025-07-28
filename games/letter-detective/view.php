<?php ob_start(); ?>

<div class="max-w-3xl mx-auto px-4 py-10">
    <div class="game-header flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-blue-700">Detective de Letras</h1>
            <p class="text-gray-600">Nivel <?= $game_data['level'] ?></p>
        </div>
        <div class="stats flex gap-4">
            <div class="bg-blue-100 px-4 py-2 rounded-lg">
                <span id="current-pair">1</span>/<?= count($game_data['pairs']) ?> pares
            </div>
            <div class="bg-green-100 px-4 py-2 rounded-lg">
                <span id="score">0</span> puntos
            </div>
        </div>
    </div>

    <div class="game-container bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-center mb-4">Selecciona la letra: 
            <span id="target-letter" class="text-3xl text-blue-600"><?= $game_data['pairs'][0]['correct_letter'] ?></span>
        </h2>
        
        <!-- Botón de Text-to-Speech -->
        <div class="text-center mb-6">
            <button id="speech-btn" class="bg-blue-500 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-volume-up mr-2"></i> Escuchar letra
            </button>
        </div>
        
        <div class="letters flex justify-center gap-8 mb-8">
            <button class="letter-btn text-8xl w-40 h-40 border-4 border-blue-400 rounded-xl bg-white hover:bg-blue-50 transition" 
                     data-letter="<?= $game_data['pairs'][0]['letter1'] ?>">
                <?= $game_data['pairs'][0]['letter1'] ?>
            </button>
            <button class="letter-btn text-8xl w-40 h-40 border-4 border-blue-400 rounded-xl bg-white hover:bg-blue-50 transition" 
                     data-letter="<?= $game_data['pairs'][0]['letter2'] ?>">
                <?= $game_data['pairs'][0]['letter2'] ?>
            </button>
        </div>

        <div class="text-center">
            <p class="text-gray-500 mb-4">Progreso del nivel</p>
            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                <div id="progress-bar" class="bg-blue-600 h-4 transition-all duration-500" style="width: 0%"></div>
            </div>
        </div>
    </div>

    <div class="feedback hidden text-center mb-8">
        <div class="feedback-content p-4 rounded-lg mb-4">
            <span class="result-icon flex items-center justify-center w-16 h-16 rounded-full text-3xl mx-auto mb-3"></span>
            <p class="message text-lg font-medium text-gray-700"></p>
        </div>
        <button class="next-btn bg-blue-500 text-white text-lg px-6 py-3 rounded-lg shadow hover:bg-blue-600 transition">
            Siguiente par
        </button>
    </div>

    <div class="level-complete hidden text-center p-8 bg-green-50 rounded-xl">
        <h2 class="text-2xl font-bold text-green-700 mb-4">¡Nivel <?= $game_data['level'] ?> Completado!</h2>
        <p class="text-lg mb-6">Puntuación: <span id="final-score">0</span> puntos</p>
        <div class="flex justify-center gap-4">
            <button id="replay-btn" class="bg-blue-500 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-600 transition">
                Repetir nivel
            </button>
            <?php if ($game_data['level'] < 3): ?>
                <button id="next-level-btn" class="bg-green-500 text-white px-6 py-3 rounded-lg shadow hover:bg-green-600 transition">
                    Nivel <?= $game_data['level'] + 1 ?>
                </button>
            <?php else: ?>
                <button id="return-btn" class="bg-gray-500 text-white px-6 py-3 rounded-lg shadow hover:bg-gray-600 transition">
                    Volver al inicio
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../../includes/game_layout.php';
?>

<script>
    const gameData = <?= json_encode($game_data) ?>;
    let currentPairIndex = 0;
    let score = 0;
    let pairsCompleted = 0;

    document.addEventListener('DOMContentLoaded', () => {
        updateProgress();
        
        // Configurar Text-to-Speech
        const speechBtn = document.getElementById('speech-btn');
        speechBtn.addEventListener('click', speakLetter);
        
        // Pronunciar letra al inicio
        speakLetter();
        
        document.querySelectorAll('.letter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                checkAnswer(this.dataset.letter);
            });
        });

        document.querySelector('.next-btn').addEventListener('click', nextPair);
        document.querySelector('#replay-btn').addEventListener('click', () => location.reload());
        document.querySelector('#next-level-btn').addEventListener('click', () => {
            location.href = `index.php?level=${gameData.level + 1}`;
        });
        document.querySelector('#return-btn').addEventListener('click', () => {
            location.href = '../../index.php';
        });
    });

    function speakLetter() {
        const letter = gameData.pairs[currentPairIndex].correct_letter;
        const utterance = new SpeechSynthesisUtterance(letter);
        utterance.lang = 'es-ES';
        utterance.rate = 0.8;
        speechSynthesis.speak(utterance);
    }

    function checkAnswer(selectedLetter) {
        const correctLetter = gameData.pairs[currentPairIndex].correct_letter;
        const isCorrect = selectedLetter === correctLetter;
        
        if (isCorrect) {
            score += gameData.level * 10;
            pairsCompleted++;
            showFeedback(true);
        } else {
            showFeedback(false, correctLetter);
        }
        
        // Deshabilitar botones
        document.querySelectorAll('.letter-btn').forEach(btn => {
            btn.disabled = true;
            if (btn.dataset.letter === correctLetter) {
                btn.classList.add('border-green-500', 'bg-green-50');
            } else if (btn.dataset.letter === selectedLetter && !isCorrect) {
                btn.classList.add('border-red-500', 'bg-red-50');
            }
        });
        
        updateProgress();
    }

    function showFeedback(isCorrect, correctLetter = '') {
        const feedback = document.querySelector('.feedback');
        const resultIcon = document.querySelector('.result-icon');
        const message = document.querySelector('.message');
        
        if (isCorrect) {
            resultIcon.className = 'result-icon bg-green-100 text-green-500';
            resultIcon.innerHTML = '<i class="fas fa-check"></i>';
            message.textContent = '¡Correcto!';
        } else {
            resultIcon.className = 'result-icon bg-red-100 text-red-500';
            resultIcon.innerHTML = '<i class="fas fa-times"></i>';
            message.textContent = `La letra correcta era "${correctLetter}".`;
        }
        
        feedback.classList.remove('hidden');
    }

    function nextPair() {
        currentPairIndex++;
        
        // Verificar si se completó el nivel
        if (currentPairIndex >= gameData.pairs.length) {
            completeLevel();
            return;
        }
        
        // Actualizar la interfaz para el próximo par
        const nextPair = gameData.pairs[currentPairIndex];
        document.getElementById('target-letter').textContent = nextPair.correct_letter;
        
        const letterButtons = document.querySelectorAll('.letter-btn');
        letterButtons[0].dataset.letter = nextPair.letter1;
        letterButtons[0].textContent = nextPair.letter1;
        letterButtons[1].dataset.letter = nextPair.letter2;
        letterButtons[1].textContent = nextPair.letter2;
        
        // Reiniciar estilos y habilitar botones
        letterButtons.forEach(btn => {
            btn.disabled = false;
            btn.classList.remove('border-green-500', 'bg-green-50', 'border-red-500', 'bg-red-50');
        });
        
        document.querySelector('.feedback').classList.add('hidden');
        updateProgress();
        
        // Pronunciar nueva letra
        speakLetter();
    }

    function completeLevel() {
        // Guardar progreso
        saveProgress();
        
        // Mostrar pantalla de finalización
        document.querySelector('.game-container').classList.add('hidden');
        document.querySelector('.feedback').classList.add('hidden');
        document.querySelector('.level-complete').classList.remove('hidden');
        document.querySelector('#final-score').textContent = score;
    }

    function updateProgress() {
        const progressPercent = (currentPairIndex / gameData.pairs.length) * 100;
        document.querySelector('#progress-bar').style.width = `${progressPercent}%`;
        document.querySelector('#current-pair').textContent = currentPairIndex + 1;
        document.querySelector('#score').textContent = score;
    }

    function saveProgress() {
        const gameType = 'letter-detective';
        
        fetch('../../api/save-progress.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                game: gameType,
                level: gameData.level,
                score: score,
                details: {
                    pairs_completed: pairsCompleted,
                    total_pairs: gameData.pairs.length
                }
            })
        });
    }
</script>

<style>
    .letter-btn {
        font-family: 'OpenDyslexic', sans-serif;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .letter-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .letter-btn:disabled {
        cursor: not-allowed;
        opacity: 0.7;
    }
    
    #target-letter {
        font-family: 'OpenDyslexic', sans-serif;
        font-size: 2.5rem;
    }
</style>