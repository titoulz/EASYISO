<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/../public/assets/css/style.css"> <!-- Assurez-vous que le chemin est correct -->
<?php require_once '../partials/header.php'; ?>
</head>
<body>
    <div class="container mt-5">
        <h1>Bienvenue sur votre tableau de bord</h1>
        <p>Contenu réservé aux utilisateurs connectés.</p>
        <a href="/public/index.php?action=logout" class="btn btn-danger">Déconnexion</a>
    </div>
    <?php include '../partials/footer.php'; ?>
</body>
</html>