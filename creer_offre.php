<?php
session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

// Seul un recruteur peut créer une offre
if (($_SESSION['role'] ?? '') !== 'recruteur') {
    header('Location: index.php');
    exit;
}

$succes = '';
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre       = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $salaire     = intval($_POST['salaire'] ?? 0);
    $ville       = trim($_POST['ville'] ?? '');
    $type        = $_POST['type'] ?? '';

    // Validation simple
    if (empty($titre))       $erreurs[] = "Le titre est obligatoire.";
    if (empty($description)) $erreurs[] = "La description est obligatoire.";
    if (empty($ville))       $erreurs[] = "La ville est obligatoire.";
    if ($salaire <= 0)       $erreurs[] = "Le salaire doit être un nombre positif.";

    if (empty($erreurs)) {
        $stmt = $pdo->prepare("INSERT INTO Offre_emploi (titre, description, salaire, ville, type, id_utilisateurs)
                               VALUES (:titre, :description, :salaire, :ville, :type, :id)");
        $stmt->execute([
            ':titre'       => $titre,
            ':description' => $description,
            ':salaire'     => $salaire,
            ':ville'       => $ville,
            ':type'        => $type,
            ':id'          => $_SESSION['id_utilisateurs'],
        ]);
        header('Location: mes_offres.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une offre — JobFinder</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav>
    <h2>JobFinder</h2>
    <div class="menu">
        <a href="index.php">Accueil</a>
        <a href="creer_offre.php">Créer une offre</a>
        <a href="mes_offres.php">Mes offres</a>
        <a href="profil.php">Profil</a>
        <a href="logout.php">Déconnexion</a>
    </div>
</nav>

<main class="container">
    <h1>📝 Créer une offre d'emploi</h1>

    <?php if (!empty($erreurs)): ?>
        <ul class="erreurs">
            <?php foreach ($erreurs as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div class="form-offre">
        <form method="POST" action="creer_offre.php">

            <label for="titre">Titre du poste</label>
            <input type="text" id="titre" name="titre"
                   value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

            <label for="salaire">Salaire (€ / an)</label>
            <input type="number" id="salaire" name="salaire"
                   value="<?= htmlspecialchars($_POST['salaire'] ?? '') ?>" required>

            <label for="ville">Ville</label>
            <input type="text" id="ville" name="ville"
                   value="<?= htmlspecialchars($_POST['ville'] ?? '') ?>" required>

            <label for="type">Type de contrat</label>
            <select id="type" name="type">
                <?php foreach (['CDI', 'CDD', 'Stage', 'Alternance', 'Interim', 'Freelance'] as $t): ?>
                    <option value="<?= $t ?>" <?= ($_POST['type'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit">📤 Publier l'offre</button>
        </form>
    </div>
</main>

</body>
</html>
