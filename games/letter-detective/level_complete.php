<?php
require_once '../../includes/auth.php';
safe_session_start();

if (!is_logged_in()) {
    header('Location: ../../user/login.php');
    exit;
}
?>

<div class="level-selector grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
    <!-- Nivel Fácil -->
    <div class="level-card bg-green-100 rounded-xl p-6 text-center">
        <div class="icon bg-green-200 w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center">
            <i class="fas fa-seedling text-green-600 text-2xl"></i>
        </div>
        <h3 class="text-xl font-bold text-green-700 mb-2">Fácil</h3>
        <p class="text-gray-600 mb-4">Letras básicas sin rotación</p>
        <a href="index.php?level=1" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg">
            Jugar
        </a>
    </div>
    
    <!-- Nivel Medio -->
    <div class="level-card bg-yellow-100 rounded-xl p-6 text-center">
        <div class="icon bg-yellow-200 w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center">
            <i class="fas fa-tree text-yellow-600 text-2xl"></i>
        </div>
        <h3 class="text-xl font-bold text-yellow-700 mb-2">Medio</h3>
        <p class="text-gray-600 mb-4">Rotación 90° con tiempo</p>
        <a href="index.php?level=2" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg">
            Jugar
        </a>
    </div>
    
    <!-- Nivel Difícil -->
    <div class="level-card bg-red-100 rounded-xl p-6 text-center">
        <div class="icon bg-red-200 w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center">
            <i class="fas fa-mountain text-red-600 text-2xl"></i>
        </div>
        <h3 class="text-xl font-bold text-red-700 mb-2">Difícil</h3>
        <p class="text-gray-600 mb-4">Rotación 180° con tiempo limitado</p>
        <a href="index.php?level=3" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg">
            Jugar
        </a>
    </div>
</div>