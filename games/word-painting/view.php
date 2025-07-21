<?php
// Verificar si se cargaron los datos correctamente
if (!isset($game_data)) {
    die("Error: Datos del juego no disponibles.");
}

$word = $game_data['word'] ?? 'palabra';
$syllables = $game_data['syllables'] ?? [];
$letters = $game_data['letters'] ?? [];
$audio = $game_data['audio'] ?? '';
$level = $game_data['level'] ?? 1;

// Contar letras objetivo
$target_letter_count = 0;
foreach ($letters as $letter) {
    if ($letter['is_target']) {
        $target_letter_count++;
    }
}
?>

<?php ob_start(); ?>

<div class="game-container painting">
  <h1>Pintando Palabras <small>Nivel <?= $level ?></small></h1>
  
  <div class="game-header">
    <div class="target-word">
      <span>Palabra: </span>
      <span id="target-word-text"><?= htmlspecialchars($word) ?></span>
    </div>
    <button class="audio-btn" id="play-word">
      <i class="fas fa-volume-up"></i> Escuchar palabra
    </button>
  </div>
  
  <div class="instructions">
    <p>Encuentra las letras de la palabra y colorealas con el color de cada sílaba</p>
    <button class="audio-btn" id="play-instructions">
      <i class="fas fa-volume-up"></i> Escuchar instrucciones
    </button>
  </div>
  
  <div class="syllables-guide">
    <h3>Guía de sílabas:</h3>
    <?php foreach ($syllables as $syllable): ?>
      <div class="syllable-color" data-syllable="<?= $syllable ?>">
        <span class="color-box" style="background-color: <?= $letters[0]['color'] ?>;"></span>
        <span class="syllable-text"><?= $syllable ?></span>
      </div>
    <?php endforeach; ?>
  </div>
  
  <div class="canvas-container">
    <?php foreach($letters as $letter): ?>
      <div class="letter-box" 
           data-letter="<?= htmlspecialchars($letter['char']) ?>"
           data-color="<?= $letter['color'] ?>"
           data-target="<?= $letter['is_target'] ? 'true' : 'false' ?>"
           onclick="selectLetter(this)">
        <?= htmlspecialchars($letter['char']) ?>
      </div>
    <?php endforeach; ?>
  </div>
  
  <div class="color-palette">
    <div class="color red" data-color="red" onclick="selectColor('red')"></div>
    <div class="color blue" data-color="blue" onclick="selectColor('blue')"></div>
    <div class="color green" data-color="green" onclick="selectColor('green')"></div>
    <div class="color yellow" data-color="yellow" onclick="selectColor('yellow')"></div>
    <div class="color orange" data-color="orange" onclick="selectColor('orange')"></div>
    <div class="color purple" data-color="purple" onclick="selectColor('purple')"></div>
  </div>
  
  <div class="actions">
    <button class="check-btn" id="check-btn">Comprobar</button>
    <button class="reset-btn" id="reset-btn">Reiniciar</button>
  </div>
  
  <div class="feedback">
    <div class="result-icon"></div>
    <p class="message"></p>
    <button class="next-btn hidden" id="next-btn">Siguiente</button>
  </div>
</div>

<script>
// Variables globales
const targetWord = "<?= $word ?>";
let currentColor = '';
let coloredCount = 0;
const targetLetterCount = <?= $target_letter_count ?>;

// Audio de la palabra
const wordAudio = new Howl({
    src: ['<?= $audio ?>']
});

// Audio de instrucciones
const instructionsAudio = new Howl({
    src: ['<?= get_audio('common', 'instructions_painting.mp3') ?>']
});

// Sonidos de feedback
const successAudio = new Howl({
    src: ['<?= get_audio('common', 'success.mp3') ?>']
});

const errorAudio = new Howl({
    src: ['<?= get_audio('common', 'error.mp3') ?>']
});

// Reproducir palabra
document.getElementById('play-word').addEventListener('click', () => {
    wordAudio.play();
});

// Reproducir instrucciones
document.getElementById('play-instructions').addEventListener('click', () => {
    instructionsAudio.play();
});

function selectColor(color) {
    currentColor = color;
    document.querySelectorAll('.color').forEach(el => {
        el.classList.remove('selected');
    });
    document.querySelector(`.color[data-color="${color}"]`).classList.add('selected');
}

function selectLetter(letterBox) {
    if (!currentColor) {
        alert('Por favor selecciona un color primero');
        return;
    }
    
    // Solo permitir colorear letras que están en la palabra objetivo
    if (letterBox.dataset.target === 'true') {
        letterBox.style.backgroundColor = currentColor;
        letterBox.dataset.colored = "true";
        coloredCount++;
        
        // Verificar si completó la palabra
        if (coloredCount === targetLetterCount) {
            checkSolution();
        }
    } else {
        // Mostrar error
        letterBox.classList.add('error');
        setTimeout(() => {
            letterBox.classList.remove('error');
        }, 1000);
    }
}

// Comprobar solución
document.getElementById('check-btn').addEventListener('click', checkSolution);

