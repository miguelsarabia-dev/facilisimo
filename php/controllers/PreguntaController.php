<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Pregunta.php';

class PreguntaController {
    public static function crearPregunta($input) {

        $input = trim($_POST['input'] ?? '');
        $correo = $_SESSION['usuario'] ?? '';

        if (empty($input)) {
            $_SESSION['error'] = 'vacio';
            header("Location: views/dashboard.php");
            exit;
        }

        $now = date('Y-m-d H:i:s');
        $tomorrow = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($now)));
        $tomorrow = date('Y-m-d H:00:00', strtotime($tomorrow));

        $creacion = $now;
        $sesion = $tomorrow;

        $db = new Database();
        $conn = $db->getConnection();
        $preguntaModel = new Pregunta($conn);
        $maestro = null;
        $pagar = null;
        $link = null;

        if ($preguntaModel->registrar($input, $creacion, $sesion, $correo, $maestro, $pagar, $link)) {
            $_SESSION['success'] = 'pregunta_creada';
            header("Location: views/dashboard.php");
        } else {
            $_SESSION['error'] = 'bd';
            header("Location: views/dashboard.php");
        }
    }
}