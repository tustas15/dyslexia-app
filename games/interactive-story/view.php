<!-- Head -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
  <h1 class="text-2xl font-bold text-center mb-6"><?= htmlspecialchars($game_data['title']) ?></h1>

  <!-- Header -->
  <div class="flex items-center mb-6 bg-gray-100 rounded-lg p-4 shadow-sm">
    <img src="<?= $game_data['image'] ?>" alt="<?= htmlspecialchars($game_data['title']) ?>" class="w-48 h-36 object-cover rounded-lg mr-4">
    <div>
      <p class="text-gray-700 mb-2">Arrastra las palabras a los espacios en blanco para crear tu historia</p>
      <button id="play-instructions" class="flex items-center gap-2 text-blue-600 hover:text-blue-800">
        <i class="fas fa-volume-up"></i> Escuchar instrucciones
      </button>
    </div>
  </div>

  <!-- Template -->
  <div class="border-2 border-blue-400 rounded-lg p-6 mb-6 text-lg leading-relaxed min-h-[200px] bg-white">
    <?php
    $template = $game_data['template'];
    $parts = preg_split('/(\{[^}]+\})/', $template, -1, PREG_SPLIT_DELIM_CAPTURE);
    foreach ($parts as $part) {
        if (preg_match('/\{([^}]+)\}/', $part, $match)) {
            $category = $match[1];
            echo '<div class="inline-block align-middle min-w-[120px] relative mx-1">';
            echo '<div class="border-b-4 border-dashed border-red-400 py-1 px-2 text-center min-h-[40px] cursor-pointer placeholder-content" data-category="'.htmlspecialchars($category).'">______</div>';
            echo '<div class="absolute -top-5 left-0 right-0 text-xs text-blue-700 font-bold text-center">'.htmlspecialchars($category).'</div>';
            echo '</div>';
        } else {
            echo '<span class="inline">'.htmlspecialchars($part).'</span>';
        }
    }
    ?>
  </div>

  <!-- Elementos -->
  <div class="bg-blue-100 rounded-lg p-5 mb-6">
    <?php foreach ($game_data['elements'] as $category => $items): ?>
      <div class="mb-6">
        <h3 class="bg-blue-800 text-white inline-block px-4 py-1 rounded-full text-sm"><?= ucfirst(htmlspecialchars($category)) ?></h3>
        <div class="flex flex-wrap gap-4 mt-4">
          <?php foreach ($items as $item): ?>
            <div class="bg-white border-2 border-blue-400 rounded-lg p-3 w-36 text-center cursor-move shadow hover:-translate-y-1 transform transition" 
                 draggable="true"
                 data-category="<?= htmlspecialchars($category) ?>"
                 data-value="<?= htmlspecialchars($item['word']) ?>"
                 data-audio="<?= $item['audio'] ?>">
              <img src="<?= $item['image'] ?>" alt="<?= htmlspecialchars($item['word']) ?>" class="w-24 h-24 object-contain mx-auto mb-2">
              <div class="font-bold h-10 flex items-center justify-center mb-1"><?= htmlspecialchars($item['word']) ?></div>
              <button class="text-red-500 hover:text-red-700 text-lg audio-btn element-audio" data-audio="<?= $item['audio'] ?>">
                <i class="fas fa-volume-up"></i>
              </button>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Botones -->
  <div class="flex justify-center gap-4 mb-6">
    <button id="reset-story" class="bg-blue-500 hover:bg-blue-700 text-white px-6 py-2 rounded-full flex items-center gap-2">
      <i class="fas fa-redo"></i> Reiniciar
    </button>
    <button id="save-story" class="bg-blue-500 hover:bg-blue-700 text-white px-6 py-2 rounded-full flex items-center gap-2">
      <i class="fas fa-save"></i> Guardar Cuento
    </button>
    <button id="read-story" class="bg-blue-500 hover:bg-blue-700 text-white px-6 py-2 rounded-full flex items-center gap-2">
      <i class="fas fa-book-reader"></i> Leer Cuento
    </button>
  </div>

  <!-- Resultado -->
  <div class="bg-green-100 border-2 border-green-600 rounded-lg p-6 hidden story-result">
    <h3 class="text-lg font-bold mb-3">Tu Cuento Completo:</h3>
    <div class="bg-white p-4 rounded-md text-lg leading-relaxed final-story mb-4"></div>
    <button id="new-story" class="bg-blue-500 hover:bg-blue-700 text-white px-6 py-2 rounded-full flex items-center gap-2">
      <i class="fas fa-plus"></i> Crear Otro Cuento
    </button>
  </div>
</div>
