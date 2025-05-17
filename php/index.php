<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once 'controllers/AuthController.php';

// Obtener la acción desde el query string
$action = $_GET['action'] ?? null;

if ($action === 'verify' && isset($_GET['token'])) {
    AuthController::verifyEmail($_GET['token']);
    exit;
}

switch ($action) {
    case 'login':
        AuthController::login($_POST);
        break;

    case 'register':
        AuthController::register($_POST);
        break;

    case 'crear_pregunta':
        require_once 'controllers/PreguntaController.php';
        PreguntaController::crearPregunta($_POST);
        break;

    case 'send_reset_link':
        AuthController::sendResetLink($_POST);
        break;

    case 'reset_password':
        AuthController::resetPassword($_POST);
        break;

    case 'logout':
        session_destroy();
        header('Location: ../php/views/index.php');
        break;

    default:
        // Vista por defecto si no hay acción definida
        header('Location: ../php/views/index.php');
        break;
}
?>
