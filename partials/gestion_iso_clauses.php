<?php
//connexion à la base de données
require_once('../config/database.php');
session_start();

//récupération des données de la table iso_clauses afin de les afficher dans un tableau 
$query = $pdo->query("SELECT * FROM iso_clause");
$iso_clauses = $query->fetchAll();

//recuperer grace a la session l'id de l'utilisateur connecté
$id_user = $_SESSION['user_id'] ?? null;
if (!$id_user) {
    die("Erreur : utilisateur non connecté.");
}

//recuperer dans la table generated_clauses les clauses deja generer pour cet user_id
$query = $pdo->prepare("SELECT * FROM generated_clauses WHERE user_id = ?");
$query->execute([$id_user]);
$generated_clauses = $query->fetchAll(PDO::FETCH_ASSOC);

//recuperer dans la table prompts les prompts liés aux clauses
$query = $pdo->query("SELECT * FROM prompts");
$prompts_db = $query->fetchAll(PDO::FETCH_ASSOC);

// Traitement des modifications des prompts
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prompt_id']) && isset($_POST['prompt_text'])) {
    $prompt_id = (int)$_POST['prompt_id'];
    $prompt_text = trim($_POST['prompt_text']);

    // Mettre à jour le prompt dans la base de données
    $stmt = $pdo->prepare("UPDATE prompts SET prompt_text = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$prompt_text, $prompt_id]);

    echo "<div class='alert alert-success'>Le prompt a été mis à jour avec succès.</div>";
    // Rafraîchir les données des prompts
    $query = $pdo->query("SELECT * FROM prompts");
    $prompts_db = $query->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EASYISO</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="/public/assets/css/navbar.css"> <!-- Inclure le fichier CSS personnalisé -->
    <?php require_once('header.php'); ?>
</head>
<body>
<div class="container">
    <h1 class="text-center">Gestion des clauses ISO</h1>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Numéro Clause</th>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Prompts</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($iso_clauses as $iso_clause): ?>
                        <tr>
                            <td><?= $iso_clause['id'] ?></td>
                            <td><?= $iso_clause['clause_number'] ?></td>
                            <td><?= htmlspecialchars($iso_clause['title']) ?></td>
                            <td><?= htmlspecialchars($iso_clause['description']) ?></td>
                            <td>
                                <?php
                                // Récupérer les prompts correspondant à cette clause
                                $prompts_for_clause = array_filter($prompts_db, function ($prompt) use ($iso_clause) {
                                    return $prompt['clause_id'] === $iso_clause['id'];
                                });

                                // Afficher les prompts dans des formulaires
                                if (!empty($prompts_for_clause)) {
                                    foreach ($prompts_for_clause as $prompt) {
                                        ?>
                                        <form method="POST" action="">
                                            <div class="form-group">
                                                <textarea class="form-control mb-2" name="prompt_text" rows="5"  style="font-size: 16px; height: 200px; width: 400px; resize: both;"><?= htmlspecialchars($prompt['prompt_text']) ?></textarea>
                                                <input type="hidden" name="prompt_id" value="<?= $prompt['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-primary">Modifier le prompt</button>
                                            </div>
                                        </form>
                                        <?php
                                    }
                                } else {
                                    echo "Aucun prompt disponible.";
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                // Vérifier si la clause a déjà été générée pour cet utilisateur
                                $is_generated = false;
                                foreach ($generated_clauses as $generated_clause) {
                                    if ($generated_clause['clause_id'] == $iso_clause['id']) {
                                        $is_generated = true;
                                        break;
                                    }
                                }

                                if ($is_generated) {
                                    // Afficher un bouton pour modifier la clause
                                    echo "<a href='/partials/edit_clause.php?clause_id={$iso_clause['id']}' class='btn btn-primary'>Modifier le doc</a>";
                                } else {
                                    // Afficher un bouton pour générer la clause
                                    echo "<a href='/partials/generate_template_iso.php?id={$iso_clause['id']}' class='btn btn-success'>Générer</a>";
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
