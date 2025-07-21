<?php
// Verificar si se cargaron los datos correctamente
if (!isset($game_data)) {
    die("Error: Datos del juego no disponibles.");
}

$correctWord = $game_data['correct_word'] ?? 'zapato';
$incorrectWord = $game_data['incorrect_word'] ?? 'sapato';
$audio = $game_data['audio'] ?? '';
$image = $game_data['image'] ?? '';
$level = $game_data['level'] ?? 1;
?>

<div class="game-container robot">
  <h1>Palabrabot <small>Nivel <?= $level ?></small></h1>
  
  <div class="instructions">
    <p>¡Ayuda al robot a corregir las palabras que pronuncia mal!</p>
    <button class="audio-btn" id="play-instructions">
      <i class="fas fa-volume-up"></i> Escuchar instrucciones
    </button>
  </div>
  
  <div class="robot-container">
    <div class="robot-character">
      <img src="/assets/images/robot.png" id="robot-img" alt="Robot amigable">
    </div>
    
    <div class="speech-bubble">
      <p id="robot-text"><?= htmlspecialchars($incorrectWord) ?></p>
    </div>
    
    <button class="audio-btn" id="play-robot-word">
      <i class="fas fa-volume-up"></i> Escuchar al robot
    </button>
  </div>
  
  <div class="correction-panel">
    <div class="input-group">
      <label for="correction-input">Escribe la palabra correcta:</label>
      <input type="text" id="correction-input" 
             placeholder="Escribe la corrección aquí"
             autocomplete="off"
             autocapitalize="off"
             spellcheck="false">
    </div>
    
    <div class="actions">
      <button class="check-btn" id="check-btn">Corregir</button>
      <button class="hint-btn" id="hint-btn">Pista</button>
    </div>
  </div>
  
  <div class="feedback">
    <div class="result-icon"></div>
    <p class="message"></p>
    <button class="next-btn hidden" id="next-btn">Siguiente</button>
  </div>
  
  <div class="word-image">
    <img src="<?= $image ?>" alt="<?= htmlspecialchars($correctWord) ?>">
  </div>
</div>

<script>
// Variables globales
const correctWord = "<?= $correctWord ?>";
const incorrectWord = "<?= $incorrectWord ?>";
let attempts = 0;
const maxAttempts = 3;

// Audio del robot diciendo la palabra incorrecta
const robotAudio = new Howl({
    src: ['<?= $audio ?>'],
    rate: 0.8, // Hacer que el robot hable más lento
    onend: function() {
        document.getElementById('robot-img').src = "/assets/images/robot.png";
    }
});

// Audio de instrucciones
const instructionsAudio = new Howl({
    src: ['<?= get_audio('common', 'instructions_robot.mp3') ?>']
});

// Sonidos de feedback
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

// Reproducir palabra del robot
document.getElementById('play-robot-word').addEventListener('click', () => {
    document.getElementById('robot-img').src = "/assets/images/robot-talking.png";
    robotAudio.play();
});

// Comprobar corrección
document.getElementById('check-btn').addEventListener('click', checkCorrection);

// Función para verificar la corrección
function checkCorrection() {
    const input = document.getElementById('correction-input');
    const userInput = input.value.trim().toLowerCase();
    
    if (!userInput) {
        showMessage('Por favor escribe una corrección', 'error');
        return;
    }
    
    attempts++;
    const isCorrect = userInput === correctWord;
    
    const resultIcon = document.querySelector('.result-icon');
    const message = document.querySelector('.message');
    const nextBtn = document.getElementById('next-btn');
    
    if (isCorrect) {
        // Éxito
        document.getElementById('robot-img').src = "/assets/images/robot-happy.png";
        resultIcon.className = 'result-icon correct';
        resultIcon.innerHTML = '<i class="fas fa-check"></i>';
        message.textContent = '¡Excelente! Has ayudado al robot a hablar correctamente.';
        nextBtn.classList.remove('hidden');
        
        // Sonido de éxito
        successAudio.play();
        
        // Guardar progreso
        saveProgress(true);
    } else {
        // Error
        document.getElementById('robot-img').src = "/assets/images/robot-sad.png";
        resultIcon.className = 'result-icon incorrect';
        resultIcon.innerHTML = '<i class="fas fa-times"></i>';
        
        // Sonido de error
        errorAudio.play();
        
        if (attempts >= maxAttempts) {
            message.textContent = `¡Oh no! La palabra correcta es "${correctWord}".`;
            nextBtn.classList.remove('hidden');
            saveProgress(false);
        } else {
            message.textContent = `Intento ${attempts} de ${maxAttempts}. ¡Sigue intentando!`;
            
            // Dar pista después del primer intento fallido
            if (attempts === 1) {
                const firstLetter = correctWord.charAt(0);
                message.textContent += ` Pista: La palabra empieza con "${firstLetter.toUpperCase()}".`;
            } else if (attempts === 2) {
                const lastLetter = correctWord.charAt(correctWord.length - 1);
                message.textContent += ` Pista: La palabra termina con "${lastLetter.toUpperCase()}".`;
            }
        }
    }
}

