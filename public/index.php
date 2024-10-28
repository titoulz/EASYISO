<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once '../partials/auth.php';
require_once '../config/database.php';

use App\Controllers\UserController;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
<?php
require_once '../partials/auth.php';

$action = $_GET['action'] ?? '';

if ($action == 'register') {
    include '../views/register.php';
    exit();
} elseif ($action == 'login') {
    include '../views/login.php';
    exit();
} elseif ($action == 'logout') {
    logoutUser();
    header("Location: /public/index.php?action=login");
    exit();
} elseif ($action == 'dashboard') {
    require_once '../views/dashboard.php';
    exit();
}

// Autres logiques de routage...
?>

<!-- Header -->
 <Header>
  
<?php require_once '../partials/header.php'; ?>
</Header>
<!-- Main Content -->
<div class="container mt-5">
    <div class="row align-items-center">
        <!-- Section Image 1 et Texte -->
        <div class="col-md-6">
            <img src="https://via.placeholder.com/500" class="img-fluid" alt="Image 1">
        </div>
        <div class="col-md-6">
            <h2>Titre de la section 1</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum tristique. Duis cursus, mi quis viverra ornare, eros dolor interdum nulla, ut commodo diam libero vitae erat.</p>
        </div>
    </div>
    <hr class="my-5">
    <div class="row align-items-center">
        <!-- Section Image 2 et Texte -->
        <div class="col-md-6 order-md-2">
            <img src="https://via.placeholder.com/500" class="img-fluid" alt="Image 2">
        </div>
        <div class="col-md-6 order-md-1">
            <h2>Titre de la section 2</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum tristique. Duis cursus, mi quis viverra ornare, eros dolor interdum nulla, ut commodo diam libero vitae erat.</p>
        </div>
    </div>
</div>

<!-- Footer -->
<?php require_once '../partials/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
