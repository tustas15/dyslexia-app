<?php
require_once 'config.php';
require_once 'database.php';

// Función para manejar sesiones seguramente
function safe_session_start() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function login($username, $password) {
    global $db;
    
    $stmt = $db->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            return true;
        }
    }
    return false;
}

function logout() {
    session_unset();
    session_destroy();
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

function register($username, $password, $age) {
    global $db;
    
    // Verificar si el usuario ya existe
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return false; // Usuario ya existe
    }
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (username, password, age) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $username, $hashed_password, $age);
    return $stmt->execute();
}