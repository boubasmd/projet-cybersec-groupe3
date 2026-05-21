<?php
session_start();
require_once 'auth.php';
require_once 'db.php';
$id_utilisateurs = $_SESSION['id_utilisateurs'];
$stmt = $pdo->prepare("SELECT * FROM Offre_emploi WHERE id_utilisateurs = :id_utilisateurs");
$stmt->execute([':id_utilisateurs' => $id_utilisateurs]);
$offres = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes offres — JobFinder</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php require_once 'header.php'; ?>
<main class="container">
    <h1>Mes offres</h1>
    <a href="creer_offre.php">Créer une nouvelle offre</a>
    <?php if (count($offres) === 0): ?>
        <p>Vous n'avez pas encore créé d'offre.</p>
    <?php else: ?>
        <?php foreach ($offres as $offre): ?>
            <div class="card">
                <h2><?= $offre['titre'] ?></h2>
                <p><?= $offre['description'] ?></p>
                <p>Salaire : <?= $offre['salaire'] ?> €</p>
                <p>Ville : <?= $offre['ville'] ?></p>
                <p>Contrat : <?= $offre['type'] ?></p>
                <a href="modifier_offre.php?id=<?= $offre['id_offre'] ?>">Modifier</a>
                <a href="supprimer_offre.php?id=<?= $offre['id_offre'] ?>" onclick="return confirm('Supprimer cette offre ?')">Supprimer</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>
</body>
</html>