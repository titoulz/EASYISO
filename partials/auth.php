<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php';

function loginUser($email, $password) {
    global $pdo; // Utiliser la connexion PDO existante

    // Préparez et exécutez la requête
    $stmt = $pdo->prepare("SELECT id_utilisateur, nom, mot_de_passe FROM Utilisateur WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifiez le mot de passe
    if ($user && password_verify($password, $user['mot_de_passe'])) {
        $_SESSION['user_id'] = $user['id_utilisateur'];
        $_SESSION['nom'] = $user['nom'];
        return true;
    } else {
        return false;
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function logoutUser() {
    session_unset();
    session_destroy();
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /public/index.php?action=login&error=Vous devez être connecté pour accéder à cette page.");
        exit();
    }
}
?>