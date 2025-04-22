<?php
class Pregunta {
    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    public function registrar($texto, $fechaCreacion, $fechaSesion, $correo, $maestro, $pagar, $link) {
        $stmt = $this->conn->prepare("INSERT INTO preguntas (pregunta, creacion, sesion, alumno, maestro, pagar, link) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) return false;

        $stmt->bind_param("sssssss", $texto, $fechaCreacion, $fechaSesion, $correo, $maestro, $pagar, $link);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    public function obtenerPorMaestro($correo) {
        $stmt = $this->conn->prepare("SELECT * FROM preguntas WHERE maestro = ? ORDER BY sesion ASC");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $preguntas = $resultado->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $preguntas;
    }

    public function obtenerPorAlumno($correo) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM preguntas 
             WHERE FIND_IN_SET(?, alumno)
             ORDER BY sesion ASC"
        );
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $preguntas = $resultado->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $preguntas;
    }

    public function obtenerSinMaestro() {
        $hace22Horas = date('Y-m-d H:i:s', strtotime('-22 hours'));
        $stmt = $this->conn->prepare(
            "SELECT * FROM preguntas 
            WHERE maestro IS NULL 
            AND creacion >= ?
            ORDER BY sesion ASC"
            );
        $stmt->bind_param("s", $hace22Horas);    
        $stmt->execute();
        $resultado = $stmt->get_result();
        $preguntas = $resultado->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $preguntas;
    }

    public function obtenerDisponiblesParaAlumno($correo) {
        $hace22Horas = date('Y-m-d H:i:s', strtotime('-22 hours'));
        $stmt = $this->conn->prepare(
            "SELECT * FROM preguntas 
             WHERE maestro != '' 
             AND maestro != ? 
             AND NOT FIND_IN_SET(?, alumno)
             AND creacion >= ?
             ORDER BY sesion ASC"
        );
        $stmt->bind_param("sss", $correo, $correo, $hace22Horas);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $preguntas = $resultado->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $preguntas;
    }
}