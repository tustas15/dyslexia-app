<?php ob_start(); ?>

<div class="max-w-3xl mx-auto px-4 py-10">
    <div class="game-header flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-blue-700">Detective de Letras</h1>
            <p class="text-gray-600">Nivel <?= $game_data['level'] ?></p>
        </div>
        <div class="stats flex gap-4">
            <div class="bg-blue-100 px-4 py-2 rounded-lg">
                <span class="text-blue-700 font-bold" id="score">0</span> pts
            </div>
            <div class="bg-red-100 px-4 py-2 rounded-lg">
                <span class="text-red-700 font-bold" id="lives">3</span> vidas
            </div>
        </div>
    </div>

    <div class="game-container bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-center mb-8">Selecciona la letra correcta:</h2>
        
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
            <p class="text-gray-500 mb-4">Pares completados: <span id="progress">0</span>/10</p>
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
        <h2 class="text-2xl font-bold text-green-700 mb-4">¡Nivel Completado!</h2>
        <p class="text-lg mb-6">Puntuación: <span id="final-score">0</span> puntos</p>
        <div class="flex justify-center gap-4">
            <button id="replay-btn" class="bg-blue-500 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-600 transition">
                Repetir nivel
            </button>
            <?php if ($game_data['level'] < 3): ?>
                <button id="next-level-btn" class="bg-green-500 text-white px-6 py-3 rounded-lg shadow hover:bg-green-600 transition">
                    Siguiente nivel
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
    let currentPair = 0;
    let score = 0;
    let lives = gameData.lives;
    let pairsCompleted = 0;

    document.addEventListener('DOMContentLoaded', () => {
        updateGameStats();
        
        document.querySelectorAll('.letter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                checkAnswer(this.dataset.letter);
            });
        });

        document.querySelector('.next-btn').addEventListener('click', nextPair);
        document.querySelector('#replay-btn').addEventListener('click', () => location.reload());
        document.querySelector('#next-level-btn').addEventListener('click', () => {
            location.href = `?level=${gameData.level + 1}`;
        });
        document.querySelector('#return-btn').addEventListener('click', () => {
            location.href = '../../index.php';
        });
    });

    function checkAnswer(selectedLetter) {
        const correctLetter = gameData.pairs[currentPair].correct_letter;
        const isCorrect = selectedLetter === correctLetter;
        
        // Actualizar estado del juego
        if (isCorrect) {
            score += gameData.level * 5; // Más puntos en niveles más altos
            pairsCompleted++;
            showFeedback(true, correctLetter);
        } else {
            lives--;
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
        
        updateGameStats();
    }

    function showFeedback(isCorrect, correctLetter) {
        const feedback = document.querySelector('.feedback');
        const resultIcon = document.querySelector('.result-icon');
        const message = document.querySelector('.message');
        
        if (isCorrect) {
            resultIcon.className = 'result-icon bg-green-100 text-green-500';
            resultIcon.innerHTML = '<i class="fas fa-check"></i>';
            message.textContent = '¡Correcto! Has seleccionado la letra correcta.';
        } else {
            resultIcon.className = 'result-icon bg-red-100 text-red-500';
            resultIcon.innerHTML = '<i class="fas fa-times"></i>';
            message.textContent = `Incorrecto. La letra correcta era "${correctLetter}".`;
        }
        
        feedback.classList.remove('hidden');
    }

    function nextPair() {
        currentPair++;
        
        // Verificar si el juego ha terminado
        if (lives <= 0 || currentPair >= gameData.pairs.length || pairsCompleted >= 10) {
            endGame();
            return;
        }
        
        // Actualizar la interfaz para el próximo par
        document.querySelectorAll('.letter-btn').forEach((btn, index) => {
            btn.disabled = false;
            btn.classList.remove('border-green-500', 'bg-green-50', 'border-red-500', 'bg-red-50');
            btn.dataset.letter = gameData.pairs[currentPair][`letter${index+1}`];
            btn.textContent = gameData.pairs[currentPair][`letter${index+1}`];
        });
        
        document.querySelector('.feedback').classList.add('hidden');
        updateGameStats();
    }

    function endGame() {
        // Guardar progreso
        saveProgress();
        
        // Mostrar pantalla de finalización
        document.querySelector('.game-container').classList.add('hidden');
        document.querySelector('.feedback').classList.add('hidden');
        document.querySelector('.level-complete').classList.remove('hidden');
        document.querySelector('#final-score').textContent = score;
    }

    function updateGameStats() {
        document.querySelector('#score').textContent = score;
        document.querySelector('#lives').textContent = lives;
        document.querySelector('#progress').textContent = pairsCompleted;
        document.querySelector('#progress-bar').style.width = `${(pairsCompleted / 10) * 100}%`;
    }

    function saveProgress() {
        const gameType = 'letter-detective';
        
        // Guardar puntuación final
        fetch('../../api/save-progress.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                game: gameType,
                level: gameData.level,
                final_score: score,
                correct_answers: pairsCompleted,
                total_pairs: gameData.pairs.length,
                lives_remaining: lives
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
    
    .feedback {
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }
    
    .level-complete {
        animation: scaleIn 0.5s forwards;
    }
    
    @keyframes scaleIn {
        from { transform: scale(0.8); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
</style>