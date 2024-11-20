<?php
// Connexion à la base de données
require_once('../config/database.php');
session_start();

// Vérification de l'utilisateur connecté
$id_user = $_SESSION['user_id'] ?? null;
if (!$id_user) {
    die("Erreur : utilisateur non connecté.");
}

// Récupération de l'ID de la clause passé en paramètre
$clause_id = $_GET['id'] ?? null;
if (!$clause_id) {
    die("Erreur : ID de clause non spécifié.");
}

// Récupération des données nécessaires
$query = $pdo->prepare("SELECT * FROM prompts WHERE clause_id = ?");
$query->execute([$clause_id]);
$prompts = $query->fetchAll();

$query = $pdo->prepare("SELECT * FROM iso_clause WHERE id = ?");
$query->execute([$clause_id]);
$iso_clause = $query->fetch();

$query = $pdo->prepare("SELECT * FROM entreprise WHERE user_id = ?");
$query->execute([$id_user]);
$entreprise = $query->fetch();

if (!$iso_clause || !$entreprise || !$prompts) {
    die("Erreur : données introuvables pour l'utilisateur ou la clause.");
}

// Préparation de l'API OpenAI
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
$openai_api_key = $_ENV['API_KEY_EXAMPLE'];
$client = \OpenAI::client($openai_api_key);

$generated_template = '';
$updated_template = '';

// Génération de la version initiale
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $prompt = "Vous êtes un expert en conformité ISO 27001. Votre tâche est de rédiger un document structuré et détaillé répondant aux exigences de la clause ISO ci-dessous. 
    Ce document doit inclure des sections bien définies, des recommandations pratiques et des exemples spécifiques à l'entreprise.

### Informations de l'entreprise ###
- Nom : {$entreprise['nom_entreprise']}
- Adresse : {$entreprise['adresse']}, {$entreprise['code_postal']} {$entreprise['ville']}, {$entreprise['pays']}
- Contexte : {$entreprise['contexte']}
- Objectif : {$entreprise['objectif']}
- Domaine d'application : {$entreprise['domaine']}

### Clause ISO ###
- ID : {$iso_clause['id']}
- Titre : {$iso_clause['title']}
- Description : {$iso_clause['description']}

### Directives spécifiques ###
1. Résumez l'objectif principal de cette clause.
2. Expliquez comment cette clause peut être appliquée dans le contexte spécifique de l'entreprise.
3. Détaillez les étapes pratiques pour se conformer à cette clause.
4. Fournissez des exemples concrets ou scénarios pertinents.
5. Concluez avec les bénéfices pour l'entreprise.

### Éléments additionnels fournis ###
";

    foreach ($prompts as $prompt_data) {
        $prompt .= "- " . $prompt_data['prompt_text'] . "\n";
    }

    $prompt .= "\nLe document doit être structuré avec des sous-titres clairs et un ton professionnel.";

    $response = $client->chat()->create([
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => 'Vous êtes un assistant expert en conformité ISO 27001. Fournissez des réponses longues et détaillées.'],
            ['role' => 'user', 'content' => $prompt],
        ],
    ]);

    if (!isset($response['choices'][0]['message']['content'])) {
        die("Erreur : aucune réponse générée par l'API.");
    }

    $generated_template = $response['choices'][0]['message']['content'];
    $updated_template = $generated_template;
}

// Traitement des modifications et des enregistrements
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $generated_template = trim($_POST['generated_content']);
    $user_feedback = trim($_POST['user_feedback']);
    $action = $_POST['action'] ?? '';

    if ($action === 'save_initial') {
        // Enregistrer la version initiale
        $stmt = $pdo->prepare("INSERT INTO generated_clauses (user_id, clause_id, version_number, content, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$id_user, $clause_id, 1, $generated_template, 'initial']);
        echo "<div class='alert alert-success'>Version initiale enregistrée avec succès.</div>";
    } elseif ($action === 'save_updated' && !empty($user_feedback)) {
        // Générer une version mise à jour avec les précisions
        $new_prompt = "Voici une version initiale du document généré pour une clause ISO spécifique :

### Version précédente ###
" . $generated_template . "

### Commentaires utilisateur ###
L'utilisateur a fourni les commentaires suivants :
" . $user_feedback . "

### Instructions ###
1. Intégrez les commentaires et suggestions fournis pour améliorer le document.
2. Ajoutez des détails supplémentaires en cas de besoin pour clarifier certains points.
3. Conservez une structure claire et professionnelle avec des sous-titres.
4. Ajoutez des exemples ou cas pratiques si cela renforce les recommandations.

Améliorez et reformulez le contenu pour répondre pleinement aux attentes.";

        $response = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'Vous êtes un assistant expert en conformité ISO 27001. Fournissez des réponses longues et détaillées.'],
                ['role' => 'user', 'content' => $new_prompt],
            ],
        ]);

        if (isset($response['choices'][0]['message']['content'])) {
            $updated_template = $response['choices'][0]['message']['content'];

            // Enregistrer la version mise à jour
            $stmt = $pdo->prepare("INSERT INTO generated_clauses (user_id, clause_id, version_number, content, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$id_user, $clause_id, 2, $updated_template, 'updated']);
            echo "<div class='alert alert-success'>Version mise à jour enregistrée avec succès.</div>";
        } else {
            echo "<div class='alert alert-danger'>Erreur : la version mise à jour n'a pas pu être générée par l'IA.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template ISO</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Template pour la clause "<?php echo htmlspecialchars($iso_clause['title']); ?>"</h1>

        <!-- Version initiale -->
        <div class="mb-4">
            <h2>Version initiale</h2>
            <textarea class="form-control" rows="10" readonly><?php echo htmlspecialchars($generated_template); ?></textarea>
            <form method="POST" action="">
                <input type="hidden" name="generated_content" value="<?php echo htmlspecialchars($generated_template); ?>">
                <input type="hidden" name="action" value="save_initial">
                <button type="submit" class="btn btn-primary mt-2">Enregistrer la version initiale</button>
            </form>
        </div>

        <!-- Section pour poser des questions supplémentaires -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="user-feedback">Ajoutez des commentaires ou posez une question pour améliorer le document :</label>
                <textarea class="form-control" id="user-feedback" name="user_feedback" rows="5" placeholder="Ajoutez vos commentaires ou questions ici..."></textarea>
            </div>
            <input type="hidden" name="generated_content" value="<?php echo htmlspecialchars($generated_template); ?>">
            <input type="hidden" name="action" value="save_updated">
            <button type="submit" class="btn btn-secondary">Envoyer les commentaires</button>
        </form>

        <!-- Version mise à jour -->
        <?php if (!empty($updated_template) && $updated_template !== $generated_template): ?>
            <div class="mt-4">
                <h2>Version mise à jour</h2>
                <form method="POST" action="">
                    <textarea class="form-control" id="updated-content" name="generated_content" rows="10"><?php echo htmlspecialchars($updated_template); ?></textarea>
                    <input type="hidden" name="action" value="save_updated">
                    <button type="submit" class="btn btn-success mt-2">Enregistrer les modifications</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
