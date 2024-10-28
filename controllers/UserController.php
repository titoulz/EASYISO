<?php
namespace App\Controllers;

use App\Models\User;

class UserController {
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }

    public function showProfile($id) {
        $user = $this->userModel->getUserById($id);
        include __DIR__ . '/../views/userProfile.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $email = $_POST['email'];
            $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_BCRYPT);

            if ($this->userModel->createUser($nom, $prenom, $email, $mot_de_passe)) {
                echo "Compte créé avec succès !";
            } else {
                $error = "L'adresse email est déjà utilisée.";
                include __DIR__ . '/../views/register.php';
            }
        } else {
            include __DIR__ . '/../views/register.php';
        }
    }
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $mot_de_passe = $_POST['mot_de_passe'];
    
            if ($user = $this->userModel->verifyUser($email, $mot_de_passe)) {
                echo "Connexion réussie !";
                // Rediriger vers la page de profil ou une autre page sécurisée
            } else {
                $error = "Email ou mot de passe incorrect.";
                include __DIR__ . '/../views/login.php';
            }
        } else {
            echo "Affichage du formulaire de connexion"; // Message de débogage
            include __DIR__ . '/../views/login.php';
        }
    }
}