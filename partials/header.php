<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__.'/../config/database.php';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EASYISO</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="/public/assets/css/navbar.css"> <!-- Inclure le fichier CSS personnalisé -->
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a href="/public/index.php" class="navbar-brand">EASYISO</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                 
                    <li class="nav-item"><a href="/partials/api/chat.php" class="nav-link"><i class="fas fa-comments"></i> Chat</a></li>
                    <li class="nav-item"><a href="/public/index.php?action=dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
                    <li class="nav-item"><a href="/partials/mycorp.php" class="nav-link"><i class="fas fa-question"></i> Mon entreprise</a></li>

                    <!-- Sous-menu "Point de contrôles" -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="pointDeControlesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-book"></i> Point de contrôles
                        </a>
                        <div class="dropdown-menu" aria-labelledby="pointDeControlesDropdown">
                            <a class="dropdown-item" href="/partials/clauses.php"><i class="fas fa-book"></i> Liste des points de controles</a>
                            <a class="dropdown-item" href="/partials/documents.php"><i class="fas fa-file-alt"></i> Mes points de contrôles</a>
                            <a class="dropdown-item" href="/partials/gestion_clause.php"><i class="fas fa-cogs"></i> Gestion des points de contrôles</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="pointDeControlesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-book"></i> CLauses
                        </a>
                        <div class="dropdown-menu" aria-labelledby="pointDeControlesDropdown">
                            <a class="dropdown-item" href="/partials/gestion_iso_clauses.php"><i class="fas fa-book"></i> Gestion des Clauses</a>
                           
                        </div>
                    </li>

                    <li class="nav-item"><a href="/partials/logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                <?php else: ?>
                    <li class="nav-item"><a href="/views/register.php" class="nav-link">Inscription</a></li>
                    <li class="nav-item"><a href="/views/login.php" class="nav-link">Connexion</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
