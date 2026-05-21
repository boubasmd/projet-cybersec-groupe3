<?php
/**
 * interface_inscription.php
 * Formulaire d'inscription.
 */

session_start();

if (isset($_SESSION['id_utilisateurs'])) {
    header("Location: index.php");
    exit();
}

$erreurs = $_SESSION['erreurs_inscription'] ?? [];
unset($_SESSION['erreurs_inscription']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — EmploiBD</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="auth-container">
        <h1>Créer un compte</h1>

        <?php if (!empty($erreurs)): ?>
            <ul class="erreurs">
                <?php foreach ($erreurs as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form action="register.php" method="post">

            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" required>

            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" required>

            <label for="email">Adresse email</label>
            <input type="email" id="email" name="email" required>

            <label for="role">Je suis</label>
            <select id="role" name="role">
                <option value="candidat">Candidat</option>
                <option value="recruteur">Recruteur</option>
            </select>

            <label for="password">Mot de passe <small>(8 caractères minimum)</small></label>
            <input type="password" id="password" name="password" required minlength="8">

            <label for="password_confirm">Confirmer le mot de passe</label>
            <input type="password" id="password_confirm" name="password_confirm" required minlength="8">

            <button type="submit">S'inscrire</button>
        </form>

        <p>Déjà un compte ? <a href="interface_connexion.php">Se connecter</a></p>
    </main>
</body>
</html>
