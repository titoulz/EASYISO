<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat avec l'IA</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <?php require_once '../partials/header.php'; ?>
</head>
<body>
<div class="container mt-5">
    <h2>Chat avec l'IA</h2>
    <div id="chat-window" class="border p-3 mb-3" style="height: 300px; overflow-y: scroll;">
        <!-- Les messages s'afficheront ici -->
    </div>
    <form id="chat-form">
        <input type="text" id="message-input" class="form-control" placeholder="Posez votre question..." required>
        <button type="submit" class="btn btn-primary mt-2">Envoyer</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Fonction pour envoyer le message et recevoir la réponse
        $('#chat-form').submit(function(e) {
            e.preventDefault();
            const message = $('#message-input').val();
            $.ajax({
                url: 'api/chat.php',
                method: 'POST',
                data: { message: message },
                success: function(response) {
                    $('#chat-window').append('<p><strong>Vous:</strong> ' + message + '</p>');
                    $('#chat-window').append('<p><strong>IA:</strong> ' + response + '</p>');
                    $('#message-input').val('');
                    $('#chat-window').scrollTop($('#chat-window')[0].scrollHeight);
                }
            });
        });
    });
</script>
</body>

</html>
