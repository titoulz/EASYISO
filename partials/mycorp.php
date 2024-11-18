<?php 
// Inclure le fichier de configuration
require_once __DIR__.'/../config/database.php';
session_start();

// Assurez-vous que vous avez une manière d'identifier l'utilisateur, par exemple, via une variable de session
$user_id = $_SESSION['user_id'] ?? null;

// Initialiser les variables
$nom_entreprise = $adresse = $code_postal = $ville = $pays = $telephone = $contexte = $objectif = $domaine = "";

// Si l'utilisateur est identifié, récupérer ses informations
if ($user_id) {
    $sql = "SELECT * FROM ENTREPRISE WHERE user_id = :user_id LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $entreprise = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($entreprise) {
        // Récupérer les valeurs depuis la base de données
        $nom_entreprise = $entreprise['nom_entreprise'];
        $adresse = $entreprise['adresse'];
        $code_postal = $entreprise['code_postal'];
        $ville = $entreprise['ville'];
        $pays = $entreprise['pays'];
        $telephone = $entreprise['telephone'];
        $contexte = $entreprise['contexte'];
        $objectif = $entreprise['objectif'];
        $domaine = $entreprise['domaine'];
    }
}

// Traitement du formulaire pour mise à jour ou insertion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_entreprise = $_POST['nom_entreprise'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $code_postal = $_POST['code_postal'] ?? '';
    $ville = $_POST['ville'] ?? '';
    $pays = $_POST['pays'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $contexte = $_POST['contexte'] ?? '';
    $objectif = $_POST['objectif'] ?? '';
    $domaine = $_POST['domaine'] ?? '';

    if ($user_id && $entreprise) {
        // Mise à jour des informations existantes
        $sql = "UPDATE ENTREPRISE SET 
                    nom_entreprise = :nom_entreprise, 
                    adresse = :adresse, 
                    code_postal = :code_postal, 
                    ville = :ville, 
                    pays = :pays, 
                    telephone = :telephone, 
                    contexte = :contexte, 
                    objectif = :objectif, 
                    domaine = :domaine 
                WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    } else {
        // Insérer de nouvelles informations si elles n'existent pas
        $sql = "INSERT INTO ENTREPRISE (user_id, nom_entreprise, adresse, code_postal, ville, pays, telephone, contexte, objectif, domaine) 
                VALUES (:user_id, :nom_entreprise, :adresse, :code_postal, :ville, :pays, :telephone, :contexte, :objectif, :domaine)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    }

    $stmt->bindParam(':nom_entreprise', $nom_entreprise);
    $stmt->bindParam(':adresse', $adresse);
    $stmt->bindParam(':code_postal', $code_postal);
    $stmt->bindParam(':ville', $ville);
    $stmt->bindParam(':pays', $pays);
    $stmt->bindParam(':telephone', $telephone);
    $stmt->bindParam(':contexte', $contexte);
    $stmt->bindParam(':objectif', $objectif);
    $stmt->bindParam(':domaine', $domaine);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Les informations ont été enregistrées avec succès.</div>";
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de l'enregistrement des informations.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IALEARNING</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="/public/assets/css/navbar.css"> <!-- Inclure le fichier CSS personnalisé -->
    <?php require_once __DIR__.'/header.php'; ?>
</head>
<body>
    <form method="POST" action="">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h1>Mon entreprise</h1>
                    <div class="form-group">
                        <label for="nom_entreprise">Nom de l'entreprise</label>
                        <input type="text" name="nom_entreprise" id="nom_entreprise" class="form-control" value="<?php echo htmlspecialchars($nom_entreprise); ?>">
                    </div>
                    <div class="form-group">
                        <label for="adresse">Adresse</label>
                        <input type="text" name="adresse" id="adresse" class="form-control" value="<?php echo htmlspecialchars($adresse); ?>">
                    </div>
                    <div class="form-group">
                        <label for="code_postal">Code postal</label>
                        <input type="text" name="code_postal" id="code_postal" class="form-control" value="<?php echo htmlspecialchars($code_postal); ?>">
                    </div>
                    <div class="form-group">
                        <label for="ville">Ville</label>
                        <input type="text" name="ville" id="ville" class="form-control" value="<?php echo htmlspecialchars($ville); ?>">
                    </div>
                    <div class="form-group">
                        <label for="pays">Pays</label>
                        <input type="text" name="pays" id="pays" class="form-control" value="<?php echo htmlspecialchars($pays); ?>">
                    </div>
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="text" name="telephone" id="telephone" class="form-control" value="<?php echo htmlspecialchars($telephone); ?>">
                    </div>
                    <div class="form-group">
                        <label for="contexte">Contexte</label>
                        <input type="text" name="contexte" id="contexte" class="form-control" value="<?php echo htmlspecialchars($contexte); ?>">
                    </div>
                    <div class="form-group">
                        <label for="objectif">Objectif</label>
                        <input type="text" name="objectif" id="objectif" class="form-control" value="<?php echo htmlspecialchars($objectif); ?>">
                    </div>
                    <div class="form-group">
                        <label for="domaine">Domaine</label>
                        <input type="text" name="domaine" id="domaine" class="form-control" value="<?php echo htmlspecialchars($domaine); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </div>
        </div>
    </form>
</body>
</html>
<?php require_once __DIR__.'/footer.php'; ?>
