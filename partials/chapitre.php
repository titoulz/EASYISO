<?php
require_once '../config/database.php';

$id_chapitre = $_GET['id_chapitre'] ?? null;

if (!$id_chapitre) {
    die("ID du chapitre manquant.");
}

try {
    // Récupérer les informations du chapitre
    $stmt = $pdo->prepare("SELECT nom_chapitre, description FROM Chapitre WHERE id_chapitre = ?");
    $stmt->execute([$id_chapitre]);
    $chapitre = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$chapitre) {
        die("Chapitre non trouvé.");
    }

    // Récupérer les sous-chapitres associés
    $stmt = $pdo->prepare("SELECT id_souschapitre, nom_souschapitre FROM SousChapitre WHERE id_chapitre = ? ORDER BY ordre");
    $stmt->execute([$id_chapitre]);
    $sousChapitres = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($chapitre['nom_chapitre']); ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/assets/css/navbar.css"> <!-- Inclure le fichier CSS personnalisé -->
</head>
<body>
    <?php require_once '../partials/header.php'; ?>
    <div class="container mt-5">
        <h1><?php echo htmlspecialchars($chapitre['nom_chapitre']); ?></h1>
        <p><?php echo htmlspecialchars($chapitre['description']); ?></p>
        <div>
            <h2>Sous-Chapitres</h2>
            <ul class="list-group">
                <?php foreach ($sousChapitres as $sousChapitre): ?>
                    <li class="list-group-item">
                        <a href="souschapitres.php?id_souschapitre=<?php echo $sousChapitre['id_souschapitre']; ?>">
                            <?php echo htmlspecialchars($sousChapitre['nom_souschapitre']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php require_once '../partials/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>