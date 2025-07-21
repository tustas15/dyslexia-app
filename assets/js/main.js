// Funciones globales para todos los juegos
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar Howler.js para audio
    if (typeof Howl !== 'undefined') {
        window.soundManager = {
            sounds: {},
            play: function(name, file) {
                if (!this.sounds[name]) {
                    this.sounds[name] = new Howl({
                        src: [file],
                        html5: true
                    });
                }
                this.sounds[name].play();
            }
        };
    }
    
    // Configuración de accesibilidad
    document.addEventListener('keydown', (e) => {
        // Tecla R para repetir audio
        if (e.key === 'r' || e.key === 'R') {
            const audioBtn = document.querySelector('.audio-btn');
            if (audioBtn) audioBtn.click();
        }
        
        // Tecla N para siguiente
        if (e.key === 'n' || e.key === 'N') {
            const nextBtn = document.querySelector('.next-btn');
            if (nextBtn && !nextBtn.classList.contains('hidden')) {
                nextBtn.click();
            }
        }
    });
    
    // Hacer elementos arrastrables
    if (typeof Draggable !== 'undefined') {
        document.querySelectorAll('[draggable="true"]').forEach(item => {
            new Draggable.Draggable(item, {
                plugins: [Draggable.Plugins.Sortable]
            });
        });
    }
    
    // Inicializar animaciones
    if (typeof anime !== 'undefined') {
        document.querySelectorAll('.game-card').forEach((card, index) => {
            anime({
                targets: card,
                opacity: [0, 1],
                translateY: [20, 0],
                duration: 600,
                delay: index * 100,
                easing: 'easeOutQuad'
            });
        });
    }
});

// Función para guardar progreso
function saveGameProgress(gameType, score, details = {}) {
    const data = {
        game_type: gameType,
        score: score,
        details: details
    };
    
    fetch('/api/save-progress.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Error al guardar progreso:', data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}