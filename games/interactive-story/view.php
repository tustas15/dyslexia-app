<div class="game-container story">
  <h1><?= htmlspecialchars($game_data['title']) ?></h1>
  
  <div class="story-header">
    <img src="<?= $game_data['image'] ?>" alt="<?= htmlspecialchars($game_data['title']) ?>" class="story-image">
    <div class="story-instructions">
      <p>Arrastra las palabras a los espacios en blanco para crear tu historia</p>
      <button class="audio-btn" id="play-instructions">
        <i class="fas fa-volume-up"></i> Escuchar instrucciones
      </button>
    </div>
  </div>
  
  <div class="story-template">
    <?php
    $template = $game_data['template'];
    $parts = preg_split('/(\{[^}]+\})/', $template, -1, PREG_SPLIT_DELIM_CAPTURE);
    
    foreach ($parts as $part) {
        if (preg_match('/\{([^}]+)\}/', $part, $match)) {
            $category = $match[1];
            echo '<div class="story-placeholder" data-category="'.htmlspecialchars($category).'">';
            echo '<div class="placeholder-content">______</div>';
            echo '<div class="placeholder-label">'.htmlspecialchars($category).'</div>';
            echo '</div>';
        } else {
            echo '<span class="story-text">'.htmlspecialchars($part).'</span>';
        }
    }
    ?>
  </div>
  
  <div class="story-elements">
    <?php foreach ($game_data['elements'] as $category => $items): ?>
      <div class="element-category">
        <h3><?= ucfirst(htmlspecialchars($category)) ?></h3>
        <div class="category-items">
          <?php foreach ($items as $item): ?>
            <div class="story-element" 
                 draggable="true"
                 data-category="<?= htmlspecialchars($category) ?>"
                 data-value="<?= htmlspecialchars($item['word']) ?>"
                 data-audio="<?= $item['audio'] ?>">
              <img src="<?= $item['image'] ?>" alt="<?= htmlspecialchars($item['word']) ?>" class="element-image">
              <div class="element-text"><?= htmlspecialchars($item['word']) ?></div>
              <button class="audio-btn element-audio" data-audio="<?= $item['audio'] ?>">
                <i class="fas fa-volume-up"></i>
              </button>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  
  <div class="story-actions">
    <button id="reset-story" class="action-btn">
      <i class="fas fa-redo"></i> Reiniciar
    </button>
    <button id="save-story" class="action-btn">
      <i class="fas fa-save"></i> Guardar Cuento
    </button>
    <button id="read-story" class="action-btn">
      <i class="fas fa-book-reader"></i> Leer Cuento
    </button>
  </div>
  
  <div class="story-result hidden">
    <h3>Tu Cuento Completo:</h3>
    <div class="final-story"></div>
    <button id="new-story" class="action-btn">
      <i class="fas fa-plus"></i> Crear Otro Cuento
    </button>
  </div>
</div>

<script>
// Audio para la historia
const instructionsAudio = new Howl({
    src: ['<?= get_audio('common', 'instructions.mp3') ?>']
});

// Reproducir instrucciones
document.getElementById('play-instructions').addEventListener('click', () => {
    instructionsAudio.play();
});

// Reproducir audio de elementos
document.querySelectorAll('.element-audio').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const audioUrl = btn.dataset.audio;
        const sound = new Howl({ src: [audioUrl] });
        sound.play();
    });
});

// Arrastrar elementos
document.querySelectorAll('.story-element').forEach(element => {
    element.addEventListener('dragstart', (e) => {
        e.dataTransfer.setData('text/plain', JSON.stringify({
            category: element.dataset.category,
            value: element.dataset.value,
            audio: element.dataset.audio
        }));
    });
});

// Soltar elementos en los placeholders
document.querySelectorAll('.story-placeholder').forEach(placeholder => {
    placeholder.addEventListener('dragover', (e) => {
        e.preventDefault();
        placeholder.classList.add('drag-over');
    });
    
    placeholder.addEventListener('dragleave', () => {
        placeholder.classList.remove('drag-over');
    });
    
    placeholder.addEventListener('drop', (e) => {
        e.preventDefault();
        placeholder.classList.remove('drag-over');
        
        const data = JSON.parse(e.dataTransfer.getData('text/plain'));
        
        // Verificar si la categoría coincide
        if (data.category === placeholder.dataset.category) {
            placeholder.querySelector('.placeholder-content').textContent = data.value;
            placeholder.dataset.value = data.value;
            placeholder.dataset.audio = data.audio;
            
            // Reproducir sonido de éxito
            const successSound = new Howl({ src: ['<?= get_audio('common', 'success.mp3') ?>'] });
            successSound.play();
        } else {
            // Reproducir sonido de error
            const errorSound = new Howl({ src: ['<?= get_audio('common', 'error.mp3') ?>'] });
            errorSound.play();
            alert(`Esta palabra pertenece a la categoría "${data.category}", pero necesitas una de "${placeholder.dataset.category}"`);
        }
    });
});

