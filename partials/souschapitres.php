<?php
require_once '../config/database.php';

$id_souschapitre = $_GET['id_souschapitre'] ?? null;

if (!$id_souschapitre) {
    die("ID du sous-chapitre manquant.");
}

try {
    // Récupérer les informations du sous-chapitre
    $stmt = $pdo->prepare("SELECT nom_souschapitre, description FROM SousChapitre WHERE id_souschapitre = ?");
    $stmt->execute([$id_souschapitre]);
    $sousChapitre = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sousChapitre) {
        die("Sous-chapitre non trouvé.");
    }

    // Récupérer le contenu du sous-chapitre
    $stmt = $pdo->prepare("SELECT type_contenu, contenu FROM ContenuChapitre WHERE id_chapitre = ?");
    $stmt->execute([$id_souschapitre]);
    $contenus = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($sousChapitre['nom_souschapitre']); ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/assets/css/navbar.css"> <!-- Inclure le fichier CSS personnalisé -->
</head>
<body>
    <?php require_once '../partials/header.php'; ?>
    <div class="container mt-5">
        <h1><?php echo htmlspecialchars($sousChapitre['nom_souschapitre']); ?></h1>
        <p><?php echo htmlspecialchars($sousChapitre['description']); ?></p>
        <div>
            <h2>Contenu</h2>
            <?php foreach ($contenus as $contenu): ?>
                <h3><?php echo htmlspecialchars($contenu['type_contenu']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($contenu['contenu'])); ?></p>
            <?php endforeach; ?>
        </div>
    </div>
    <?php require_once '../partials/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>