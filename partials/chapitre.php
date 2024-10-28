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

    // Récupérer le contenu du chapitre
    $stmt = $pdo->prepare("SELECT type_contenu, contenu FROM ContenuChapitre WHERE id_chapitre = ?");
    $stmt->execute([$id_chapitre]);
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
    <title><?php echo htmlspecialchars($chapitre['nom_chapitre']); ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once '../partials/header.php'; ?>
    <div class="container mt-5">
        <h1><?php echo htmlspecialchars($chapitre['nom_chapitre']); ?></h1>
        <p><?php echo htmlspecialchars($chapitre['description']); ?></p>
        <div>
            <?php foreach ($contenus as $contenu): ?>
                <h3><?php echo htmlspecialchars($contenu['type_contenu']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($contenu['contenu'])); ?></p>
            <?php endforeach; ?>
        </div>
    </div>
    <?php require_once '../partials/footer.php'; ?>
</body>
</html>