<div class="game-container syllables">
  <h1>Caza Sílabas</h1>
  <div class="target-word"><?= $targetWord ?></div>
  
  <div class="syllables-container">
    <?php foreach($syllables as $syl): ?>
      <div class="syllable" draggable="true"><?= $syl ?></div>
    <?php endforeach; ?>
  </div>
  
  <div class="drop-zone">
    <div class="slot"></div>
    <div class="slot"></div>
    <div class="slot"></div>
  </div>
  
  <button class="check-btn">Comprobar</button>
  <div class="feedback"></div>
</div>

<script>
// Implementación con Draggable.js
const dragManager = new Draggable.Draggable(
  document.querySelector('.syllables-container'),
  { draggable: '.syllable' }
);

dragManager.on('drag:stop', () => {
  // Lógica para verificar orden correcto
});
</script>