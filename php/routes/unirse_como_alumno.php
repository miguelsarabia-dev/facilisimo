<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/Database.php';
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../views/dashboard.php?error=no_sesion");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $correoAlumno = $_SESSION['usuario'];

    if (!$id) {
        header("Location: ../views/dashboard.php?error=id_faltante");
        exit;
    }

    $db = new Database();
    $conn = $db->getConnection();

    // Verificar que la clase tenga maestro
    $stmt = $conn->prepare("SELECT maestro, alumno FROM preguntas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($maestro, $alumnoActual);
    $stmt->fetch();
    $stmt->close();

    // Normalizar el valor del campo alumno
    $alumnoActual = $alumnoActual ?? '';
    $lista = $alumnoActual !== '' ? explode(',', $alumnoActual) : [];

    if ($maestro === null) {
        header("Location: ../views/dashboard.php?error=sin_maestro");
        exit;
    }

    if (in_array($correoAlumno, $lista)) {
        header("Location: ../views/dashboard.php?error=ya_unido");
        exit;
    }

    // Verificar límite de caracteres
    $longitudNueva = strlen($alumnoActual) + strlen($correoAlumno) + 1;
    if ($longitudNueva > 3000) {
        header("Location: ../views/dashboard.php?error=limite_alumnos");
        exit;
    }

    // Agregar nuevo alumno a la lista
    $alumnosActualizados = $alumnoActual ? $alumnoActual . ',' . $correoAlumno : $correoAlumno;

    // Asignar alumno
    $stmt = $conn->prepare("UPDATE preguntas SET alumno = ? WHERE id = ?");
    $stmt->bind_param("si", $alumnosActualizados, $id);

    if ($stmt->execute()) {
        header("Location: ../views/dashboard.php?success=unido");
    } else {
        header("Location: ../views/dashboard.php?error=fallo_union");
    }

    $stmt->close();
    $conn->close();
}