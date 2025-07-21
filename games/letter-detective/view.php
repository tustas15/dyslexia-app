<div class="game-container detective">
  <h1>Detective de Letras</h1>
  <p>Encuentra la letra correcta</p>
  
  <div class="letter-pairs">
    <?php foreach($pairs as $pair): ?>
      <div class="pair">
        <div class="letter" onclick="checkLetter(this, <?= $pair[0]['correct'] ?>)">
          <?= $pair[0]['char'] ?>
        </div>
        <div class="letter" onclick="checkLetter(this, <?= $pair[1]['correct'] ?>)">
          <?= $pair[1]['char'] ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<style>
.letter {
  font-size: 5rem;
  margin: 1rem;
  cursor: pointer;
  transition: transform 0.3s;
}
.letter:hover {
  transform: scale(1.2);
}
</style>