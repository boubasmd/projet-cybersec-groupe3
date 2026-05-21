<?php
session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

$id = $_SESSION['id_utilisateurs'];
$succes = '';
$erreurs = [];

$stmt = $pdo->prepare("SELECT * FROM Utilisateurs WHERE id_utilisateurs = :id");
$stmt->execute([':id' => $id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom         = trim($_POST['nom'] ?? '');
    $prenom      = trim($_POST['prenom'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $competences = trim($_POST['competences'] ?? '');
    $nouveau_mdp = $_POST['nouveau_mdp'] ?? '';
    $confirm_mdp = $_POST['confirm_mdp'] ?? '';

    if (empty($nom))    $erreurs[] = "Le nom est obligatoire.";
    if (empty($prenom)) $erreurs[] = "Le prénom est obligatoire.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erreurs[] = "Email invalide.";

    $stmt2 = $pdo->prepare("SELECT id_utilisateurs FROM Utilisateurs WHERE email = :email AND id_utilisateurs != :id");
    $stmt2->execute([':email' => $email, ':id' => $id]);
    if ($stmt2->fetch()) $erreurs[] = "Cet email est déjà utilisé par un autre compte.";

    if (!empty($nouveau_mdp)) {
        if (strlen($nouveau_mdp) < 8) $erreurs[] = "Le mot de passe doit faire au moins 8 caractères.";
        if ($nouveau_mdp !== $confirm_mdp) $erreurs[] = "Les mots de passe ne correspondent pas.";
    }

    if (empty($erreurs)) {
        if (!empty($nouveau_mdp)) {
            $hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
            $stmt3 = $pdo->prepare("UPDATE Utilisateurs SET nom=:nom, prenom=:prenom, email=:email,
                competences=:competences, mot_de_passe=:mdp WHERE id_utilisateurs=:id");
            $stmt3->execute([':nom' => $nom, ':prenom' => $prenom, ':email' => $email,
                ':competences' => $competences, ':mdp' => $hash, ':id' => $id]);
        } else {
            $stmt3 = $pdo->prepare("UPDATE Utilisateurs SET nom=:nom, prenom=:prenom, email=:email,
                competences=:competences WHERE id_utilisateurs=:id");
            $stmt3->execute([':nom' => $nom, ':prenom' => $prenom, ':email' => $email,
                ':competences' => $competences, ':id' => $id]);
        }
        $_SESSION['nom']    = $nom;
        $_SESSION['prenom'] = $prenom;
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        $succes = "Profil mis à jour avec succès !";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil — JobFinder</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav>
    <h2>JobFinder</h2>
    <div class="menu">
        <a href="index.php">Accueil</a>
        <a href="mes_candidatures.php">Mes candidatures</a>
        <a href="mes_favoris.php">Mes favoris</a>
        <?php if (($_SESSION['role'] ?? '') === 'recruteur'): ?>
            <a href="creer_offre.php">Créer une offre</a>
            <a href="mes_offres.php">Mes offres</a>
        <?php endif; ?>
        <a href="profil.php">Profil</a>
        <a href="logout.php">Déconnexion</a>
    </div>
</nav>

<div class="profil-wrapper">
    <div class="profil-card">
        <h1>👤 Mon profil</h1>

        <?php if ($succes): ?>
            <p class="succes"><?= htmlspecialchars($succes) ?></p>
        <?php endif; ?>

        <?php if (!empty($erreurs)): ?>
            <ul class="erreurs">
                <?php foreach ($erreurs as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" action="profil.php">

            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>

            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label for="competences">Compétences / CV</label>
            <textarea id="competences" name="competences"><?= htmlspecialchars($user['competences'] ?? '') ?></textarea>

            <hr>
            <p><strong>Changer le mot de passe</strong> <small>(laisser vide pour ne pas changer)</small></p>

            <label for="nouveau_mdp">Nouveau mot de passe</label>
            <input type="password" id="nouveau_mdp" name="nouveau_mdp" minlength="8">

            <label for="confirm_mdp">Confirmer le mot de passe</label>
            <input type="password" id="confirm_mdp" name="confirm_mdp" minlength="8">

            <button type="submit">💾 Enregistrer les modifications</button>
        </form>
    </div>
</div>

</body>
</html>
