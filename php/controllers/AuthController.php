<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    public static function login($post) {
        
        $email = $post['correo'];
        $password = $post['contrasena'];

        if (empty($email) || empty($password)) {
            header('Location: views/index.php?error=empty');
            exit;
        }


        // Obtener conexión a la base de datos
        $db = new Database();
        $conn = $db->getConnection();

        //$usuario = User::findByEmail($email);
        // Instanciar modelo y buscar usuario
        $userModel = new User($conn);
        $user = $userModel->findByEmail($email);

        if ($user && password_verify($password, $user['contrasena'])) {
     
            session_start();
            $_SESSION['usuario'] = $email;
            $_SESSION['profesor'] = $user['profesor'];
            $_SESSION['fullName'] = $user['usuario'];
            header("Location: views/dashboard.php");
        } else {
            header("Location: views/index.php?error=1");
        }
    }

    public static function register($post) {

        $fullName = $post['nombre'];
        $email = $post['correo'];
        $username = $post['usuario'];
        $password = $post['contrasena'];

        // Obtener conexión a la base de datos
        $db = new Database();
        $conn = $db->getConnection();

        //$usuario = User::findByEmail($email);
        // Instanciar modelo y buscar usuario
       
        if (empty($fullName) || empty($email) || empty($username) || empty($password)) {
            header('Location: views/index.php?error=empty');
            exit;
        }

        $passwordHash = password_hash($post['contrasena'], PASSWORD_DEFAULT);

        $userModel = new User($conn);
        // $user = $userModel->crear($fullName, $email, $username, $password);


        if ($user = $userModel->create($fullName, $email, $username, $passwordHash)) {
            header("Location: views/index.php?message=registered");
        } else {
            header("Location: views/index.php?error=register");
        }
    }
}