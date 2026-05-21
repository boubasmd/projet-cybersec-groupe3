<?php
/**
 * interface_connexion.php
 * Formulaire de connexion.
 */

session_start();

// Si déjà connecté, rediriger
if (isset($_SESSION['id_utilisateurs'])) {
    header("Location: index.php");
    exit();
}

// Récupérer et vider l'erreur stockée en session
$erreur = $_SESSION['erreur_connexion'] ?? '';
unset($_SESSION['erreur_connexion']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — EmploiBD</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="auth-container">
        <h1>Connexion</h1>

        <?php if ($erreur): ?>
            <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <form action="login.php" method="post">

            <label for="email">Adresse email</label>
            <input type="email" id="email" name="email" required autofocus>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Se connecter</button>
        </form>

        <p>Pas encore de compte ? <a href="interface_inscription.php">S'inscrire</a></p>
    </main>
</body>
</html>
