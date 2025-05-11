<?php

class User {
    private $conn;
    private $tableUser = "usuarios1"; // Asegúrate de que esta tabla exista

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Buscar usuario por correo electrónico
     */
    public function findByEmail($email) {
        $stmt = $this->conn->prepare("SELECT usuario, profesor, contrasena, is_verified, token FROM $this->tableUser WHERE correo = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows === 1 ? $result->fetch_assoc() : null;
    }


    /**
     * Crear un nuevo usuario
     */
    public function create($fullName, $email, $username, $password, $token) {

        $profesor = 'no';
        $rfc = null;
        $activo = 'si';

        $stmt = $this->conn->prepare("INSERT INTO " . $this->tableUser . " (nombre_completo, correo, usuario, contrasena, profesor, rfc, activo, token, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("ssssssss", $fullName, $email, $username, $password, $profesor, $rfc, $activo, $token);

        return $stmt->execute();
    }

    public function verifyToken($token) {
        $stmt = $this->conn->prepare("UPDATE $this->tableUser SET is_verified = 1, token = NULL WHERE token = ?");
        $stmt->bind_param("s", $token);
        return $stmt->execute() && $stmt->affected_rows > 0;
    }
}