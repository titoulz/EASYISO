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

        // Créer un nouvel utilisateur
        $hashedPassword = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO Utilisateur (nom, prenom, email, mot_de_passe) VALUES (:nom, :prenom, :email, :mot_de_passe)");
        return $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'mot_de_passe' => $hashedPassword
        ]);
    }

    public function getUserByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM Utilisateur WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }
}
?>