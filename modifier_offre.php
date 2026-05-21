<?php
session_start();
require_once 'auth.php';
require_once 'db.php';
$id_utilisateurs = $_SESSION['id_utilisateurs'];
$id_offre = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM Offre_emploi WHERE id_offre = :id_offre AND id_utilisateurs = :id_utilisateurs");
$stmt->execute([':id_offre' => $id_offre, ':id_utilisateurs' => $id_utilisateurs]);
$offre = $stmt->fetch();
if (!$offre) die("Offre introuvable ou accès refusé.");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE Offre_emploi SET titre=:titre, description=:description, salaire=:salaire, ville=:ville, type=:type WHERE id_offre=:id_offre AND id_utilisateurs=:id_utilisateurs");
    $stmt->execute([
        ':titre'           => $_POST['titre'],
        ':description'     => $_POST['description'],
        ':salaire'         => $_POST['salaire'],
        ':ville'           => $_POST['ville'],
        ':type'            => $_POST['type'],
        ':id_offre'        => $id_offre,
        ':id_utilisateurs' => $id_utilisateurs
    ]);
    header('Location: mes_offres.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une offre — JobFinder</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php require_once 'header.php'; ?>
<main class="container">
    <h1>Modifier l'offre</h1>
    <form method="POST" action="modifier_offre.php?id=<?= $id_offre ?>">
        <label>Titre :</label>
        <input type="text" name="titre" value="<?= $offre['titre'] ?>" required>
        <label>Description :</label>
        <textarea name="description" required><?= $offre['description'] ?></textarea>
        <label>Salaire :</label>
        <input type="number" name="salaire" value="<?= $offre['salaire'] ?>" required>
        <label>Ville :</label>
        <input type="text" name="ville" value="<?= $offre['ville'] ?>" required>
        <label>Type de contrat :</label>
        <select name="type">
            <option value="CDI" <?= $offre['type']==='CDI' ? 'selected' : '' ?>>CDI</option>
            <option value="CDD" <?= $offre['type']==='CDD' ? 'selected' : '' ?>>CDD</option>
            <option value="Interim" <?= $offre['type']==='Interim' ? 'selected' : '' ?>>Intérim</option>
            <option value="Stage" <?= $offre['type']==='Stage' ? 'selected' : '' ?>>Stage</option>
            <option value="Alternance" <?= $offre['type']==='Alternance' ? 'selected' : '' ?>>Alternance</option>
            <option value="Freelance" <?= $offre['type']==='Freelance' ? 'selected' : '' ?>>Freelance</option>
        </select>
        <button type="submit">Enregistrer les modifications</button>
    </form>
</main>
</body>
</html>