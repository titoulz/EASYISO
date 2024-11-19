<?php
session_start();
//inclure le fichier de configuration
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../partials/header.php';

// Récupérer l'id de la clause
$clause_id = $_GET['clause_id'] ?? null;
if (!$clause_id) {
    echo '<p>Clause non spécifiée. Veuillez sélectionner une clause pour voir le document.</p>';
    exit;
}

// Récupérer l'id de l'utilisateur
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo '<p>Utilisateur non connecté. Veuillez vous connecter pour voir le document.</p>';
    exit;
}


// Récupérer le document le plus récemment modifié lié à la clause sélectionnée et à l'utilisateur
$sql = "SELECT * FROM generated_documents WHERE clause_id = :clause_id AND user_id = :user_id ORDER BY updated_at DESC LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':clause_id', $clause_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$document = $stmt->fetch(PDO::FETCH_ASSOC);



// Si le document n'existe pas
if (!$document) {
    echo '<p>Aucun document généré pour cette clause.</p>';
    echo '<p><a href="/partials/generate_template.php?clause_id=' . htmlspecialchars($clause_id) . '">Générer un document</a></p>';
    exit;
}

// Handle form submission to update the document
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated_content = $_POST['content'] ?? '';
    $sql = "UPDATE generated_documents SET content = :content, updated_at = NOW() WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':content', $updated_content, PDO::PARAM_STR);
    $stmt->bindParam(':id', $document['id'], PDO::PARAM_INT);
    $stmt->execute();
    echo '<p class ="text-success">Document mis à jour avec succès.</p>';
    //derniere

    // Re-fetch the updated document
    $sql = "SELECT * FROM generated_documents WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $document['id'], PDO::PARAM_INT);
    $stmt->execute();
    $document = $stmt->fetch(PDO::FETCH_ASSOC);
}
//recuperer le numero_clause e sous_categorie de la clause et description grace a l'id de la clause
$sql = "SELECT numero_clause, sous_categorie, description FROM MESURES_SECURITE WHERE id = :clause_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':clause_id', $clause_id, PDO::PARAM_INT);
$stmt->execute();
$clause = $stmt->fetch(PDO::FETCH_ASSOC);

// afficher le numero_clause et sous_categorie de la clause et la description
echo '<h1>Document pour la clause ' . htmlspecialchars($clause['numero_clause']) . ' - ' . htmlspecialchars($clause['sous_categorie']) . '</h1>';
// Afficher la description de la clause
echo '<p>' . nl2br(htmlspecialchars($clause['description'])) . '</p>';

// Inclure le script TinyMCE
echo '<script src="https://cdn.tiny.cloud/1/hkllk6mj3297ngp2m1wzdupfj0yw0tb9pgvyyn0ufcxe0wkm/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>';
echo '<script>
        tinymce.init({
          selector: "textarea.form-control",
          resize: true
        });
      </script>';

// Ajouter un style pour rendre le textarea redimensionnable
echo '<style>
        textarea.form-control {
          resize: both; /* Permet de redimensionner horizontalement et verticalement */
        }
      </style>';

// Afficher le document
echo '<form method="POST" action="">
        <textarea class="form-control" name="content" rows="10">' . htmlspecialchars($document['content']) . '</textarea>
        <button type="submit" class="btn btn-primary mt-2">Mettre à jour</button>
      </form>';

require_once __DIR__.'/../partials/footer.php';
?>