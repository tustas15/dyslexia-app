<?php ob_start(); ?>
<div class="game-container detective">
  <h1>Detective de Letras <small>Nivel <?= $game_data['level'] ?></small></h1>
  <p class="instructions">Selecciona la letra que está escrita correctamente</p>
  
  <div class="progress-bar">
    <div class="progress-fill" id="progress-fill"></div>
  </div>
  
  <div class="letter-pairs">
    <?php foreach($game_data['pairs'] as $index => $pair): ?>
      <div class="pair" data-pair-id="<?= $pair['id'] ?>" <?= $index > 0 ? 'style="display:none;"' : '' ?>>
        <div class="letter" data-letter="<?= $pair['letter1'] ?>" 
             onclick="checkLetter(this, '<?= $pair['letter1'] ?>', '<?= $pair['correct'] ?>')">
          <?= $pair['letter1'] ?>
        </div>
        <div class="vs">VS</div>
        <div class="letter" data-letter="<?= $pair['letter2'] ?>" 
             onclick="checkLetter(this, '<?= $pair['letter2'] ?>', '<?= $pair['correct'] ?>')">
          <?= $pair['letter2'] ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  
  <div class="feedback">
    <div class="result-icon"></div>
    <p class="message"></p>
    <button class="next-btn hidden" onclick="nextPair()">Siguiente</button>
  </div>
</div>

<script>
let currentPairIndex = 0;
const pairs = document.querySelectorAll('.pair');
const totalPairs = pairs.length;
let correctAnswers = 0;

// Actualizar barra de progreso
function updateProgress() {
    const progressFill = document.getElementById('progress-fill');
    const progress = (currentPairIndex / totalPairs) * 100;
    progressFill.style.width = `${progress}%`;
}

function checkLetter(element, letter, correctLetter) {
    const isCorrect = (letter === correctLetter);
    
    // Feedback visual
    if (isCorrect) {
        element.classList.add('correct');
        element.classList.remove('incorrect');
        correctAnswers++;
    } else {
        element.classList.add('incorrect');
        element.classList.remove('correct');
        
        // Marcar la correcta
        const correctElement = element.parentElement.querySelector(`.letter[data-letter="${correctLetter}"]`);
        correctElement.classList.add('correct');
    }
    
    // Deshabilitar clics
    const letters = element.parentElement.querySelectorAll('.letter');
    letters.forEach(letter => {
        letter.style.cursor = 'default';
        letter.onclick = null;
    });
    
    // Mostrar feedback
    const resultIcon = document.querySelector('.result-icon');
    const message = document.querySelector('.message');
    const nextBtn = document.querySelector('.next-btn');
    
    resultIcon.className = 'result-icon ' + (isCorrect ? 'correct' : 'incorrect');
    resultIcon.innerHTML = isCorrect ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>';
    message.textContent = isCorrect ? 
        '¡Correcto! La letra "' + correctLetter + '" está bien escrita' : 
        '¡Incorrecto! La letra correcta es "' + correctLetter + '"';
    nextBtn.classList.remove('hidden');
    
    // Guardar progreso
    saveProgress(isCorrect, '<?= $game_data['level'] ?>', letter, correctLetter);
}

function nextPair() {
    currentPairIndex++;
    
    if (currentPairIndex < totalPairs) {
        // Ocultar el par actual
        pairs[currentPairIndex - 1].style.display = 'none';
        
        // Mostrar el siguiente par
        pairs[currentPairIndex].style.display = 'flex';
        
        // Resetear estilos
        const letters = pairs[currentPairIndex].querySelectorAll('.letter');
        letters.forEach(letter => {
            letter.classList.remove('correct', 'incorrect');
            letter.style.cursor = 'pointer';
        });
        
        // Ocultar feedback
        document.querySelector('.result-icon').className = 'result-icon';
        document.querySelector('.message').textContent = '';
        document.querySelector('.next-btn').classList.add('hidden');
        
        // Actualizar progreso
        updateProgress();
    } else {
        // Fin del juego
        const score = Math.round((correctAnswers / totalPairs) * 100);
        const message = document.querySelector('.message');
        message.innerHTML = `¡Felicidades!<br>Completaste ${correctAnswers} de ${totalPairs} correctamente.<br>Puntuación: ${score}%`;
        
        // Guardar puntuación final
        saveFinalScore(score);
        
        // Mostrar botón de volver
        const nextBtn = document.querySelector('.next-btn');
        nextBtn.textContent = 'Volver al Menú';
        nextBtn.onclick = function() {
            window.location.href = '../../index.php';
        };
        
        // Ocultar el par actual
        pairs[currentPairIndex - 1].style.display = 'none';
    }
}

function saveProgress(isCorrect, level, selected, correct) {
    fetch('/api/save-progress.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            game: 'letter-detective',
            level: level,
            correct: isCorrect,
            selected: selected,
            correctLetter: correct
        })
    });
}

function saveFinalScore(score) {
    fetch('/api/save-progress.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            game: 'letter-detective',
            level: <?= $game_data['level'] ?>,
            final_score: score,
            correct_answers: correctAnswers,
            total_pairs: totalPairs
        })
    });
}

// Inicializar barra de progreso
updateProgress();
</script>

<style>
.game-container.detective {
    max-width: 600px;
    margin: 0 auto;
    text-align: center;
    padding: 20px;
}

.instructions {
    font-size: 1.2rem;
    margin-bottom: 20px;
    color: #43658b;
}

.progress-bar {
    height: 20px;
    background-color: #e3f2fd;
    border-radius: 10px;
    margin-bottom: 30px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background-color: #4e89ae;
    border-radius: 10px;
    width: 0%;
    transition: width 0.5s ease;
}

.letter-pairs {
    margin-top: 30px;
}

.pair {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 30px;
    margin-bottom: 30px;
}

.letter {
    width: 150px;
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 5rem;
    font-weight: bold;
    border: 4px solid #4e89ae;
    border-radius: 15px;
    cursor: pointer;
    background-color: white;
    transition: all 0.3s;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.letter:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.letter.correct {
    background-color: #d4edda;
    border-color: #28a745;
    color: #28a745;
    box-shadow: 0 0 20px rgba(40, 167, 69, 0.4);
}

.letter.incorrect {
    background-color: #f8d7da;
    border-color: #dc3545;
    color: #dc3545;
}

.vs {
    font-size: 2rem;
    font-weight: bold;
    color: #ed6663;
}

.feedback {
    margin-top: 30px;
    padding: 20px;
    border-radius: 15px;
    background-color: #f8f9fa;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

.result-icon {
    font-size: 3rem;
    margin-bottom: 15px;
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.result-icon.correct {
    background-color: #d4edda;
    color: #28a745;
}

.result-icon.incorrect {
    background-color: #f8d7da;
    color: #dc3545;
}

.message {
    font-size: 1.3rem;
    margin-bottom: 20px;
    line-height: 1.6;
}

.next-btn {
    background-color: #4e89ae;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 50px;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.3s;
    min-width: 150px;
}

.next-btn:hover {
    background-color: #43658b;
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

@media (max-width: 600px) {
    .pair {
        flex-direction: column;
        gap: 20px;
    }
    
    .letter {
        width: 120px;
        height: 120px;
        font-size: 4rem;
    }
    
    .vs {
        transform: rotate(90deg);
    }
}
</style>
<?php
$content = ob_get_clean();
include '../../includes/game_layout.php';
?>
