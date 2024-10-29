<?php
require_once '../config/database.php';

$id_matiere = $_GET['id_matiere'] ?? null;

if (!$id_matiere) {
    die("ID de la matière manquant.");
}

try {
    // Récupérer les informations de la matière
    $stmt = $pdo->prepare("SELECT nom_matiere FROM matiere WHERE id_matiere = ?");
    $stmt->execute([$id_matiere]);
    $matiere = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$matiere) {
        die("Matière non trouvée.");
    }

    // Récupérer les chapitres associés
    $stmt = $pdo->prepare("SELECT id_chapitre, nom_chapitre FROM Chapitre WHERE id_matiere = ?");
    $stmt->execute([$id_matiere]);
    $chapitres = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
$xyz=$matiere['nom_matiere'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($matiere['nom_matiere']); ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/assets/css/navbar.css"> <!-- Inclure le fichier CSS personnalisé -->
</head>
<body>
    <?php require_once '../partials/header.php'; ?>
    <div class="container mt-5">
        <h1><?php echo htmlspecialchars($xyz); ?></h1>
        <ul class="list-group">
            <?php foreach ($chapitres as $chapitre): ?>
                <li class="list-group-item">
                    <a href="chapitre.php?id_chapitre=<?php echo $chapitre['id_chapitre']; ?>">
                        <?php echo htmlspecialchars($chapitre['nom_chapitre']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php require_once '../partials/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>