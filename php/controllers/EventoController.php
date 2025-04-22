<?php
require_once __DIR__ . '/../helpers/CorreoHelper.php';
require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

use Google\Client as Google_Client;
use Google\Service\Calendar as Google_Service_Calendar;
use Google\Service\Calendar\Event as Google_Service_Calendar_Event;
use Google\Service\Calendar\EventDateTime;

class EventoController {
    public static function crearEventoMeet($maestro, $alumno, $fecha_hora, $pregunta_texto) {
        error_log("✅ Entró al método crearEventoMeet con maestro: $maestro, alumno: $alumno, fecha: $fecha_hora");

        
        $client = new Google_Client();
        
        $auth = [
            'client_id' => $_ENV['GOOGLE_CLIENT_ID'],
            'client_secret' => $_ENV['GOOGLE_CLIENT_SECRET'],
            'access_token' => $_ENV['GOOGLE_ACCESS_TOKEN'],
            'refresh_token' => $_ENV['GOOGLE_REFRESH_TOKEN'],
            'expires_in' => $_ENV['GOOGLE_EXPIRES_IN'],
            'created' => $_ENV['GOOGLE_CREATED'],
            'token_type' => $_ENV['GOOGLE_TOKEN_TYPE'],
            'scope' => $_ENV['GOOGLE_SCOPE']
        ];

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


        $service = new Google_Service_Calendar($client);

        $event = new Google_Service_Calendar_Event([
            'summary' => 'Clase FACILISIMO',
            'description' => $pregunta_texto,
            'start' => [
                'dateTime' => date('c', strtotime($fecha_hora)),
                'timeZone' => 'Etc/UTC'
            ],
            'end' => [
                'dateTime' => date('c', strtotime($fecha_hora . ' +1 hour')),
                'timeZone' => 'Etc/UTC'
            ],
            'attendees' => [
                ['email' => $maestro],
                ['email' => $alumno]
            ],
            'conferenceData' => [
                'createRequest' => [
                    'requestId' => uniqid(),
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet']
                ]
            ]
        ]);

        $optParams = ['conferenceDataVersion' => 1];

        
        $event = $service->events->insert('primary', $event, $optParams);
        $meetLink = $event->getHangoutLink(); 

        CorreoHelper::enviarCorreoClase($maestro, $alumno, $pregunta_texto, $meetLink);
        return $meetLink;
        
    }
}