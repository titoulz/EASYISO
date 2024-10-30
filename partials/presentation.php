<?php
// Connexion à la base de données
require_once '../config/database.php';

// Récupération des thèmes, chapitres, et sous-chapitres associés
$sql = "SELECT theme.id_theme, theme.nom_theme, chapitre.id_chapitre, chapitre.nom_chapitre, 
        souschapitre.id_souschapitre, souschapitre.nom_souschapitre 
        FROM theme
        LEFT JOIN chapitre ON theme.id_theme = chapitre.id_theme
        LEFT JOIN souschapitre ON chapitre.id_chapitre = souschapitre.id_chapitre
        ORDER BY theme.id_theme, chapitre.id_chapitre, souschapitre.ordre";

$result = $pdo->query($sql);
$themes = [];

// Organiser les résultats par thèmes et chapitres
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $themes[$row['id_theme']]['nom_theme'] = $row['nom_theme'];
    $themes[$row['id_theme']]['chapitres'][$row['id_chapitre']]['nom_chapitre'] = $row['nom_chapitre'];
    if ($row['id_souschapitre']) {
        $themes[$row['id_theme']]['chapitres'][$row['id_chapitre']]['souschapitres'][] = [
            'id_souschapitre' => $row['id_souschapitre'],
            'nom_souschapitre' => $row['nom_souschapitre']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Présentation des Thèmes</title>
    <!-- Inclusion de Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php require_once '../partials/header.php'; ?>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Thèmes et Chapitres</h1>
    <div class="accordion" id="themeAccordion">
        <?php foreach ($themes as $id_theme => $theme): ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTheme<?= $id_theme; ?>">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTheme<?= $id_theme; ?>" aria-expanded="true" aria-controls="collapseTheme<?= $id_theme; ?>">
                        <?= htmlspecialchars($theme['nom_theme']); ?>
                    </button>
                </h2>
                <div id="collapseTheme<?= $id_theme; ?>" class="accordion-collapse collapse" aria-labelledby="headingTheme<?= $id_theme; ?>" data-bs-parent="#themeAccordion">
                    <div class="accordion-body">
                        <?php foreach ($theme['chapitres'] as $id_chapitre => $chapitre): ?>
                            <div class="accordion" id="chapitreAccordion<?= $id_chapitre; ?>">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingChapitre<?= $id_chapitre; ?>">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseChapitre<?= $id_chapitre; ?>" aria-expanded="false" aria-controls="collapseChapitre<?= $id_chapitre; ?>">
                                            <?= htmlspecialchars($chapitre['nom_chapitre']); ?>
                                        </button>
                                    </h2>
                                    <div id="collapseChapitre<?= $id_chapitre; ?>" class="accordion-collapse collapse" aria-labelledby="headingChapitre<?= $id_chapitre; ?>" data-bs-parent="#chapitreAccordion<?= $id_chapitre; ?>">
                                        <div class="accordion-body">
                                            <ul class="list-group">
                                                <?php if (!empty($chapitre['souschapitres'])): ?>
                                                    <?php foreach ($chapitre['souschapitres'] as $souschapitre): ?>
                                                        <li class="list-group-item">
                                                            <a href="cours.php?id_souschapitre=<?= $souschapitre['id_souschapitre']; ?>">
                                                                <?= htmlspecialchars($souschapitre['nom_souschapitre']); ?>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <li class="list-group-item">Aucun sous-chapitre disponible</li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Inclusion de Bootstrap JS et de ses dépendances -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
