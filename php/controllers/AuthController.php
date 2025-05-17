<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/CorreoHelper.php';

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
            if (!$user['is_verified']) {
                header("Location: views/index.php?error=not_verified");
                exit;
            }
            session_start();
            $_SESSION['usuario'] = $email;
            $_SESSION['profesor'] = $user['profesor'];
            $_SESSION['fullName'] = $user['usuario'];
            header("Location: views/dashboard.php");
        } else {
            header("Location: views/index.php?error=invalid_data");
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
        $userModel = new User($conn);
        //$usuario = User::findByEmail($email);
        // Instanciar modelo y buscar usuario
       
        if (empty($fullName) || empty($email) || empty($username) || empty($password)) {
            header('Location: views/index.php?error=empty');
            exit;
        }

        $userMail = $userModel->findByEmail($email);

        if ($userMail) {
            header("Location: views/index.php?error=email_found");
            exit;
        }

        $passwordHash = password_hash($post['contrasena'], PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(16));

        
        // $user = $userModel->crear($fullName, $email, $username, $password);

        if ($user = $userModel->create($fullName, $email, $username, $passwordHash, $token)) {
            CorreoHelper::enviarCorreoVerificacion($email, $token);
            header("Location: views/index.php?message=registered");
        } else {
            header("Location: views/index.php?error=register");
        }
    }

    public static function verifyEmail($token) {
        $db = new Database();
        $conn = $db->getConnection();
        $userModel = new User($conn);
        if ($userModel->verifyToken($token)) {
            header("Location: views/index.php?message=verified");
        } else {
            header("Location: views/index.php?error=invalid_token");
        }
    }

    public static function sendResetLink($post) {
        if (empty($post['correo'])) {
            header("Location: views/index.php?error=empty");
            exit;
        }

        $email = $post['correo'];

        $db = new Database();
        $conn = $db->getConnection();
        $userModel = new User($conn);

        $user = $userModel->findByEmail($email);

        if (!$user) {
            header("Location: views/index.php?error=email_not_found");
            exit;
        }

        $token = bin2hex(random_bytes(16));

        if ($userModel->setResetToken($email, $token)) {
            CorreoHelper::enviarEnlaceRecuperacion($email, $token);
            header("Location: views/index.php?message=reset_sent");
        } else {
            header("Location: views/index.php?error=reset_error");
        }
    }


    public static function resetPassword($post) {
        if (empty($post['token']) || empty($post['new_password']) || empty($post['confirm_password'])) {
            header("Location: views/index.php?error=empty");
            exit;
        }

        $token = $post['token'];
        $newPassword = $post['new_password'];
        $confirmPassword = $post['confirm_password'];

        if ($newPassword !== $confirmPassword) {
            header("Location: views/index.php?action=reset&token=$token&error=password_mismatch");
            exit;
        }

        $db = new Database();
        $conn = $db->getConnection();
        $userModel = new User($conn);

        $user = $userModel->findByResetToken($token);
        if (!$user) {
            header("Location: views/index.php?error=invalid_token");
            exit;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        if ($userModel->updatePasswordByToken($token, $hashedPassword)) {
            header("Location: views/index.php?message=password_updated");
        } else {
            header("Location: views/index.php?error=update_failed");
        }
    }
}