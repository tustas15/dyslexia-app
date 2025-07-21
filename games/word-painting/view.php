<div class="game-container painting">
  <h1>Pintando Palabras</h1>
  <button class="audio-btn" onclick="playWord()">▶ Escuchar palabra</button>
  
  <div class="canvas-container">
    <?php foreach($letters as $letter): ?>
      <div class="letter-box" 
           data-letter="<?= $letter['char'] ?>"
           data-color="<?= $letter['color'] ?>"
           onclick="selectLetter(this)">
        <?= $letter['char'] ?>
      </div>
    <?php endforeach; ?>
  </div>
  
  <div class="color-palette">
    <div class="color red" onclick="selectColor('red')"></div>
    <div class="color blue" onclick="selectColor('blue')"></div>
    <div class="color green" onclick="selectColor('green')"></div>
  </div>
</div>

<script>
let currentColor = '';

function selectColor(color) {
  currentColor = color;
  document.querySelectorAll('.color').forEach(el => 
    el.classList.remove('selected')
  );
  event.target.classList.add('selected');
}

function selectLetter(letterBox) {
  if(!currentColor) return;
  
  letterBox.style.backgroundColor = currentColor;
  letterBox.dataset.colored = "1";
  
  // Verificar si completó la palabra
  const colored = document.querySelectorAll('[data-colored="1"]').length;
  if(colored === <?= count($correctLetters) ?>) {
    checkSolution();
  }
}
</script>