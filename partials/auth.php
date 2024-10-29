<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php';

function registerUser($email, $password, $name) {
    global $pdo;

    // Vérifiez si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id_utilisateur FROM Utilisateur WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return false; // L'email existe déjà
    }

    // Hachez le mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insérez le nouvel utilisateur
    try {
        $stmt = $pdo->prepare("INSERT INTO Utilisateur (email, mot_de_passe, nom) VALUES (?, ?, ?)");
        $result = $stmt->execute([$email, $hashedPassword, $name]);
        if (!$result) {
            throw new Exception("Erreur lors de l'insertion de l'utilisateur.");
        }
        return $result;
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}
function loginUser($email, $password) {
    global $pdo;

    // Préparez et exécutez la requête
    $stmt = $pdo->prepare("SELECT id_utilisateur, nom, mot_de_passe FROM Utilisateur WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifiez le mot de passe
    if ($user && password_verify($password, $user['mot_de_passe'])) {
        // Régénérez l'ID de session pour éviter les attaques de fixation de session
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id_utilisateur'];
        $_SESSION['nom'] = $user['nom'];
        return true;
    } else {
        return false;
    }
}

function updatePassword($userId, $currentPassword, $newPassword) {
    global $pdo;

    // Récupérez l'utilisateur par ID
    $stmt = $pdo->prepare("SELECT mot_de_passe FROM Utilisateur WHERE id_utilisateur = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifiez l'ancien mot de passe
    if ($user && password_verify($currentPassword, $user['mot_de_passe'])) {
        // Hachez le nouveau mot de passe
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Mettez à jour le mot de passe
        $stmt = $pdo->prepare("UPDATE Utilisateur SET mot_de_passe = ? WHERE id_utilisateur = ?");
        return $stmt->execute([$hashedPassword, $userId]);
    } else {
        return false;
    }
}