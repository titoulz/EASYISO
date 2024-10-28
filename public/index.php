<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil utilisateur</title>
</head>
<body>
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

use App\Controllers\UserController;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    echo "Connexion réussie à la base de données !";
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

$userController = new UserController($pdo);

// Routage basé sur les paramètres de l'URL
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'profile' && isset($_GET['id'])) {
        $userController->showProfile($_GET['id']);
    } elseif ($_GET['action'] == 'register') {
        $userController->register();
    } elseif ($_GET['action'] == 'login') {
        $userController->login();
    } else {
        echo "Page non trouvée";
    }
} else {
    echo "Page non trouvée";
}
?>
</body>
</html>