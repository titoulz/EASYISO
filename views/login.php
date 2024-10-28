<?php include __DIR__ . '/../partials/header.php'; ?>
    <div class="container">
        <h1>Connexion</h1>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="/public/index.php?action=login" method="post">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required><br>

            <label for="mot_de_passe">Mot de passe :</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required><br>

            <button type="submit">Connexion</button>
        </form>
    </div>
