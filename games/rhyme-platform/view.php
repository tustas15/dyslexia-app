<?php
// Verificar si se cargaron los datos correctamente
if (!isset($game_data)) {
    die("Error: Datos del juego no disponibles.");
}

$targetWord = $game_data['target_word'] ?? 'sol';
$words = $game_data['words'] ?? ['col', 'gol', 'pan', 'sal', 'pez'];
$rhymes = $game_data['rhymes'] ?? ['col', 'gol'];
$level = $game_data['level'] ?? 1;
?>
<?php ob_start(); ?>

<div class="game-container platform">
  <h1>Saltarima <small>Nivel <?= $level ?></small></h1>
  
  <div class="game-instructions">
    <p>¡Salta solo sobre las palabras que riman con la palabra objetivo!</p>
    <button class="audio-btn" id="play-instructions">
      <i class="fas fa-volume-up"></i> Escuchar instrucciones
    </button>
  </div>
  
  <div class="target-box">
    <p>Palabra objetivo:</p>
    <div class="target-word"><?= htmlspecialchars($targetWord) ?></div>
    <button class="audio-btn" id="play-target-word">
      <i class="fas fa-volume-up"></i>
    </button>
  </div>
  
  <div class="platform-game">
    <?php foreach($words as $word): ?>
      <?php $isRhyme = in_array($word, $rhymes); ?>
      <div class="platform" 
           data-rhyme="<?= $isRhyme ? '1' : '0' ?>"
           data-word="<?= htmlspecialchars($word) ?>"
           onclick="jump(this)">
        <span class="platform-word"><?= htmlspecialchars($word) ?></span>
      </div>
    <?php endforeach; ?>
  </div>
  
  <div class="character" id="character">🤸</div>
  
  <div class="feedback">
    <div class="result-icon"></div>
    <p class="message"></p>
    <button class="next-btn hidden" id="next-btn">Siguiente</button>
  </div>
  
  <div class="game-stats">
    <p>Puntuación: <span id="score">0</span></p>
    <p>Vidas: <span id="lives">3</span></p>
  </div>
</div>

<script>
// Estado del juego
let score = 0;
let lives = 3;
let currentLevel = <?= $level ?>;
let targetWord = "<?= $targetWord ?>";
let rhymes = <?= json_encode($rhymes) ?>;
let character = document.getElementById('character');

// Elementos DOM
const scoreElement = document.getElementById('score');
const livesElement = document.getElementById('lives');
const nextBtn = document.getElementById('next-btn');
const messageElement = document.querySelector('.message');
const resultIcon = document.querySelector('.result-icon');

// Audios
const targetWordAudio = new Howl({
    src: ['<?= get_audio('rhymes', $targetWord . '.mp3') ?>']
});

const instructionsAudio = new Howl({
    src: ['<?= get_audio('common', 'instructions_rhyme.mp3') ?>']
});

const successAudio = new Howl({
    src: ['<?= get_audio('common', 'success.mp3') ?>']
});

const errorAudio = new Howl({
    src: ['<?= get_audio('common', 'error.mp3') ?>']
});

// Reproducir instrucciones
document.getElementById('play-instructions').addEventListener('click', () => {
    instructionsAudio.play();
});

// Reproducir palabra objetivo
document.getElementById('play-target-word').addEventListener('click', () => {
    targetWordAudio.play();
});

function jump(platform) {
    const isRhyme = platform.dataset.rhyme === "1";
    const word = platform.dataset.word;
    
    // Animación de salto
    anime({
        targets: character,
        bottom: [0, platform.offsetTop],
        easing: 'easeOutQuad',
        duration: 800,
        complete: function() {
            if (isRhyme) {
                handleCorrectJump(platform, word);
            } else {
                handleIncorrectJump(platform, word);
            }
        }
    });
}

function handleCorrectJump(platform, word) {
    // Éxito
    platform.classList.add('activated');
    score += 10;
    scoreElement.textContent = score;
    
    // Feedback
    resultIcon.className = 'result-icon correct';
    resultIcon.innerHTML = '<i class="fas fa-check"></i>';
    messageElement.textContent = `¡Correcto! "${word}" rima con "${targetWord}"`;
    
    // Sonido
    successAudio.play();
    
    // Ocultar plataforma después de un tiempo
    setTimeout(() => {
        platform.style.visibility = 'hidden';
    }, 1000);
    
    // Verificar si todas las rimas están activadas
    const allRhymesActivated = document.querySelectorAll('.platform[data-rhyme="1"]:not(.activated)').length === 0;
    if (allRhymesActivated) {
        messageElement.textContent += ' ¡Nivel completado!';
        nextBtn.classList.remove('hidden');
    }
}

