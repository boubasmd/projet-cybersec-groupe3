<?php
session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

$id_user = $_SESSION['id_utilisateurs'];

$stmt = $pdo->prepare("
    SELECT o.id_offre, o.titre, o.ville, o.type, o.salaire, o.image,
           u.nom AS nom_recruteur, u.prenom AS prenom_recruteur,
           c.date AS date
    FROM Candidature c
    JOIN Offre_emploi o ON c.id_offre = o.id_offre
    JOIN Utilisateurs u ON o.id_utilisateurs = u.id_utilisateurs
    WHERE c.id_utilisateur = :id_user
    ORDER BY c.date DESC
");
$stmt->execute([':id_user' => $id_user]);
$candidatures = $stmt->fetchAll();

$succes = $_SESSION['succes'] ?? '';
$erreur = $_SESSION['erreur'] ?? '';
unset($_SESSION['succes'], $_SESSION['erreur']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes candidatures — JobFinder</title>
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
    <h1>Mes candidatures</h1>

    <?php if ($succes): ?>
        <p class="succes"><?= htmlspecialchars($succes) ?></p>
    <?php endif; ?>
    <?php if ($erreur): ?>
        <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <?php if (empty($candidatures)): ?>
        <div class="vide">
            <p>Vous n'avez postulé à aucune offre pour l'instant.</p>
            <a href="index.php" class="btn-primary">Voir les offres disponibles</a>
        </div>
    <?php else: ?>
        <div class="stats-bar">
            <span class="badge"><?= count($candidatures) ?> candidature(s)</span>
        </div>
        <div class="cards-grid">
            <?php foreach ($candidatures as $c): ?>
                <div class="card">
                    <?php if ($c['image']): ?>
                        <img src="<?= htmlspecialchars($c['image']) ?>" alt="Image offre">
                    <?php endif; ?>
                    <h2><?= htmlspecialchars($c['titre']) ?></h2>
                    <p><?= htmlspecialchars($c['ville']) ?></p>
                    <p><?= htmlspecialchars($c['type']) ?></p>
                    <p><?= htmlspecialchars($c['salaire']) ?> €</p>
                    <p><?= htmlspecialchars($c['nom_recruteur']) ?> <?= htmlspecialchars($c['prenom_recruteur']) ?></p>
                    <span class="date-badge">Postulé le <?= htmlspecialchars($c['date']) ?></span>
                    <div class="card-actions">
                        <a href="offre.php?id=<?= (int)$c['id_offre'] ?>" class="btn-secondary">Voir l'offre</a>
                        <a href="abandonner.php?id=<?= (int)$c['id_offre'] ?>"
                           onclick="return confirm('Annuler cette candidature ?')"
                           class="btn-danger">Abandonner</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

</body>
</html>
