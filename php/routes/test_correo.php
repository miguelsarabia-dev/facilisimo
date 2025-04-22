<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../helpers/CorreoHelper.php';

// Datos de prueba
$maestro = 'mas_g_10@hotmail.com';
$alumno = 'miguel-sarabia.dev@outlook.com'; // Usa tu correo aquí para recibir el email
$pregunta_texto = '¿Puedes explicarme cómo usar Git y GitHub?';
$googleMeetLink = 'https://meet.google.com/test-enlace';

$resultado = CorreoHelper::enviarCorreoClase($maestro, $alumno, $pregunta_texto, $googleMeetLink);

if ($resultado) {
    echo "✅ Correo enviado correctamente.";
} else {
    echo "❌ Hubo un error al enviar el correo. Revisa el log de errores.";
}