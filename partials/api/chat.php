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

if (empty($message)) {
    echo "Message vide.";
    exit;
}

$apiKey = $_ENV['API_KEY_EXAMPLE'];
if (!$apiKey) {
    die("API_KEY_OPENAI non définie.");
}

$url = "https://api.openai.com/v1/chat/completions";
$data = [
    "model" => "gpt-3.5-turbo",
    "messages" => [
        ["role" => "user", "content" => $message]
    ],
    "max_tokens" => 100,
];

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $apiKey",
    "Content-Type: application/json"
]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($curl);
if ($response === false) {
    die("Erreur cURL : " . curl_error($curl));
}

curl_close($curl);

// Décodage de la réponse JSON pour extraire uniquement le texte de l'IA
$responseData = json_decode($response, true);

if (isset($responseData['choices'][0]['message']['content'])) {
    echo $responseData['choices'][0]['message']['content'];
} else {
    echo "Erreur : la réponse n'a pas pu être générée.";
}
?>
