<?php
// Obtener palabra objetivo y palabras para saltar
$targetWord = "sol";
$platformWords = ["col", "pez", "gol", "pan", "sal"];
?>

<div class="game-container platform">
  <h1>Saltarima</h1>
  
  <div class="target-box">
    <p>Palabra objetivo:</p>
    <div class="target-word"><?= $targetWord ?></div>
  </div>
  
  <div class="platform-game">
    <?php foreach($platformWords as $word): ?>
      <?php $isRhyme = doesRhyme($targetWord, $word); ?>
      <div class="platform" 
           data-rhyme="<?= $isRhyme ? 1 : 0 ?>"
           onclick="jump(this)">
        <?= $word ?>
      </div>
    <?php endforeach; ?>
  </div>
  
  <div class="character">🤸</div>
</div>

<script>
function jump(platform) {
  const isRhyme = platform.dataset.rhyme === "1";
  
  if(isRhyme) {
    platform.classList.add('activated');
    // Mover personaje
    document.querySelector('.character').style.bottom = platform.offsetTop + 'px';
  } else {
    // Animación de caída
    document.querySelector('.character').classList.add('fall');
  }
}
</script>