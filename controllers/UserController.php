<?php
namespace App\Controllers;

use App\Models\User;

class UserController {
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }

    public function login($email, $password) {
        $user = $this->userModel->getUserByEmail($email);
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id_utilisateur'];
            $_SESSION['nom'] = $user['nom'];
            return true;
        } else {
            return false;
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
    }
}
?>