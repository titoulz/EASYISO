<?php
// Inclure le fichier de configuration pour la connexion à la base de données
require_once __DIR__.'/../config/database.php';
session_start();

// Vérification de l'utilisateur connecté
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['error' => 'Utilisateur non connecté']);
    exit;
}

// Récupérer l'ID de la clause
$clause_id = $_GET['clause_id'] ?? null;
if (!$clause_id) {
    echo json_encode(['error' => 'ID de clause non spécifié']);
    exit;
}

// Récupération des informations de la clause
$sql_clause = "SELECT * FROM MESURES_SECURITE WHERE id = :clause_id LIMIT 1";
$stmt_clause = $pdo->prepare($sql_clause);
$stmt_clause->bindParam(':clause_id', $clause_id, PDO::PARAM_INT);
$stmt_clause->execute();
$clause = $stmt_clause->fetch(PDO::FETCH_ASSOC);

if (!$clause) {
    echo json_encode(['error' => 'Clause non trouvée']);
    exit;
}

// Récupération des informations de l'entreprise
$sql = "SELECT * FROM ENTREPRISE WHERE user_id = :user_id LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$entreprise = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$entreprise) {
    echo json_encode(['error' => 'Aucune entreprise trouvée pour cet utilisateur']);
    exit;
}

// Préparer la demande pour OpenAI
require_once __DIR__ . '/../vendor/autoload.php'; // Charger l'autoloader Composer

// Créez une instance du client OpenAI avec votre clé API
$openai_api_key = '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'; // Remplacez par votre clé API réelle
$client = \OpenAI::client($openai_api_key);

$prompt = "Génère un document pour l'entreprise suivante en utilisant les informations de la clause pour démontrer la conformité. Le document doit être détaillé, compatible avec Bootstrap pour le rendu HTML, et structuré de manière à montrer comment l'entreprise répond à la clause spécifiée. Utilisez les classes Bootstrap pour garantir une mise en page réactive et bien structurée :\n";
$prompt .= "Nom de l'entreprise: " . $entreprise['nom_entreprise'] . "\n";
$prompt .= "Adresse: " . $entreprise['adresse'] . ", " . $entreprise['code_postal'] . " " . $entreprise['ville'] . ", " . $entreprise['pays'] . "\n";
$prompt .= "Contexte: " . $entreprise['contexte'] . "\n";
$prompt .= "Objectif: " . $entreprise['objectif'] . "\n";
$prompt .= "Domaine: " . $entreprise['domaine'] . "\n\n";
$prompt .= "Clause : " . $clause['numero_clause'] . " - " . $clause['sous_categorie'] . "\n";
$prompt .= $clause['description'] . "\n\n";
$prompt .= "Veuillez fournir une explication détaillée et un plan de mise en œuvre basé sur les exigences de cette clause.";

// Utilisation du point de terminaison 'chat' pour les modèles comme 'gpt-3.5-turbo'
$response = $client->chat()->create([
    'model' => 'gpt-3.5-turbo', // Utilisez le modèle approprié
    'messages' => [
        ['role' => 'system', 'content' => 'Vous êtes un assistant qui génère des documents professionnels détaillés et conformes.'],
        ['role' => 'user', 'content' => $prompt],
    ],
]);

// Vérifier si la réponse contient des données
if (isset($response['choices'][0]['message']['content'])) {
    $generated_content = $response['choices'][0]['message']['content'];

    // Conversion de la réponse générée en HTML
    echo '<!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document Généré</title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-4">
            <!-- Affichage du contenu généré -->
            ' . $generated_content . '
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>';
} else {
    echo json_encode(['error' => 'Aucune réponse générée par l\'API']);
}
