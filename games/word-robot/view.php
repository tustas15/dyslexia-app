<div class="game-container robot">
  <div class="robot-character">
    <img src="/assets/images/robot.png" id="robot-img">
  </div>
  
  <div class="speech-bubble">
    <p id="robot-text"><?= $incorrectWord ?></p>
  </div>
  
  <div class="correction-panel">
    <input type="text" id="correction-input" placeholder="Escribe la corrección">
    <button onclick="checkCorrection()">Corregir</button>
  </div>
  
  <div class="feedback"></div>
</div>

<script>
function checkCorrection() {
  const input = document.getElementById('correction-input');
  const isCorrect = input.value.toLowerCase() === "<?= $correctWord ?>";
  
  if(isCorrect) {
    document.getElementById('robot-img').src = "/assets/images/robot-happy.png";
    document.querySelector('.feedback').textContent = "¡Excelente!";
  } else {
    document.getElementById('robot-img').src = "/assets/images/robot-sad.png";
    document.querySelector('.feedback').textContent = "Sigue intentando";
  }
}
</script>