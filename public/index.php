<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';
require_once '../partials/auth.php';

$action = $_GET['action'] ?? '';

if ($action == 'register') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $mot_de_passe = $_POST['mot_de_passe'];

        error_log("Form submitted: $email, $nom, $prenom");

        if (registerUser($email, $mot_de_passe, $nom)) {
            header("Location: /views/login.php");
            exit();
        } else {
            $error = "L'email existe déjà.";
        }
    }
    require_once '../views/register.php';
    exit();
}

if ($action == 'login') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $mot_de_passe = $_POST['mot_de_passe'];

        error_log("Login attempt: $email");

        if (loginUser($email, $mot_de_passe)) {
            header("Location: /views/dashboard.php");
            exit();
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    }
    require_once '../views/login.php';
    exit();
}
if ($action == 'dashboard') {
    // Vérifiez si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        header("Location: /views/login.php");
        exit();
    }
    // Chargez le tableau de bord
    require_once '../views/dashboard.php';
    exit();
}
//logique de routage pour action=logout
if ($action == 'logout') {
    // Déconnectez l'utilisateur
    session_start();
    session_destroy();
    header("Location: /public/index.php");
    exit();
}

// Autres logiques de routage...
?>
<DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/../public/assets/css/style.css">
</head>
<body>
    <?php require_once '../partials/header.php'; ?>
    <div class="container mt-5">
        <h1>Accueil</h1>
        <p>Bienvenue sur IALEARNING, la plateforme d'apprentissage de l'intelligence artificielle.</p>
    <?php require_once '../partials/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>