// Reiniciar historia
document.getElementById('reset-story').addEventListener('click', () => {
    document.querySelectorAll('.story-placeholder').forEach(placeholder => {
        placeholder.querySelector('.placeholder-content').textContent = '______';
        delete placeholder.dataset.value;
        delete placeholder.dataset.audio;
    });
});

// Guardar historia
document.getElementById('save-story').addEventListener('click', () => {
    const storyData = {
        id: <?= $game_data['id'] ?>,
        level: <?= $game_data['level'] ?>,
        selections: {}
    };
    
    document.querySelectorAll('.story-placeholder').forEach(placeholder => {
        if (placeholder.dataset.value) {
            storyData.selections[placeholder.dataset.category] = placeholder.dataset.value;
        }
    });
    
    // Enviar al servidor
    fetch('/api/save-story.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(storyData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('¡Tu cuento se ha guardado correctamente!');
        } else {
            alert('Error al guardar: ' + data.message);
        }
    });
});

// Leer historia completa
document.getElementById('read-story').addEventListener('click', () => {
    const storyContainer = document.querySelector('.final-story');
    storyContainer.innerHTML = '';
    
    // Construir la historia final
    document.querySelectorAll('.story-text, .story-placeholder').forEach(element => {
        if (element.classList.contains('story-text')) {
            storyContainer.innerHTML += element.textContent;
        } else {
            const value = element.dataset.value || '______';
            storyContainer.innerHTML += `<span class="highlight">${value}</span>`;
        }
    });
    
    // Mostrar resultado y ocultar elementos
    document.querySelector('.story-result').classList.remove('hidden');
    document.querySelector('.story-elements').classList.add('hidden');
    document.querySelector('.story-actions').classList.add('hidden');
    
    // Leer la historia en voz alta (usando la API de síntesis de voz)
    if ('speechSynthesis' in window) {
        const speech = new SpeechSynthesisUtterance(storyContainer.textContent);
        speech.lang = 'es-ES';
        speech.rate = 0.9;
        window.speechSynthesis.speak(speech);
    }
});

// Crear nueva historia
document.getElementById('new-story').addEventListener('click', () => {
    location.reload();
});
</script>

<style>
.game-container.story {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

.story-header {
    display: flex;
    align-items: center;
    margin-bottom: 25px;
    background-color: #f8f9fa;
    border-radius: 15px;
    padding: 15px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.story-image {
    width: 200px;
    height: 150px;
    object-fit: cover;
    border-radius: 10px;
    margin-right: 20px;
}

.story-instructions {
    flex: 1;
}

.story-template {
    background-color: #fff;
    border: 2px solid #4e89ae;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 25px;
    font-size: 1.3rem;
    line-height: 1.8;
    min-height: 200px;
}

.story-text {
    display: inline;
}

.story-placeholder {
    display: inline-block;
    min-width: 120px;
    position: relative;
    vertical-align: middle;
    margin: 0 5px;
}

.placeholder-content {
    border-bottom: 3px dashed #ed6663;
    padding: 5px 10px;
    text-align: center;
    min-height: 40px;
    cursor: pointer;
}

.placeholder-label {
    position: absolute;
    top: -20px;
    left: 0;
    right: 0;
    font-size: 0.8rem;
    color: #43658b;
    text-align: center;
    font-weight: bold;
}

.story-elements {
    background-color: #e3f2fd;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 25px;
}

.element-category {
    margin-bottom: 25px;
}

.element-category h3 {
    background-color: #43658b;
    color: white;
    padding: 8px 15px;
    border-radius: 30px;
    display: inline-block;
    margin-top: 0;
}

.category-items {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 10px;
}

.story-element {
    background-color: white;
    border: 2px solid #4e89ae;
    border-radius: 10px;
    padding: 10px;
    width: 150px;
    text-align: center;
    cursor: move;
    transition: all 0.3s;
}

.story-element:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.element-image {
    width: 100px;
    height: 100px;
    object-fit: contain;
    margin-bottom: 10px;
}

.element-text {
    font-weight: bold;
    margin-bottom: 8px;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.element-audio {
    background: none;
    border: none;
    color: #ed6663;
    cursor: pointer;
    font-size: 1.1rem;
}

.story-actions {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 25px;
}

.action-btn {
    background-color: #4e89ae;
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 50px;
    font-size: 1.1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.action-btn:hover {
    background-color: #43658b;
    transform: translateY(-3px);
}

.story-result {
    background-color: #d4edda;
    border: 2px solid #28a745;
    border-radius: 15px;
    padding: 25px;
    margin-top: 20px;
}

.final-story {
    font-size: 1.2rem;
    line-height: 1.8;
    margin: 20px 0;
    padding: 15px;
    background-color: white;
    border-radius: 10px;
}

.highlight {
    background-color: #fff3cd;
    padding: 2px 5px;
    border-radius: 5px;
}

.drag-over {
    background-color: #e3f2fd;
    box-shadow: 0 0 10px rgba(78, 137, 174, 0.5);
}

.hidden {
    display: none;
}
</style>