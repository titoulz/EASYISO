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
        <p>bienvenu sur ISO 27001</p>
        <p> Cette plateforme propose une gamme complète de templates personnalisés pour la certification ISO 27001, conçus pour simplifier et accélérer la mise en conformité des entreprises avec les normes de sécurité de l'information.</p>
        <h4 class="text-center">ACTIONS:</h4>
        <br>
    <a class="btn btn-primary" href="/partials/clauses.php">VOIR LES CLAUSES</a>
    <a class="btn btn-primary" href="/partials/documents.php">VOIR MES DOCUMENTS</a>
    <a class="btn btn-primary" href="/partials/gestion_clause.php">VOIR MES CLAUSES</a>
    <a class="btn btn-primary" href="/partials/mycorp.php">GERER MON ENTREPRISE</a>
    <a class="btn btn-primary" href="index.php?action=dashboard">GERER MON COMPTE</a>
    <a class="btn btn-primary" href="/partials/api/chat.php">Poser une Question</a>
</div>
        <?php require_once '../partials/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>