<?php
// Chargement des variables d'environnement
require_once __DIR__ . '/../vendor/autoload.php';

// Configuration des variables d'environnement avec dotenv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Informations de connexion à la base de données
$host = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_NAME'] ?? 'plateforme_accompagnement_scolaire';
$username = $_ENV['DB_USER'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? '';
$port = $_ENV['DB_PORT'] ?? '3306';

try {
    // Création de l'instance PDO pour la connexion à la base de données
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    
    // Configuration des attributs PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Gestion des erreurs
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Mode de récupération par défaut
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Désactivation de l'émulation des requêtes préparées

 
} catch (PDOException $e) {
    // En cas d'erreur de connexion
    echo "Erreur de connexion : " . $e->getMessage();
    exit;
}
?>
