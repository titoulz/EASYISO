<?php
require_once '../config/database.php';

$action = $_GET['action'] ?? '';

if ($action == 'dashboard') {
    require_once '../views/dashboard.php';
    exit();
}

// Autres logiques de routage...
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once '../partials/header.php'; ?>
    <div class="container mt-5">
        <div class="row align-items-center">
            <!-- Section Image 1 et Texte -->
            <div class="col-md-6">
                <img src="https://via.placeholder.com/500" class="img-fluid" alt="Image 1">
            </div>
            <!-- Autres sections -->
        </div>
    </div>
    <?php require_once '../partials/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>