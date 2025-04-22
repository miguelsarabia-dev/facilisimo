<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();

$client = new Google\Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
$client->addScope([
    'https://www.googleapis.com/auth/userinfo.email',
    'https://www.googleapis.com/auth/userinfo.profile',
    'https://www.googleapis.com/auth/gmail.send',
    'https://www.googleapis.com/auth/calendar',
    'https://www.googleapis.com/auth/calendar.events',
    'https://www.googleapis.com/auth/calendar.calendars',
]);

$client->setAccessType('offline');
$client->setPrompt('consent');

if (!isset($_GET['code'])) {
    // Paso 1: redirigir al login de Google
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
    exit;
} else {
    // Paso 2: intercambiar el código por el token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    if (isset($token['error'])) {
        echo "❌ Error: " . $token['error_description'];
        exit;
    }

    // Guardar token y datos del usuario
    $client->setAccessToken($token);

    // Obtener info del usuario
    $oauth2 = new Google\Service\Oauth2($client);
    $userInfo = $oauth2->userinfo->get();

    // Guardar todo en un archivo .json
    $data = array_merge($token, [
        'client_id' => $client->getClientId(),
        'client_secret' => $client->getClientSecret(),
        'user_email' => $userInfo->email
    ]);

    file_put_contents(__DIR__ . '/php/config/gmail_credentials.json', json_encode($data, JSON_PRETTY_PRINT));

    echo "✅ ¡Token guardado! Puedes cerrar esta ventana.";
}