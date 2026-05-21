<?php
session_start();
require_once 'db.php';

$ville = trim($_GET['ville'] ?? '');
$type  = $_GET['type'] ?? '';

$sql = "SELECT o.*, u.nom, u.prenom FROM Offre_emploi o
        JOIN Utilisateurs u ON o.id_utilisateurs = u.id_utilisateurs
        WHERE 1=1";
$params = [];

if ($ville !== '') {
    $sql .= " AND o.ville LIKE :ville";
    $params[':ville'] = '%' . $ville . '%';
}

if ($type !== '') {
    $sql .= " AND o.type = :type";
    $params[':type'] = $type;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobFinder</title>
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
            <?php if (($_SESSION['role'] ?? '') === 'recruteur'): ?>
                <a href="creer_offre.php">Créer une offre</a>
                <a href="mes_offres.php">Mes offres</a>
            <?php endif; ?>
            <a href="profil.php">Profil</a>
            <a href="logout.php">Déconnexion</a>
        <?php else: ?>
            <a href="interface_connexion.php">Connexion</a>
            <a href="interface_inscription.php">S'inscrire</a>
        <?php endif; ?>
    </div>
</nav>

<h1>Liste des offres</h1>

<form method="GET" class="filtres">
    <input type="text" name="ville" placeholder="Ville" value="<?= htmlspecialchars($ville) ?>">
    <select name="type">
        <option value="">Tous contrats</option>
        <?php foreach (['CDI','CDD','Stage','Alternance','Interim','Freelance'] as $t): ?>
            <option value="<?= $t ?>" <?= $type === $t ? 'selected' : '' ?>><?= $t ?></option>
        <?php endforeach; ?>
    </select>
    <button>Rechercher</button>
</form>

<div class="cards-grid">
<?php while ($offre = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
    <div class="card">
        <?php if ($offre['image']): ?>
            <img src="<?= htmlspecialchars($offre['image']) ?>" alt="Image offre">
        <?php endif; ?>
        <h2><?= htmlspecialchars($offre['titre']) ?></h2>
        <p>📍 <?= htmlspecialchars($offre['ville']) ?></p>
        <p>💼 <?= htmlspecialchars($offre['type']) ?></p>
        <p>💰 <?= htmlspecialchars($offre['salaire']) ?> €</p>
        <p>🏢 <?= htmlspecialchars($offre['nom']) ?> <?= htmlspecialchars($offre['prenom']) ?></p>
        <a href="offre.php?id=<?= (int)$offre['id_offre'] ?>">Voir l'offre</a>
    </div>
<?php endwhile; ?>
</div>

</body>
</html>
