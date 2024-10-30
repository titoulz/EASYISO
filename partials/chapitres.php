<?php
require_once '../config/database.php';

if (isset($_GET['id_theme'])) {
    $id_theme = intval($_GET['id_theme']);

    try {
        // Récupérer les chapitres pour le thème sélectionné
        $stmt = $pdo->prepare("SELECT id_chapitre, nom_chapitre, description FROM chapitre WHERE id_theme = :id_theme");
        $stmt->bindParam(':id_theme', $id_theme, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt;
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
} else {
    die("ID du thème non spécifié.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Chapitres</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Liste des Chapitres</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
    <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
            <td><a href="souschapitre.php?id_souschapitre=<?php echo htmlspecialchars($row['id_chapitre']); ?>"><?php echo htmlspecialchars($row['nom_chapitre']); ?></a></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
        </tr>
    <?php endwhile; ?>
</tbody>
    </table>
</body>
</html>