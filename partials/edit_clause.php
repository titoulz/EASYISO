<?php 
// Connexion à la base de données
require_once('../config/database.php');
session_start();

// Récupérer l'ID de l'utilisateur connecté
$id_user = $_SESSION['user_id'] ?? null;
if (!$id_user) {
    die("Erreur : utilisateur non connecté.");
}

// Récupérer l'ID de la clause à modifier
$clause_id = $_GET['clause_id'] ?? null;
if (!$clause_id) {
    die("Erreur : ID de clause non spécifié.");
}

// Récupérer la clause_number et le title pour les afficher au-dessus du formulaire
$query = $pdo->prepare("SELECT * FROM iso_clause WHERE id = ?");
$query->execute([$clause_id]);
$iso_clause = $query->fetch();
if (!$iso_clause) {
    die("Erreur : clause introuvable.");
}

// Initialiser le contenu de la clause
$clause_content = "";

// Si le formulaire est soumis, traiter les modifications
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'update') {
        $clause_content = trim($_POST['clause_content']);

        // Enregistrer la version mise à jour
        $stmt = $pdo->prepare("INSERT INTO generated_clauses (user_id, clause_id, version_number, content, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$id_user, $clause_id, $generated_clause['version_number'] + 1 ?? 1, $clause_content, 'updated']);

        echo "<div class='alert alert-success'>Version mise à jour enregistrée avec succès.</div>";
    }
}

// Récupérer le contenu généré pour cette clause dans la table `generated_clauses` (version la plus récente)
$query = $pdo->prepare("SELECT * FROM generated_clauses WHERE user_id = ? AND clause_id = ? ORDER BY created_at DESC LIMIT 1");
$query->execute([$id_user, $clause_id]);
$generated_clause = $query->fetch();
if ($generated_clause) {
    $clause_content = $generated_clause['content'];
} else {
    die("Erreur : clause générée introuvable.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once('header.php'); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EASYISO</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tiny.cloud/1/hkllk6mj3297ngp2m1wzdupfj0yw0tb9pgvyyn0ufcxe0wkm/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#clause-content',
            height: 400,
            menubar: true,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        });
    </script>
</head>
<body>
<div class="container">
    <h1 class="text-center">Modifier la clause "<?= htmlspecialchars($iso_clause['title']) ?>"</h1>
    <form method="POST" action="">
        <div class="form-group">
            <label for="clause-content">Contenu de la clause :</label>
            <textarea class="form-control" id="clause-content" name="clause_content"><?= htmlspecialchars($clause_content) ?></textarea>
        </div>
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="clause_id" value="<?= $clause_id ?>">
        <button type="submit" class="btn btn-primary mt-2">Enregistrer les modifications</button>
    </form>
</div>
</body>
</html>
