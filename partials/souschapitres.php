<?php
require_once '../config/database.php';

$id_chapitre = $_GET['id_chapitre'] ?? null;

if (!$id_chapitre) {
    die("ID du chapitre manquant.");
}

try {
    // Récupérer les informations du chapitre
    $stmt = $pdo->prepare("SELECT nom_chapitre FROM Chapitre WHERE id_chapitre = ?");
    $stmt->execute([$id_chapitre]);
    $chapitre = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$chapitre) {
        die("Chapitre non trouvé.");
    }

    // Récupérer les sous-chapitres associés
    $stmt = $pdo->prepare("SELECT id_souschapitre, nom_souschapitre FROM SousChapitre WHERE id_chapitre = ?");
    $stmt->execute([$id_chapitre]);
    $sousChapitres = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Sous-chapitres de <?= htmlspecialchars($chapitre['nom_chapitre']) ?></title>
    <!-- Inclure Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Sous-chapitres de <?= htmlspecialchars($chapitre['nom_chapitre']) ?></h1>
        <ul class="list-group">
            <?php foreach ($sousChapitres as $sousChapitre): ?>
                <li class="list-group-item">
                    <a href="cours.php?id_souschapitre=<?= $sousChapitre['id_souschapitre'] ?>">
                        <?= htmlspecialchars($sousChapitre['nom_souschapitre']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <!-- Inclure Bootstrap JS et ses dépendances -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>