function handleIncorrectJump(platform, word) {
    // Error
    platform.classList.add('error');
    lives--;
    livesElement.textContent = lives;
    
    // Feedback
    resultIcon.className = 'result-icon incorrect';
    resultIcon.innerHTML = '<i class="fas fa-times"></i>';
    messageElement.textContent = `¡Error! "${word}" no rima con "${targetWord}"`;
    
    // Sonido
    errorAudio.play();
    
    // Animación de caída
    anime({
        targets: character,
        bottom: [character.offsetTop, -100],
        easing: 'easeInQuad',
        duration: 500
    });
    
    // Verificar fin del juego
    if (lives <= 0) {
        setTimeout(() => {
            endGame();
        }, 1000);
    }
}

function endGame() {
    messageElement.textContent = `¡Juego terminado! Puntuación final: ${score}`;
    nextBtn.textContent = 'Reintentar';
    nextBtn.classList.remove('hidden');
    nextBtn.onclick = function() {
        location.reload();
    };
    
    // Guardar puntuación
    saveScore();
}

function nextLevel() {
    const nextLevel = currentLevel + 1;
    window.location.href = `?level=${nextLevel}`;
}

function saveScore() {
    fetch('/api/save-progress.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            game: 'rhyme-platform',
            level: currentLevel,
            score: score,
            lives: lives
        })
    });
}

// Configurar botón siguiente
nextBtn.addEventListener('click', nextLevel);
</script>

<style>
.game-container.platform {
    position: relative;
    height: 80vh;
    max-width: 800px;
    margin: 0 auto;
    overflow: hidden;
}

.game-instructions {
    text-align: center;
    margin-bottom: 20px;
}

.target-box {
    text-align: center;
    margin-bottom: 20px;
    background-color: #e3f2fd;
    padding: 15px;
    border-radius: 15px;
    display: inline-flex;
    align-items: center;
    gap: 15px;
    margin: 0 auto 30px;
    display: block;
}

.target-word {
    font-size: 2.5rem;
    font-weight: bold;
    color: #43658b;
    margin: 10px 0;
}

.platform-game {
    position: relative;
    height: 400px;
    background: linear-gradient(to bottom, #87CEEB, #E0F7FA);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.platform {
    position: absolute;
    width: 100px;
    height: 30px;
    background-color: #8BC34A;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    font-weight: bold;
    color: white;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.platform:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 10px rgba(0,0,0,0.15);
}

.platform.activated {
    background-color: #4CAF50;
    box-shadow: 0 0 20px rgba(76, 175, 80, 0.7);
    animation: glow 1s infinite alternate;
}

.platform.error {
    background-color: #f44336;
    box-shadow: 0 0 20px rgba(244, 67, 54, 0.7);
}

@keyframes glow {
    from { box-shadow: 0 0 10px rgba(76, 175, 80, 0.7); }
    to { box-shadow: 0 0 30px rgba(76, 175, 80, 1); }
}

.character {
    position: absolute;
    bottom: 0;
    left: 50px;
    font-size: 3rem;
    z-index: 10;
    transition: bottom 0.8s ease;
    transform: translateX(-50%);
}

.feedback {
    text-align: center;
    margin-top: 20px;
}

.result-icon {
    font-size: 3rem;
    margin-bottom: 10px;
}

.result-icon.correct {
    color: #4CAF50;
}

.result-icon.incorrect {
    color: #f44336;
}

.message {
    font-size: 1.2rem;
    margin-bottom: 15px;
}

.next-btn {
    background-color: #4e89ae;
    color: white;
    border: none;
    padding: 10px 25px;
    border-radius: 50px;
    font-size: 1.1rem;
    cursor: pointer;
}

.game-stats {
    position: absolute;
    top: 20px;
    right: 20px;
    background-color: rgba(255, 255, 255, 0.8);
    padding: 10px 15px;
    border-radius: 10px;
    font-weight: bold;
}
</style>
<?php
$content = ob_get_clean();
include '../../includes/game_layout.php';
?>