function checkSolution() {
    // Verificar si todas las letras objetivo están coloreadas
    const coloredBoxes = document.querySelectorAll('.letter-box[data-target="true"][data-colored="true"]');
    const allColored = coloredBoxes.length === targetLetterCount;
    
    // Verificar si los colores son correctos
    let colorsCorrect = true;
    document.querySelectorAll('.letter-box[data-target="true"]').forEach(box => {
        const correctColor = box.dataset.color;
        const userColor = box.style.backgroundColor;
        
        // Convertir nombres de colores a RGB para comparación
        const colorMap = {
            'red': 'rgb(255, 107, 107)',
            'blue': 'rgb(77, 150, 255)',
            'green': 'rgb(107, 199, 119)',
            'yellow': 'rgb(255, 217, 61)',
            'orange': 'rgb(255, 156, 107)',
            'purple': 'rgb(155, 93, 229)'
        };
        
        if (colorMap[correctColor] !== userColor && userColor !== '') {
            colorsCorrect = false;
            box.classList.add('incorrect-color');
        }
    });
    
    const resultIcon = document.querySelector('.result-icon');
    const message = document.querySelector('.message');
    const nextBtn = document.getElementById('next-btn');
    
    if (allColored && colorsCorrect) {
        resultIcon.className = 'result-icon correct';
        resultIcon.innerHTML = '<i class="fas fa-check"></i>';
        message.textContent = '¡Perfecto! Has coloreado todas las letras correctamente.';
        nextBtn.classList.remove('hidden');
        
        // Sonido de éxito
        successAudio.play();
        
        // Guardar progreso
        saveProgress(true);
    } else {
        resultIcon.className = 'result-icon incorrect';
        resultIcon.innerHTML = '<i class="fas fa-times"></i>';
        
        if (!allColored) {
            message.textContent = `¡Faltan letras! ${coloredCount} de ${targetLetterCount} coloreadas.`;
        } else {
            message.textContent = '¡Colores incorrectos! Revisa la guía de sílabas.';
        }
        
        // Sonido de error
        errorAudio.play();
    }
}

// Reiniciar
document.getElementById('reset-btn').addEventListener('click', () => {
    document.querySelectorAll('.letter-box').forEach(box => {
        box.style.backgroundColor = '';
        delete box.dataset.colored;
        box.classList.remove('incorrect-color');
    });
    coloredCount = 0;
    document.querySelector('.result-icon').className = 'result-icon';
    document.querySelector('.message').textContent = '';
    document.getElementById('next-btn').classList.add('hidden');
});

// Guardar progreso
function saveProgress(isCorrect) {
    fetch('/api/save-progress.php', {
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
    });
}

// Siguiente palabra
document.getElementById('next-btn').addEventListener('click', () => {
    window.location.reload();
});
</script>

<style>
.game-container.painting {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.game-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    background-color: #e3f2fd;
    padding: 15px;
    border-radius: 15px;
}

.target-word {
    font-size: 1.8rem;
    font-weight: bold;
    color: #43658b;
}

.instructions {
    text-align: center;
    margin-bottom: 30px;
    font-size: 1.1rem;
}

.syllables-guide {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 30px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 15px;
}

.syllable-color {
    display: flex;
    align-items: center;
    gap: 8px;
}

.color-box {
    display: inline-block;
    width: 25px;
    height: 25px;
    border-radius: 5px;
    border: 2px solid #ddd;
}

.syllable-text {
    font-weight: bold;
    font-size: 1.2rem;
}

.canvas-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
    gap: 15px;
    margin-bottom: 30px;
    min-height: 300px;
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 15px;
    box-shadow: inset 0 0 10px rgba(0,0,0,0.1);
}

.letter-box {
    width: 70px;
    height: 70px;
    border: 2px solid #4e89ae;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: bold;
    cursor: pointer;
    background-color: white;
    transition: all 0.3s;
    user-select: none;
}

.letter-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 10px rgba(0,0,0,0.1);
}

.letter-box.error {
    animation: shake 0.5s;
    background-color: #f8d7da;
    border-color: #dc3545;
}

.letter-box.incorrect-color {
    border: 3px solid #dc3545;
}

@keyframes shake {
    0% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    50% { transform: translateX(5px); }
    75% { transform: translateX(-5px); }
    100% { transform: translateX(0); }
}

.color-palette {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.color {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    cursor: pointer;
    border: 3px solid transparent;
    transition: all 0.3s;
    box-shadow: 0 3px 6px rgba(0,0,0,0.1);
}

.color.selected {
    border-color: #333;
    transform: scale(1.2);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.red { background-color: #ff6b6b; }
.blue { background-color: #4d96ff; }
.green { background-color: #6bc77; }
.yellow { background-color: #ffd93d; }
.orange { background-color: #ff9c6b; }
.purple { background-color: #9b5de5; }

.actions {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 20px;
}

.check-btn, .reset-btn {
    background-color: #4e89ae;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 50px;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.3s;
}

.check-btn:hover, .reset-btn:hover {
    background-color: #43658b;
    transform: translateY(-3px);
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
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px 25px;
    border-radius: 50px;
    font-size: 1.1rem;
    cursor: pointer;
}
</style>

<?php
$content = ob_get_clean();
include '../../includes/game_layout.php';
?>