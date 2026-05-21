<?php
session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

$id_user = $_SESSION['id_utilisateurs'];

$stmt = $pdo->prepare("
    SELECT o.id_offre, o.titre, o.ville, o.type, o.salaire, o.image,
           u.nom AS nom_recruteur, u.prenom AS prenom_recruteur
    FROM Favoris f
    JOIN Offre_emploi o ON f.id_offre = o.id_offre
    JOIN Utilisateurs u ON o.id_utilisateurs = u.id_utilisateurs
    WHERE f.id_utilisateur = :id_user
    ORDER BY f.id_favoris DESC
");
$stmt->execute([':id_user' => $id_user]);
$favoris = $stmt->fetchAll();

$succes = $_SESSION['succes'] ?? '';
$erreur = $_SESSION['erreur'] ?? '';
unset($_SESSION['succes'], $_SESSION['erreur']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes favoris — JobFinder</title>
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

<main class="container">
    <h1>Mes favoris</h1>

    <?php if ($succes): ?>
        <p class="succes"><?= htmlspecialchars($succes) ?></p>
    <?php endif; ?>
    <?php if ($erreur): ?>
        <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <?php if (empty($favoris)): ?>
        <div class="vide">
            <p>Vous n'avez aucune offre en favori.</p>
            <a href="index.php" class="btn-primary">Parcourir les offres</a>
        </div>
    <?php else: ?>
        <div class="stats-bar">
            <span class="badge"><?= count($favoris) ?> favori(s)</span>
        </div>
        <div class="cards-grid">
            <?php foreach ($favoris as $f): ?>
                <div class="card">
                    <?php if ($f['image']): ?>
                        <img src="<?= htmlspecialchars($f['image']) ?>" alt="Image offre">
                    <?php endif; ?>
                    <h2><?= htmlspecialchars($f['titre']) ?></h2>
                    <p><?= htmlspecialchars($f['ville']) ?></p>
                    <p><?= htmlspecialchars($f['type']) ?></p>
                    <p><?= htmlspecialchars($f['salaire']) ?> €</p>
                    <p><?= htmlspecialchars($f['nom_recruteur']) ?> <?= htmlspecialchars($f['prenom_recruteur']) ?></p>
                    <div class="card-actions">
                        <a href="offre.php?id=<?= (int)$f['id_offre'] ?>" class="btn-secondary">Voir l'offre</a>
                        <a href="supprimer_favori.php?id=<?= (int)$f['id_offre'] ?>"
                           onclick="return confirm('Retirer des favoris ?')"
                           class="btn-danger">Retirer</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

</body>
</html>
