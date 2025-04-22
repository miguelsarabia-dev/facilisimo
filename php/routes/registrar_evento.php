<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../controllers/EventoController.php';
session_start();

$maestro = $_SESSION['usuario'];
$alumno = $_SESSION['alumno'];
$fecha_hora = $_SESSION['fecha_hora'];
$pregunta = $_SESSION['pregunta_texto'];

$exito = EventoController::crearEventoMeet($maestro, $alumno, $fecha_hora, $pregunta);

if ($exito) {
    header("Location: ../views/dashboard.php?evento=ok");
    exit();
} else {
    echo "Error al crear el evento o enviar el correo.";
}