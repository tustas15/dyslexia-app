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
                     style="width: <?= ($game_data['words_completed'] / $game_data['words_per_level']) * 100 ?>%">
                </div>
            </div>
        </div>
    </div>

    <!-- Botón de audio -->
    <div class="flex justify-center mb-8">
        <button id="play-btn" class="audio-btn bg-red-500 text-white text-lg sm:text-xl px-6 py-3 rounded-lg shadow hover:bg-red-600 transition w-full max-w-xs sm:max-w-none">
            <i class="fas fa-volume-up mr-2"></i> Repetir audio
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
    const wordAudio = new Howl({
        src: ['<?= $game_data['audio'] ?>'],
        html5: true
    });

    document.getElementById('play-btn').addEventListener('click', () => {
        wordAudio.play();
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

        // Guardar progreso
        fetch('../../api/save-progress.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                game: 'auditory-codes',
                correct: isCorrect,
                level: <?= $game_data['level'] ?>,
                word: '<?= $game_data['word'] ?>',
                selected: btn.textContent
            })
        });
    }

    // Manejar siguiente palabra
    document.querySelector('.next-btn').addEventListener('click', () => {
        location.reload(); // Recargar para nueva palabra
    });
</script>

<style>
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
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
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