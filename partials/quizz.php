<?php
require_once '../config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function genererContenu($prompt) {
    $apiKey = $_ENV['API_KEY_EXAMPLE'];
    if (!$apiKey) {
        die("API_KEY_EXAMPLE non définie.");
    }

    $url = "https://api.openai.com/v1/chat/completions";
    $data = [
        "model" => "gpt-3.5-turbo",
        "messages" => [
            ["role" => "system", "content" => "Tu es un professeur de mathématiques de 4ème qui génère des QCM pour des élèves."],
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

function parseQCM($text) {
    $questions = [];
    $lines = explode("\n", $text);
    $question = null;
    $options = [];
    $correctAnswer = null;

    foreach ($lines as $line) {
        $line = trim($line);

        // Détecte une question
        if (preg_match('/^\d+\.\s(.+?)$/', $line, $matches)) {
            if ($question) {
                $questions[] = [
                    'question' => $question,
                    'options' => $options,
                    'correctAnswer' => $correctAnswer
                ];
            }
            $question = $matches[1];
            $options = [];
            $correctAnswer = null;
        } elseif (preg_match('/^[a-dA-D]\)\s(.+)$/', $line, $matches)) {
            $options[] = $line;
        } elseif (preg_match('/^\*\*Bonne réponse : (.+)$/', $line, $matches)) {
            $correctAnswer = $matches[1];
        }
    }

    if ($question) {
        $questions[] = [
            'question' => $question,
            'options' => $options,
            'correctAnswer' => $correctAnswer
        ];
    }

    return $questions;
}

$questions = [];
$correction = null;
$chapter = $_GET['chapter'] ?? "Chapitre de mathématiques";


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answers'])) {
    $answers = $_POST['answers'];
    $formattedAnswers = implode(", ", array_map(function($question, $answer) {
        return "Question: $question - Réponse choisie: $answer";
    }, array_keys($answers), $answers));

    $correctionPrompt = "Voici les réponses d'un élève pour le QCM sur le chapitre '$chapter' . Corrige ces réponses et donne des commentaires pour chaque question:\n$formattedAnswers et retourne un score en pourcentage.";
    $correction = genererContenu($correctionPrompt);
} else {
    $quizPrompt = "Génère un QCM de mathématiques de niveau 4ème pour le chapitre '$chapter'. Retourne chaque question avec quatre options, en indiquant la bonne réponse pour l'enseignant seulement.";
    $quizContent = genererContenu($quizPrompt);

    // Parse the generated content into structured questions
    $questions = parseQCM($quizContent);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Quiz - <?= htmlspecialchars($chapter) ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <?php require_once __DIR__ . '/../partials/header.php'; ?>
</head>
<body>
    <div class="container mt-5">
        <h1>Quiz - <?= htmlspecialchars($chapter) ?></h1>
        
        <?php if ($correction): ?>
            <div class="alert alert-info">
                <h2>Correction du QCM</h2>
                <p><?= nl2br(htmlspecialchars($correction)) ?></p>
            </div>
        <?php elseif ($questions): ?>
            <form method="POST">
                <?php foreach ($questions as $index => $questionData): ?>
                    <div class="mb-3">
                        <h4><?= htmlspecialchars($questionData['question']) ?></h4>
                        <?php foreach ($questionData['options'] as $option): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[<?= $index ?>]" value="<?= htmlspecialchars($option) ?>" required>
                                <label class="form-check-label"><?= htmlspecialchars($option) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
                <button type="submit" class="btn btn-primary">Envoyer mes réponses</button>
            </form>
        <?php else: ?>
            <p>Le QCM n'a pas pu être généré. Veuillez réessayer.</p>
        <?php endif; ?>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
