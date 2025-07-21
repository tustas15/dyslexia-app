<div class="game-container auditory">
  <h1>Rompecódigos Auditivos <small>Nivel <?= $game_data['level'] ?></small></h1>
  
  <div class="audio-box">
    <button class="audio-btn" id="play-btn">
      <i class="fas fa-volume-up"></i> Repetir audio
    </button>
  </div>
  
  <div class="options-grid">
    <?php foreach($game_data['options'] as $index => $opt): ?>
      <button class="option" 
              data-correct="<?= $opt['correct'] ? '1' : '0' ?>"
              data-index="<?= $index ?>"
              onclick="checkAnswer(this)">
        <?= htmlspecialchars($opt['text']) ?>
      </button>
    <?php endforeach; ?>
  </div>
  
  <div class="feedback">
    <div class="feedback-content">
      <span class="result-icon"></span>
      <p class="message"></p>
    </div>
    <button class="next-btn hidden" onclick="location.reload()">Siguiente palabra</button>
  </div>
</div>

<script>
// Audio global para esta palabra
const wordAudio = new Howl({
    src: ['<?= $game_data['audio'] ?>'],
    html5: true
});

document.getElementById('play-btn').addEventListener('click', () => {
    wordAudio.play();
});

function checkAnswer(btn) {
    const isCorrect = btn.dataset.correct === "1";
    
    // Feedback visual
    btn.classList.add(isCorrect ? 'correct' : 'incorrect');
    
    // Deshabilitar todos los botones después de responder
    document.querySelectorAll('.option').forEach(optionBtn => {
        optionBtn.disabled = true;
        if (optionBtn.dataset.correct === "1") {
            optionBtn.classList.add('correct-answer');
        }
    });
    
    // Mostrar resultado
    const resultIcon = document.querySelector('.result-icon');
    const message = document.querySelector('.message');
    resultIcon.className = 'result-icon ' + (isCorrect ? 'correct' : 'incorrect');
    resultIcon.innerHTML = isCorrect ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>';
    
    message.textContent = isCorrect ? 
        '¡Correcto! La palabra es <?= htmlspecialchars($game_data['word']) ?>' : 
        'Incorrecto. La palabra correcta es "<?= htmlspecialchars($game_data['word']) ?>"';
    
    const nextBtn = document.querySelector('.next-btn');
    nextBtn.classList.remove('hidden');
    
    // Guardar progreso
    fetch('/api/save-progress.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            game: 'auditory-codes',
            correct: isCorrect,
            level: <?= $game_data['level'] ?>,
            word: '<?= $game_data['word'] ?>'
        })
    });
}
</script>

<style>
.options-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin: 25px 0;
}

.option {
    padding: 20px;
    font-size: 1.4rem;
    border: 3px solid #4e89ae;
    border-radius: 15px;
    background-color: white;
    cursor: pointer;
    transition: all 0.3s;
    font-family: 'OpenDyslexic', sans-serif;
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
    padding: 15px 30px;
    font-size: 1.2rem;
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

.next-btn {
    margin-top: 20px;
    font-size: 1.2rem;
    padding: 12px 30px;
}
</style>