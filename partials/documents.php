<?php
// Inclure le fichier de configuration pour la connexion à la base de données
require_once __DIR__.'/../config/database.php';
session_start();

// Vérification de l'utilisateur connecté
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo '<p>Utilisateur non connecté. Veuillez vous connecter pour voir les clauses.</p>';
    exit;
}

// Récupérer toutes les clauses de la table et les classer par numéro de clause
$sql = "SELECT * FROM MESURES_SECURITE ORDER BY numero_clause ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$clauses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Groupement des clauses par chapitre
$groupedClauses = [];
foreach ($clauses as $clause) {
    $chapter = explode('.', $clause['numero_clause'])[0]; // Extraire le chapitre (ex: "5" de "5.1")
    $groupedClauses[$chapter][] = $clause;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clauses de Sécurité par Chapitres</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <?php require_once 'header.php'; ?>
</head>
<body>
    <div class="container mt-4">
        <h1>MES DOCUMENTS</h1>
        <div class="accordion" id="chapterAccordion">
            <?php foreach ($groupedClauses as $chapter => $clauses): ?>
            <div class="card">
                <div class="card-header" id="chapterHeading<?= $chapter ?>">
                    <h2 class="mb-0">
                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#chapterCollapse<?= $chapter ?>" aria-expanded="false" aria-controls="chapterCollapse<?= $chapter ?>">
                            Chapitre <?= htmlspecialchars($chapter) ?>
                        </button>
                    </h2>
                </div>
                <div id="chapterCollapse<?= $chapter ?>" class="collapse" aria-labelledby="chapterHeading<?= $chapter ?>" data-parent="#chapterAccordion">
                    <div class="card-body">
                        <div class="accordion" id="clauseAccordion<?= $chapter ?>">
                            <?php foreach ($clauses as $index => $clause): ?>
                            <div class="card">
                                <div class="card-header" id="heading<?= $chapter . '-' . $index ?>">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link <?= $index === 0 ? '' : 'collapsed' ?>" type="button" data-toggle="collapse" data-target="#collapse<?= $chapter . '-' . $index ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="collapse<?= $chapter . '-' . $index ?>">
                                            <?= htmlspecialchars($clause['numero_clause']) ?> - <?= htmlspecialchars($clause['sous_categorie']) ?>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapse<?= $chapter . '-' . $index ?>" class="collapse <?= $index === 0 ? 'show' : '' ?>" aria-labelledby="heading<?= $chapter . '-' . $index ?>" data-parent="#clauseAccordion<?= $chapter ?>">
                                    <div class="card-body">
                                        <h5>Catégorie : <?= htmlspecialchars($clause['categorie']) ?></h5>
                                        <p><?= nl2br(htmlspecialchars($clause['description'])) ?></p>
                                        <button> <a href="document.php?clause_id=<?= urlencode($clause['id']) ?>" class="btn btn-primary">VOIR VOTRE DOCUMENT</a></button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
<footer>
    <?php require_once 'footer.php'; ?>
</footer>
</html>
