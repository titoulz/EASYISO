<?php
require_once '../config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Charger les variables d'environnement depuis .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Fonction pour envoyer un prompt à l'API OpenAI avec retour en HTML compatible Bootstrap
function genererContenu($prompt) {
    $apiKey = $_ENV['API_KEY_EXAMPLE']; // Utilisation directe de $_ENV au lieu de getenv
    if (!$apiKey) {
        die("API_KEY_EXAMPLE non définie.");
    }

    $url = "https://api.openai.com/v1/chat/completions";
    $data = [
        "model" => "gpt-3.5-turbo",
        "messages" => [
            ["role" => "system", "content" => "Tu es un professeur de mathématiques de 4ème qui génère du contenu de cours en HTML compatible avec Bootstrap."],
            ["role" => "user", "content" => $prompt]
        ],
        "max_tokens" => 1000,
        "temperature" => 0.7
    ];

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ]);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FAILONERROR, true);

    $apiResponse = curl_exec($curl);
    if ($apiResponse === false) {
        die("Erreur cURL : " . curl_error($curl));
    }
    curl_close($curl);

    $apiResponseData = json_decode($apiResponse, true);
    return $apiResponseData['choices'][0]['message']['content'] ?? "Erreur dans la réponse de l'API.";
}

$id_souschapitre = $_GET['id_souschapitre'] ?? null;

if (!$id_souschapitre) {
    die("ID du sous-chapitre manquant.");
}

try {
    // Récupérer les informations du sous-chapitre
    $stmt = $pdo->prepare("SELECT nom_souschapitre FROM SousChapitre WHERE id_souschapitre = ?");
    $stmt->execute([$id_souschapitre]);
    $sousChapitre = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sousChapitre) {
        die("Sous-chapitre non trouvé.");
    }

    // Récupérer les informations du cours associé
    $stmt = $pdo->prepare("SELECT * FROM cours WHERE id_souschapitre = ?");
    $stmt->execute([$id_souschapitre]);
    $cours = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cours) {
        die("Cours non trouvé.");
    }

    // Générer du contenu pour chaque section si elle est vide, en demandant du HTML compatible Bootstrap
    if (empty($cours['introduction'])) {
        $promptIntro = "En tant que professeur de mathématiques de 4ème, développe en HTML compatible avec Bootstrap l’introduction d’un cours sur '{$sousChapitre['nom_souschapitre']}'. Utilise des balises <p> et <strong> pour le texte principal.";
        $cours['introduction'] = genererContenu($promptIntro);
    }

    for ($i = 1; $i <= $cours['nb_parties_cours']; $i++) {
        $partieKey = "partie_$i";
        if (empty($cours[$partieKey])) {
            $promptPartie = "Développe en profondeur la partie $i d’un cours de mathématiques de 4ème sur '{$sousChapitre['nom_souschapitre']}', en HTML compatible avec Bootstrap. Utilise les balises <h3> pour les sous-titres et <p> pour le texte.";
            $cours[$partieKey] = genererContenu($promptPartie);
        }
    }

    if (empty($cours['exemples_clés'])) {
        $promptExemples = "En tant que professeur de mathématiques de 4ème, propose des exemples clés pour le cours intitulé '{$sousChapitre['nom_souschapitre']}', en HTML compatible avec Bootstrap. Utilise des balises <ul> et <li> pour les listes.";
        $cours['exemples_clés'] = genererContenu($promptExemples);
    }

    if (empty($cours['conclusion'])) {
        $promptConclusion = "Écris une conclusion pour un cours de mathématiques de 4ème sur '{$sousChapitre['nom_souschapitre']}', en HTML compatible avec Bootstrap. Utilise les balises <p> pour le texte et <strong> pour les points clés.";
        $cours['conclusion'] = genererContenu($promptConclusion);
    }

    if (empty($cours['fiche_revision'])) {
        $promptFicheRevision = "En tant que professeur de mathématiques de 4ème, crée une fiche de révision pour le cours intitulé '{$sousChapitre['nom_souschapitre']}', en HTML compatible avec Bootstrap. Utilise les balises <h3> pour les sections et <ul> pour les points clés.";
        $cours['fiche_revision'] = genererContenu($promptFicheRevision);
    }
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Cours de <?= htmlspecialchars($sousChapitre['nom_souschapitre'] ?? 'Inconnu') ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1><?= htmlspecialchars($cours['titre_cours'] ?? 'Titre du cours non disponible') ?></h1>
        <div class="card mb-3">
            <div class="card-body">
                <h2>Introduction</h2>
                <?= $cours['introduction'] ?? '<p>Introduction non disponible</p>' ?>
            </div>
        </div>
        <?php for ($i = 1; $i <= ($cours['nb_parties_cours'] ?? 0); $i++): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h2>Partie <?= $i ?></h2>
                    <?= $cours["partie_$i"] ?? "<p>Partie $i non disponible</p>" ?>
                </div>
            </div>
        <?php endfor; ?>
        <div class="card mb-3">
            <div class="card-body">
                <h2>Exemples Clés</h2>
                <?= $cours['exemples_clés'] ?? '<p>Exemples clés non disponibles</p>' ?>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <h2>Conclusion</h2>
                <?= $cours['conclusion'] ?? '<p>Conclusion non disponible</p>' ?>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <h2>Fiche de Révision</h2>
                <?= $cours['fiche_revision'] ?? '<p>Fiche de révision non disponible</p>' ?>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <h2>Quiz</h2>
                <a href="quizz.php?id_cours=<?= $cours['id_cours'] ?? 0 ?>" class="btn btn-primary">Commencer le Quiz</a>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
