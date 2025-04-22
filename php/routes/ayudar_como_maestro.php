<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../config/Database.php';
require_once '../controllers/EventoController.php'; 
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['profesor']) || $_SESSION['profesor'] !== 'si') {
    header("Location: ../views/dashboard.php?error=no_permitido");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $correoMaestro = $_SESSION['usuario'];

    if (!$id) {
        header("Location: ../views/dashboard.php?error=id_faltante");
        exit;
    }

    $db = new Database();
    $conn = $db->getConnection();

    // 1. Verificar que la clase exista y no tenga maestro
    $stmt = $conn->prepare("SELECT alumno, sesion, maestro FROM preguntas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($alumno, $sesion, $maestroActual);
    $stmt->fetch();
    $stmt->close();

    if ($maestroActual !== null) {
        header("Location: ../views/dashboard.php?error=ya_asignada");
        exit;
    }

    // 2. Validar que falten al menos 120 minutos para la clase
    $ahora = new DateTime();
    $inicioSesion = new DateTime($sesion);
    $diffMinutos = ($inicioSesion->getTimestamp() - $ahora->getTimestamp()) / 60;

    if ($diffMinutos < 120) {
        header("Location: ../views/dashboard.php?error=tiempo_insuficiente");
        exit;
    }

    // 3. Verificar que el maestro no tenga otra clase en ese horario exacto
    $stmt = $conn->prepare("SELECT COUNT(*) FROM preguntas WHERE maestro = ? AND sesion = ?");
    $stmt->bind_param("ss", $correoMaestro, $sesion);
    $stmt->execute();
    $stmt->bind_result($clasesMismoHorario);
    $stmt->fetch();
    $stmt->close();

    if ($clasesMismoHorario > 0) {
        header("Location: ../views/dashboard.php?error=conflicto_horario");
        exit;
    }

    // 4. Contar cuántas clases pagadas lleva el maestro este mes
    $inicioMes = date("Y-m-01 00:00:00");
    $finMes = date("Y-m-t 23:59:59");

    $stmt = $conn->prepare("SELECT COUNT(*) FROM preguntas WHERE maestro = ? AND pagar = 'si' AND sesion BETWEEN ? AND ?");
    $stmt->bind_param("sss", $correoMaestro, $inicioMes, $finMes);
    $stmt->execute();
    $stmt->bind_result($clasesPagadasMes);
    $stmt->fetch();
    $stmt->close();

    //TODO: 5. Verificar si el alumno tiene beca disponible
    //$stmt = $conn->prepare("SELECT becas FROM usuarios1 WHERE correo = ?");
    //$stmt->bind_param("s", $alumno);
    //$stmt->execute();
    //$stmt->bind_result($becasDisponibles);
    //$stmt->fetch();
    //$stmt->close();

    // Determinar si es pagada o no
    $esPagada = 'no';

    // if ($clasesPagadasMes <= 10) {
    //     if ($becasDisponibles > 0) {
    //         // Usar una beca
    //         $esPagada = 'no';
    //         $stmt = $conn->prepare("UPDATE usuarios1 SET becas = becas - 1 WHERE correo = ?");
    //         $stmt->bind_param("s", $alumno);
    //         $stmt->execute();
    //         $stmt->close();
    //     } else {
    //         $esPagada = 'si';
    //     }
    // }

    // 6. Asignar al maestro y registrar si es pagada
    if ($esPagada === 'no') {
        echo '
            <script>
                var aceptarClaseGratis = confirm("Has alcanzado el límite de clases pagadas o no tienes becas disponibles. ¿Deseas dar la clase como apoyo, sin pago?");
                if (!aceptarClaseGratis) {
                    alert("No fuiste registrado como maestro para esta clase.");
                    window.location.href = "../views/dashboard.php";
                }
            </script>
        ';
    }

    $stmt = $conn->prepare("SELECT pregunta FROM preguntas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($preguntaTexto);
    $stmt->fetch();
    $stmt->close();

    $linkMeet = EventoController::crearEventoMeet($correoMaestro, $alumno, $sesion, $preguntaTexto, $id);

    $stmt = $conn->prepare("UPDATE preguntas SET maestro = ?, pagar = ?, link = ? WHERE id = ?");
    $stmt->bind_param("sssi", $correoMaestro, $esPagada, $linkMeet, $id);

    if ($stmt->execute()) {
        header("Location: ../views/dashboard.php?success=asignado");
    } else {
        header("Location: ../views/dashboard.php?error=fallo_asignacion");
    }

    $stmt->close();
    $conn->close();
}