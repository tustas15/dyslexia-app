<?php
// Verificar si se cargaron los datos correctamente
if (!isset($game_data)) {
    die("Error: Datos del juego no disponibles.");
}

$word = $game_data['word'] ?? 'palabra';
$syllables = $game_data['syllables'] ?? ['pa', 'la', 'bra'];
$correctOrder = $game_data['correct_syllables'] ?? ['pa', 'la', 'bra'];
$image = $game_data['image'] ?? '';
$audio = $game_data['audio'] ?? '';
$level = $game_data['level'] ?? 1;
?>

<div class="game-container syllables">
  <h1>Caza Sílabas <small>Nivel <?= $level ?></small></h1>
  
  <div class="game-header">
    <div class="target-image">
      <img src="<?= $image ?>" alt="<?= htmlspecialchars($word) ?>">
    </div>
    <div class="target-word-audio">
      <div class="target-word"><?= htmlspecialchars($word) ?></div>
      <button class="audio-btn" id="play-word">
        <i class="fas fa-volume-up"></i>
      </button>
    </div>
  </div>
  
  <div class="instructions">
    <p>Arrastra las sílabas en el orden correcto para formar la palabra</p>
    <button class="audio-btn" id="play-instructions">
      <i class="fas fa-volume-up"></i> Escuchar instrucciones
    </button>
  </div>
  
  <div class="drop-zone">
    <?php for ($i = 0; $i < count($correctOrder); $i++): ?>
      <div class="slot" data-index="<?= $i ?>"></div>
    <?php endfor; ?>
  </div>
  
  <div class="syllables-container">
    <?php foreach($syllables as $syl): ?>
      <div class="syllable" draggable="true" data-syllable="<?= $syl ?>">
        <?= $syl ?>
      </div>
    <?php endforeach; ?>
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
const correctSyllables = <?= json_encode($correctOrder) ?>;
const word = "<?= $word ?>";
let userOrder = Array(correctSyllables.length).fill(null);

// Audio de la palabra
const wordAudio = new Howl({
    src: ['<?= $audio ?>']
});

// Audio de instrucciones
const instructionsAudio = new Howl({
    src: ['<?= get_audio('common', 'instructions_syllables.mp3') ?>']
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

// Hacer las sílabas arrastrables
document.querySelectorAll('.syllable').forEach(syllable => {
    syllable.addEventListener('dragstart', (e) => {
        e.dataTransfer.setData('text/plain', syllable.dataset.syllable);
    });
});

// Permitir soltar en los slots
document.querySelectorAll('.slot').forEach(slot => {
    slot.addEventListener('dragover', (e) => {
        e.preventDefault();
        slot.classList.add('drag-over');
    });
    
    slot.addEventListener('dragleave', () => {
        slot.classList.remove('drag-over');
    });
    
    slot.addEventListener('drop', (e) => {
        e.preventDefault();
        slot.classList.remove('drag-over');
        
        const syllable = e.dataTransfer.getData('text/plain');
        const index = parseInt(slot.dataset.index);
        
        // Asignar la sílaba al slot
        slot.textContent = syllable;
        slot.dataset.syllable = syllable;
        userOrder[index] = syllable;
    });
});

// Comprobar resultado
document.getElementById('check-btn').addEventListener('click', () => {
    const isCorrect = userOrder.every((syl, index) => syl === correctSyllables[index]);
    
    // Feedback visual
    const resultIcon = document.querySelector('.result-icon');
    const message = document.querySelector('.message');
    const nextBtn = document.getElementById('next-btn');
    
    if (isCorrect) {
        resultIcon.className = 'result-icon correct';
        resultIcon.innerHTML = '<i class="fas fa-check"></i>';
        message.textContent = '¡Correcto! La palabra está bien formada.';
        nextBtn.classList.remove('hidden');
        
        // Sonido de éxito
        successAudio.play();
        
        // Guardar progreso
        saveProgress(true);
    } else {
        resultIcon.className = 'result-icon incorrect';
        resultIcon.innerHTML = '<i class="fas fa-times"></i>';
        message.textContent = '¡Inténtalo de nuevo! El orden no es correcto.';
        
        // Sonido de error
        errorAudio.play();
        
        // Resaltar errores
        userOrder.forEach((syl, index) => {
            if (syl !== correctSyllables[index]) {
                const slot = document.querySelector(`.slot[data-index="${index}"]`);
                slot.classList.add('error');
            }
        });
    }
});

// Reiniciar
document.getElementById('reset-btn').addEventListener('click', () => {
    userOrder = Array(correctSyllables.length).fill(null);
    document.querySelectorAll('.slot').forEach(slot => {
        slot.textContent = '';
        delete slot.dataset.syllable;
        slot.classList.remove('error');
    });
    document.querySelector('.result-icon').className = 'result-icon';
    document.querySelector('.message').textContent = '';
    document.getElementById('next-btn').classList.add('hidden');
});

// Siguiente
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
            game: 'syllable-hunt',
            level: <?= $level ?>,
            correct: isCorrect,
            word: word
        })
    });
}
</script>

<style>
.game-container.syllables {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.game-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 30px;
    margin-bottom: 30px;
}

.target-image img {
    max-width: 150px;
    max-height: 150px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.target-word-audio {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.target-word {
    font-size: 2.5rem;
    font-weight: bold;
    color: #43658b;
}

.instructions {
    text-align: center;
    margin-bottom: 30px;
    font-size: 1.2rem;
}

.drop-zone {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 30px;
}

.slot {
    width: 100px;
    height: 100px;
    border: 3px dashed #4e89ae;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: bold;
    background-color: #f0f9ff;
    transition: all 0.3s;
}

.slot.drag-over {
    background-color: #e3f2fd;
    box-shadow: 0 0 10px rgba(78, 137, 174, 0.5);
}

.slot.error {
    background-color: #f8d7da;
    border-color: #dc3545;
}

.syllables-container {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.syllable {
    width: 90px;
    height: 90px;
    border: 3px solid #4e89ae;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: bold;
    background-color: white;
    cursor: move;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: all 0.3s;
}

.syllable:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 10px rgba(0,0,0,0.15);
}

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