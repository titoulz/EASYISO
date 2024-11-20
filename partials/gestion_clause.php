<?php
//cette page permet d'avoir une vue d'ensemble de quelle clauses ont été générées et lesquelles ne l'ont pas été
// Inclure le fichier de configuration pour la connexion à la base de données
require_once __DIR__.'/../config/database.php';
session_start();
//faire un tableau de toutes les clauses (numero_clause, sous_categorie, description) de la table MESURES_SECURITE
$sql = "SELECT id, numero_clause, sous_categorie, description FROM MESURES_SECURITE";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$clauses = $stmt->fetchAll(PDO::FETCH_ASSOC);
//afficher le tableau des clauses
//recuperer le user_id de la session
$user_id = $_SESSION['user_id'] ?? null;
//recuperer les clauses qui ont été générées pour l'utilisateur connecté 
$sql = "SELECT clause_id FROM generated_documents WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$generatedClauses = $stmt->fetchAll(PDO::FETCH_COLUMN);
//afficher dans le tableau si la clause a été générée ou non



?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Point de controles</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <?php require_once 'header.php'; ?>
</head>

<body>
    <div class="container mt-4">
        <h1>Gestion des Point de controles</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Numéro de Clause</th>
                    <th>Sous-Catégorie</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clauses as $clause): ?>
                <tr>
                    <td><?= htmlspecialchars($clause['numero_clause']) ?></td>
                    <td><?= htmlspecialchars($clause['sous_categorie']) ?></td>
                    <td><?= htmlspecialchars($clause['description']) ?></td>
                    <td><?php foreach ($generatedClauses as $generatedClause) {
                        if ($generatedClause == $clause['id']) {
                           //dans le cas ou la clause a été générée, on affiche un lien pour voir le document
                            echo '<button> <a href="/partials/document.php?clause_id=' . htmlspecialchars($clause['id']) . '">Voir le document</a> </button>';
                            break;
                        }
                    }?>
                    
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <footer>
       <?php require_once __DIR__.'/../partials/footer.php';?>
        </footer>
    </div>