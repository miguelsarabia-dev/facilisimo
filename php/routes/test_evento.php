<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$_SESSION['usuario'] = 'msarabiagardea4@gmail.com';
$_SESSION['alumno'] = 'miguel-sarabia.dev@outlook.com';
$_SESSION['fecha_hora'] = '2025-04-07 12:00:00';
$_SESSION['pregunta_texto'] = '¿Puedes explicarme funciones en PHP?';

header('Location: registrar_evento.php');
exit();