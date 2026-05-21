<?php
session_start();
require_once 'db.php';

// BUG CORRIGÉ : validation de l'ID + protection XSS
$id = intval($_GET['id'] ?? 0);

if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT o.*, u.nom, u.prenom
    FROM Offre_emploi o
    JOIN Utilisateurs u ON o.id_utilisateurs = u.id_utilisateurs
    WHERE o.id_offre = ?
");
$stmt->execute([$id]);
$offre = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$offre) {
    header('Location: index.php');
    exit;
}

$succes = $_SESSION['succes'] ?? '';
$erreur = $_SESSION['erreur'] ?? '';
unset($_SESSION['succes'], $_SESSION['erreur']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($offre['titre']) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav>
    <h2>JobFinder</h2>
    <div class="menu">
        <a href="index.php">Accueil</a>
        <?php if (isset($_SESSION['id_utilisateurs'])): ?>
            <a href="mes_candidatures.php">Mes candidatures</a>
            <a href="mes_favoris.php">Mes favoris</a>
            <a href="profil.php">Profil</a>
            <a href="logout.php">Déconnexion</a>
        <?php else: ?>
            <a href="interface_connexion.php">Connexion</a>
            <a href="interface_inscription.php">S'inscrire</a>
        <?php endif; ?>
    </div>
</nav>

<a href="index.php" class="back">⬅ Retour</a>

<?php if ($succes): ?>
    <p class="succes"><?= htmlspecialchars($succes) ?></p>
<?php endif; ?>
<?php if ($erreur): ?>
    <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
<?php endif; ?>

<div class="card detail">
    <?php if ($offre['image']): ?>
        <img src="<?= htmlspecialchars($offre['image']) ?>" alt="Image offre">
    <?php endif; ?>
    <h1><?= htmlspecialchars($offre['titre']) ?></h1>
    <p><?= nl2br(htmlspecialchars($offre['description'])) ?></p>
    <p>📍 Ville : <?= htmlspecialchars($offre['ville']) ?></p>
    <p>💼 Contrat : <?= htmlspecialchars($offre['type']) ?></p>
    <p>💰 Salaire : <?= htmlspecialchars($offre['salaire']) ?> €</p>
    <p>🏢 Entreprise : <?= htmlspecialchars($offre['nom']) ?> <?= htmlspecialchars($offre['prenom']) ?></p>

    <?php if (isset($_SESSION['id_utilisateurs'])): ?>
        <?php if (($_SESSION['role'] ?? '') === 'candidat'): ?>
            <!-- Seuls les candidats peuvent postuler et mettre en favoris -->
            <div class="card-actions">
                <a href="postuler.php?id=<?= $id ?>" class="btn-primary">Postuler</a>
                <a href="ajouter_favori.php?id=<?= $id ?>" class="btn-secondary">❤️ Ajouter aux favoris</a>
            </div>
        <?php elseif (($_SESSION['role'] ?? '') === 'recruteur'): ?>
            <!-- Les recruteurs voient juste l'offre, sans pouvoir postuler -->
            <p style="color: var(--gris); font-style: italic;">Les recruteurs ne peuvent pas postuler à une offre.</p>
        <?php endif; ?>
    <?php else: ?>
        <p><a href="interface_connexion.php">Connectez-vous</a> pour postuler.</p>
    <?php endif; ?>
</div>

</body>
</html>
