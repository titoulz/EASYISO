<?php
require_once '../config/database.php';

try {
    $stmt = $pdo->query("SELECT id_matiere, nom_matiere FROM Matiere");
    $matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Matières</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once '../partials/header.php'; ?>
    <div class="container mt-5">
        <h1>Liste des Matières</h1>
        <ul class="list-group">
            <?php foreach ($matieres as $matiere): ?>
                <li class="list-group-item">
                    <a href="chapitres.php?id_matiere=<?php echo $matiere['id_matiere']; ?>">
                        <?php echo htmlspecialchars($matiere['nom_matiere']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php require_once '../partials/footer.php'; ?>
</body>
</html>