// Mostrar pista
document.getElementById('hint-btn').addEventListener('click', () => {
    // Revelar la primera letra
    const firstLetter = correctWord.charAt(0);
    showMessage(`Pista: La palabra empieza con "${firstLetter.toUpperCase()}"`, 'info');
});

// Siguiente palabra
document.getElementById('next-btn').addEventListener('click', () => {
    window.location.reload();
});

// Guardar progreso
function saveProgress(isCorrect) {
    fetch('/api/save-progress.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            game: 'word-robot',
            level: <?= $level ?>,
            correct: isCorrect,
            word: correctWord,
            attempts: attempts
        })
    });
}

// Mostrar mensaje
function showMessage(text, type) {
    const message = document.querySelector('.message');
    message.textContent = text;
    message.className = 'message ' + type;
    
    setTimeout(() => {
        message.className = 'message';
    }, 3000);
}
</script>

<style>
.game-container.robot {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    text-align: center;
}

.instructions {
    margin-bottom: 30px;
    font-size: 1.1rem;
}

.robot-container {
    position: relative;
    margin-bottom: 40px;
}

.robot-character {
    margin: 0 auto 20px;
    width: 200px;
    height: 200px;
}

.robot-character img {
    max-width: 100%;
    transition: all 0.3s;
}

.speech-bubble {
    position: relative;
    background-color: white;
    padding: 20px;
    border-radius: 20px;
    max-width: 80%;
    margin: 0 auto 20px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    font-size: 1.5rem;
    font-weight: bold;
    min-height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.speech-bubble::after {
    content: '';
    position: absolute;
    bottom: -20px;
    left: 50%;
    transform: translateX(-50%);
    border: 10px solid transparent;
    border-top-color: white;
}

.correction-panel {
    background-color: #f8f9fa;
    padding: 25px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

.input-group {
    margin-bottom: 20px;
}

.input-group label {
    display: block;
    margin-bottom: 10px;
    font-weight: bold;
    font-size: 1.2rem;
}

#correction-input {
    width: 100%;
    max-width: 300px;
    padding: 15px;
    font-size: 1.2rem;
    border: 3px solid #4e89ae;
    border-radius: 10px;
    text-align: center;
    font-family: 'OpenDyslexic', sans-serif;
}

.actions {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 20px;
}

.check-btn, .hint-btn {
    padding: 12px 25px;
    border-radius: 50px;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.3s;
    border: none;
}

.check-btn {
    background-color: #4e89ae;
    color: white;
}

.hint-btn {
    background-color: #ffd93d;
    color: #333;
}

.check-btn:hover {
    background-color: #43658b;
    transform: translateY(-3px);
}

.hint-btn:hover {
    background-color: #ffcc00;
    transform: translateY(-3px);
}

.feedback {
    margin-top: 25px;
}

.result-icon {
    font-size: 3rem;
    margin-bottom: 15px;
}

.result-icon.correct {
    color: #4CAF50;
}

.result-icon.incorrect {
    color: #f44336;
}

.message {
    font-size: 1.2rem;
    margin-bottom: 20px;
    padding: 15px;
    border-radius: 10px;
}

.message.error {
    background-color: #f8d7da;
    color: #721c24;
}

.message.info {
    background-color: #cce5ff;
    color: #004085;
}

.next-btn {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 50px;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.3s;
}

.next-btn:hover {
    background-color: #3d8b40;
    transform: translateY(-3px);
}

.word-image {
    margin-top: 30px;
}

.word-image img {
    max-width: 200px;
    max-height: 200px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
</style>