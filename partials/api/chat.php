<?php
require_once '../../config/database.php';

use Dotenv\Dotenv;

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Accès interdit";
    exit;
}

$message = $_POST['message'] ?? '';

if (!empty($message)) {
    $apiKey = $_ENV['API_KEY_EXAMPLE'];
    if (!$apiKey) {
        die("API_KEY_OPENAI non définie.");
    }

    $url = "https://api.openai.com/v1/chat/completions";
    $data = [
        "model" => "gpt-3.5-turbo",
        "messages" => [
            ["role" => "system", "content" => "Tu es un assistant pédagogique en classe de 4ème au collège."],
            ["role" => "user", "content" => $message]
        ],
        "max_tokens" => 100,
    ];

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ]);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FAILONERROR, true); // Gestion des erreurs HTTP

    $apiResponse = curl_exec($curl);
    if ($apiResponse === false) {
        die("Erreur cURL : " . curl_error($curl));
    }
    curl_close($curl);

    $apiResponseData = json_decode($apiResponse, true);

    if (isset($apiResponseData['choices'][0]['message']['content'])) {
        $response = $apiResponseData['choices'][0]['message']['content'];

        // Insérer la question et la réponse dans la table `chat`
        $stmt = $pdo->prepare("INSERT INTO chat (user_id, question, response) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $message, $response]);

       
    } else {
        echo "Erreur dans la réponse de l'API.";
    }
}

// Récupérer les questions et réponses précédentes
$stmt = $pdo->prepare("SELECT question, response FROM chat WHERE user_id = ? ORDER BY created_at ASC");
$stmt->execute([$_SESSION['user_id']]);
$chats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once '../../partials/header.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat IA</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f9fc;
            font-family: Arial, sans-serif;
        }
        .chat-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .chat-message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
        }
        .chat-message.user {
            background-color: #e9f5ff;
            text-align: right;
        }
        .chat-message.ia {
            background-color: #f1f1f1;
        }
        .chat-message strong {
            color: #333;
        }
        #chat-box {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f7f9fc;
        }
        .form-group {
            display: flex;
        }
        #message {
            flex-grow: 1;
            margin-right: 10px;
        }
        .send-button {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="chat-container mt-5">
        <h2 class="text-center mb-4">chatbox</h2>
        
        <div id="chat-box">
            <?php foreach ($chats as $chat): ?>
                <div class="chat-message user">
                    <strong>Vous :</strong> <?php echo htmlspecialchars($chat['question']); ?>
                </div>
                <div class="chat-message ia">
                    <strong>IA :</strong> <?php echo htmlspecialchars($chat['response']); ?>
                </div>
            <?php endforeach; ?>
        </div>

        <form action="" method="post" id="chat-form">
            <div class="form-group">
                <input type="text" class="form-control" id="message" name="message" placeholder="Tapez votre message..." required>
                <button type="submit" class="btn btn-primary send-button">Envoyer</button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        // Soumettre le formulaire avec la touche "Entrée"
        document.getElementById("message").addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                event.preventDefault(); // Empêche le saut de ligne par défaut
                document.getElementById("chat-form").submit(); // Soumet le formulaire
            }
        });
    </script>
</body>
</html>