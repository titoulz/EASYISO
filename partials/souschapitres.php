<?php
require_once '../config/database.php';
use Dotenv\Dotenv;

$id_souschapitre = $_GET['id_souschapitre'] ?? null;

if (!$id_souschapitre) {
    die("ID du sous-chapitre manquant.");
}

try {
    // Récupérer les informations du sous-chapitre
    $stmt = $pdo->prepare("SELECT nom_souschapitre, description FROM SousChapitre WHERE id_souschapitre = ?");
    $stmt->execute([$id_souschapitre]);
    $sousChapitre = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sousChapitre) {
        die("Sous-chapitre non trouvé.");
    }
//recuperer le nom du chapitre associé
    $stmt = $pdo->prepare("SELECT nom_chapitre FROM Chapitre LEFT JOIN SousChapitre ON Chapitre.id_chapitre = SousChapitre.id_chapitre WHERE id_souschapitre = ?");
    $stmt->execute([$id_souschapitre]);
    $chapitre = $stmt->fetch(PDO::FETCH_ASSOC);
    //recuperer l'a

    // Récupérer le contenu du sous-chapitre
    $stmt = $pdo->prepare("SELECT type_contenu, contenu FROM ContenuChapitre WHERE id_chapitre = ?");
    $stmt->execute([$id_souschapitre]);
    $contenus = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Charger la clé API et vérifier sa validité
    $apiKey = $_ENV['API_KEY_EXAMPLE'];
    if (!$apiKey) {
        die("Erreur : Clé API manquante.");
    }

    // Appel à l'API OpenAI pour générer une fiche de révision
    $url = "https://api.openai.com/v1/chat/completions";

    $sujet = $sousChapitre['nom_souschapitre'];
    $abc=$chapitre['nom_chapitre'];
    $data = [
        "model" => "gpt-3.5-turbo",
        "messages" => [
            ["role" => "system", "content" => "Tu es un assistant pédagogique en classe de 4ème au college ."],
            ["role" => "user", "content" => "Crée une fiche de révision sur le sujet : '$sujet' dans le chapitre '$abc' en utilisant les classes suivantes : <h2 class='fiche-titre'>, <p class='fiche-paragraphe'>, et <ul class='fiche-liste'>. et en etant le plus précis dans les informations"
]
        ],
    ];

    // Configuration de la requête cURL
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ]);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FAILONERROR, true); // Gestion des erreurs HTTP

    // Exécuter la requête et vérifier la réponse
    $apiResponse = curl_exec($curl);
    if ($apiResponse === false) {
        die("Erreur cURL : " . curl_error($curl));
    }
    curl_close($curl);

    // Décoder la réponse JSON de l'API
    $apiResponseData = json_decode($apiResponse, true);

    if (isset($apiResponseData['choices'][0]['message']['content'])) {
        $ficheRevision = $apiResponseData['choices'][0]['message']['content'];
    } else {
        // Affichez la réponse brute de l'API pour déboguer
        echo "Réponse brute de l'API : " . htmlspecialchars($apiResponse);
        die("Erreur : la fiche de révision n'a pas pu être générée.");
    }
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($sousChapitre['nom_souschapitre']); ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/assets/css/navbar.css">
</head>
<body>
    <?php require_once '../partials/header.php'; ?>
    <div class="container mt-5">
        <h1><?php echo htmlspecialchars($sousChapitre['nom_souschapitre']); ?></h1>
        <p><?php echo htmlspecialchars($sousChapitre['description']); ?></p>
        <div>
            <h2>Contenu</h2>
            <?php foreach ($contenus as $contenu): ?>
                <h3><?php echo htmlspecialchars($contenu['type_contenu']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($contenu['contenu'])); ?></p>
            <?php endforeach; ?>
        </div>

        <!-- Affichage de la fiche de révision générée par l'API OpenAI -->
        <div class="fiche-revision">
    <h2>Fiche de Révision Générée pour le chapitre "<?php echo $chapitre['nom_chapitre']; ?>"</h2>
    
    <p><?php echo nl2br($ficheRevision); ?></p>
</div>
    </div>
    <?php require_once '../partials/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<style>
    .fiche-revision {
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
        font-family: Arial, sans-serif;
    }
    .fiche-revision h2 {
        color: #333;
        font-size: 1.5em;
    }
    .fiche-revision p, .fiche-revision li {
        font-size: 1em;
        line-height: 1.6;
        color: #555;
    }
    .fiche-revision ul {
        list-style-type: disc;
        padding-left: 20px;
    }
    .fiche-titre {
    color: #003366;
    font-weight: bold;
}
.fiche-paragraphe {
    color: #333;
    font-size: 16px;
}
.fiche-liste {
    padding-left: 20px;
    list-style-type: square;
}

</style>
