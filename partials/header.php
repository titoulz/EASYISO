<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php';

$matieres = [];
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->query("SELECT id_matiere, nom_matiere FROM Matiere");
        $matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IALEARNING</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/assets/css/navbar.css"> <!-- Inclure le fichier CSS personnalisé -->
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a href="/public/index.php" class="navbar-brand">IALEARNING</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="matiereDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Matières
                        </a>
                        <div class="dropdown-menu" aria-labelledby="matiereDropdown">
                            <?php foreach ($matieres as $matiere): ?>
                                <a class="dropdown-item" href="/partials/chapitres.php?id_matiere=<?php echo $matiere['id_matiere']; ?>">
                                    <?php echo htmlspecialchars($matiere['nom_matiere']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </li>
                    <li class="nav-item"><a href="/public/index.php?action=dashboard" class="nav-link">Tableau de bord</a></li>
                    <li class="nav-item"><a href="/partials/logout.php" class="nav-link">Déconnexion</a></li>
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