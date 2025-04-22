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
        $stmt = $this->conn->prepare("SELECT usuario, profesor, contrasena FROM $this->tableUser WHERE correo = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows === 1 ? $result->fetch_assoc() : null;
    }


    /**
     * Crear un nuevo usuario
     */
    public function create($fullName, $email, $username, $password) {

        $profesor = 'no';
        $rfc = null;
        $activo = 'si';

        $stmt = $this->conn->prepare("INSERT INTO " . $this->tableUser . " (nombre_completo, correo, usuario, contrasena, profesor, rfc, activo) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $fullName, $email, $username, $password, $profesor, $rfc, $activo);

        return $stmt->execute();
    }
}