<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a href="/public/index.php" class="navbar-brand">IALEARNING</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a href="/public/index.php?action=dashboard" class="nav-link">Tableau de bord</a></li>
                    <li class="nav-item"><a href="/public/index.php?action=logout" class="nav-link">Déconnexion</a></li>
                <?php else: ?>
                    <li class="nav-item"><a href="/public/index.php?action=register" class="nav-link">Inscription</a></li>
                    <li class="nav-item"><a href="/public/index.php?action=login" class="nav-link">Connexion</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>