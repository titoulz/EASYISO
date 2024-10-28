<?php
namespace App\Models;

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Utilisateur WHERE id_utilisateur = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function createUser($nom, $prenom, $email, $mot_de_passe) {
        // Vérifier si l'email existe déjà
        $stmt = $this->pdo->prepare("SELECT * FROM Utilisateur WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            return false; // L'email existe déjà
        }

        // Insérer le nouvel utilisateur
        $stmt = $this->pdo->prepare("INSERT INTO Utilisateur (nom, email, mot_de_passe) VALUES (:nom, :email, :mot_de_passe)");
        $stmt->execute([
            'nom' => $nom,
            'email' => $email,
            'mot_de_passe' => $mot_de_passe
        ]);
        return true;
    }

    public function verifyUser($email, $mot_de_passe) {
        $stmt = $this->pdo->prepare("SELECT * FROM Utilisateur WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            return $user;
        }
        return false;
    }
}