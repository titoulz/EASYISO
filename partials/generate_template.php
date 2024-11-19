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
//recuperer la clé API dans le .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
// Créer une instance du client OpenAI avec votre clé API
$openai_api_key = $_ENV['API_KEY_EXAMPLE']; // Récupérer la clé API depuis le fichier .env
// Créer une instance du client OpenAI avec votre clé API

$client = \OpenAI::client($openai_api_key);

$prompt = $clause['prompt_associated']; // Utiliser le prompt associé à la clause depuis la base de données
$prompt = str_replace('[nom de l\'organisation]', $entreprise['nom_entreprise'], $prompt);

// Utilisation du point de terminaison 'chat' pour les modèles comme 'gpt-3.5-turbo'
$response = $client->chat()->create([
    'model' => 'gpt-3.5-turbo',
    'messages' => [
        ['role' => 'system', 'content' => 'Vous êtes un assistant qui génère des documents professionnels détaillés et conformes.'],
        ['role' => 'user', 'content' => $prompt],
    ],
]);

if (isset($response['choices'][0]['message']['content'])) {
    $generated_content = $response['choices'][0]['message']['content'];

    // Sauvegarder le contenu généré dans un fichier
    $file_path = __DIR__ . '/../generated_documents/document_' . $clause_id . '.txt';
    file_put_contents($file_path, $generated_content);

    // Affichage du contenu généré et formulaire d'édition
    echo '<!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document Généré</title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>';

    include __DIR__ . '/../partials/header.php';

    echo '<div class="container mt-4">
            <!-- Formulaire permettant de modifier ou de poser une question sur le contenu généré -->
            <form method="POST" action="">
                <div class="form-group">
                    <label for="generated-content">Contenu généré (modifiable) :</label>
                    <textarea class="form-control" id="generated-content" name="generated_content" rows="10">' . htmlspecialchars($generated_content) . '</textarea>
                </div>
                <div class="form-group">
                    <label for="user-feedback">Poser une question supplémentaire ou apporter des précisions :</label>
                    <textarea class="form-control" id="user-feedback" name="user_feedback" rows="4" placeholder="Ajoutez vos précisions ou questions ici..."></textarea>
                </div>
                <button type="submit" name="submit_question" class="btn btn-primary">Soumettre la question</button>
            </form>
            <br>
            <!-- Bouton de téléchargement du fichier généré -->
            <a href="../generated_documents/document_' . $clause_id . '.txt" download="document_' . $clause_id . '.txt" class="btn btn-success">Télécharger le document généré</a>
        </div>';

    // Enregistrer la version initiale
    echo '<div class="container mt-4">
            <form method="POST" action="">
                <input type="hidden" name="save_initial_content" value="1">
                <input type="hidden" id="generated-content-hidden" name="generated_content_hidden" value="' . htmlspecialchars($generated_content) . '">
                <button type="submit" class="btn btn-secondary">Enregistrer la version actuelle</button>
            </form>
        </div>';

    echo '</body></html>';
} else {
    echo json_encode(['error' => 'Aucune réponse générée par l\'API']);
}

// Traitement des modifications ou des questions de l'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_question'])) {
        $user_feedback = trim($_POST['user_feedback']);
        $edited_content = trim($_POST['generated_content']);

        $new_prompt = "Voici un contenu généré précédemment basé sur les exigences d'une clause spécifique :\n";
        $new_prompt .= $edited_content . "\n\n";
        if (!empty($user_feedback)) {
            $new_prompt .= "L'utilisateur a posé la question ou apporté les précisions suivantes :\n";
            $new_prompt .= $user_feedback . "\n\n";
        }
        $new_prompt .= "Veuillez fournir une version mise à jour du document, en tenant compte des modifications et précisions fournies.";

        // Utilisation de l'API OpenAI pour ajuster le contenu
        $response = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'Vous êtes un assistant qui génère des documents professionnels détaillés et conformes.'],
                ['role' => 'user', 'content' => $new_prompt],
            ],
        ]);

        if (isset($response['choices'][0]['message']['content'])) {
            $updated_content = $response['choices'][0]['message']['content'];

            // Sauvegarder la version mise à jour
            file_put_contents($file_path, $updated_content);

            echo '<div class="container mt-4">
                    <h3>Contenu Mis à Jour :</h3>
                    <div id="updated-content">' . nl2br(htmlspecialchars($updated_content)) . '</div>
                    <br>
                    <form method="POST" action="">
                        <input type="hidden" name="save_updated_content" value="1">
                        <textarea class="form-control" id="updated-content" name="updated_content" rows="10">' . htmlspecialchars($updated_content) . '</textarea>
                        <button type="submit" class="btn btn-primary mt-2">Enregistrer la version mise à jour</button>
                    </form>
                  </div>';
        } else {
            echo '<div class="container mt-4"><p class="text-danger">Erreur : Aucune mise à jour générée par l\'API.</p></div>';
        }
    }

    // Enregistrer la version mise à jour si le bouton est cliqué
    if (isset($_POST['save_updated_content'])) {
        $updated_content = trim($_POST['updated_content']);
        $sql = "INSERT INTO generated_documents (clause_id, user_id, content) VALUES (:clause_id, :user_id, :content)
                ON DUPLICATE KEY UPDATE content = :content";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':clause_id', $clause_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':content', $updated_content, PDO::PARAM_STR);
        $stmt->execute();

        echo "<div class='alert alert-success'>Version mise à jour enregistrée avec succès.</div>";
    }

    // Enregistrer la version initiale si le bouton "Enregistrer la version actuelle" est cliqué
    if (isset($_POST['save_initial_content'])) {
        $initial_content = trim($_POST['generated_content_hidden']);
        $sql = "INSERT INTO generated_documents (clause_id, user_id, content) VALUES (:clause_id, :user_id, :content)
                ON DUPLICATE KEY UPDATE content = :content";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':clause_id', $clause_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':content', $initial_content, PDO::PARAM_STR);
        $stmt->execute();

        echo "<div class='alert alert-success'>Version initiale enregistrée avec succès.</div>";
    }
}
?>
