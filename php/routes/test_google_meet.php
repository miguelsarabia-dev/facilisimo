<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../vendor/autoload.php';

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;

$auth = json_decode(file_get_contents(__DIR__ . '/../config/gmail_credentials.json'), true);

$client = new Client();
$client->setClientId($auth['client_id']);
$client->setClientSecret($auth['client_secret']);
$client->setAccessToken([
    'access_token' => $auth['access_token'],
    'refresh_token' => $auth['refresh_token'],
    'expires_in' => $auth['expires_in'],
    'created' => $auth['created'],
    'token_type' => $auth['token_type'],
    'scope' => $auth['scope']
]);

$client->setScopes([
    Google_Service_Calendar::CALENDAR,
    Google_Service_Calendar::CALENDAR_EVENTS,
    Google_Service_Calendar::CALENDAR_EVENTS_READONLY,
    'https://www.googleapis.com/auth/calendar',
    'https://www.googleapis.com/auth/calendar.events'
]);

$client->setAccessType('offline');

if ($client->isAccessTokenExpired()) {
    $client->fetchAccessTokenWithRefreshToken($auth['refresh_token']);
}

$calendarService = new Calendar($client);

// Crear evento
$event = new Event([
    'summary' => 'Clase de prueba con Google Meet',
    'description' => 'Este evento fue generado como prueba desde PHP usando la API de Google Calendar.',
    'start' => new EventDateTime([
        'dateTime' => date('c', strtotime('+1 hour')),
        'timeZone' => 'America/Mexico_City',
    ]),
    'end' => new EventDateTime([
        'dateTime' => date('c', strtotime('+2 hour')),
        'timeZone' => 'America/Mexico_City',
    ]),
    'conferenceData' => [
        'createRequest' => [
            'requestId' => uniqid(),
            'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
        ],
    ],
]);

$optParams = ['conferenceDataVersion' => 1];

try {
    $createdEvent = $calendarService->events->insert('primary', $event, $optParams);
    echo "✅ Evento creado con éxito.<br>";
    echo "🔗 Link de Google Meet: <a href='" . $createdEvent->getHangoutLink() . "' target='_blank'>" . $createdEvent->getHangoutLink() . "</a>";
} catch (Exception $e) {
    echo "❌ Error al crear el evento: " . $e->getMessage();
}