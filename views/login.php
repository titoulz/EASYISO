<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/UserController.php';

use App\Controllers\UserController;

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['mot_de_passe'];

    $userController = new UserController($pdo);
    if ($userController->login($email, $password)) {
        header("Location: /public/index.php");
        exit();
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/../public/assets/css/style.css"> <!-- Assurez-vous que le chemin est correct -->
</head>
<body>
    <?php require_once '../partials/header.php'; ?>
    <div class="container mt-5">
        <?php if (!empty($error)): ?>
            <p class="text-danger"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="/public/index.php?action=login" method="post">
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe :</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Connexion</button>
        </form>
    </div>
    <?php require_once '../partials/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>