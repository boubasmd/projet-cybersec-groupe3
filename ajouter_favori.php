<?php
session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

$id_offre = intval($_GET['id'] ?? 0);
$id_user  = $_SESSION['id_utilisateurs'];

if (!$id_offre) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT id_offre FROM Offre_emploi WHERE id_offre = :id");
$stmt->execute([':id' => $id_offre]);
if (!$stmt->fetch()) {
    $_SESSION['erreur'] = "Offre introuvable.";
    header('Location: index.php');
    exit;
}

$stmt2 = $pdo->prepare("SELECT id_favoris FROM Favoris WHERE id_offre = :id_offre AND id_utilisateurs = :id_user");
$stmt2->execute([':id_offre' => $id_offre, ':id_user' => $id_user]);
if ($stmt2->fetch()) {
    $_SESSION['erreur'] = "Cette offre est déjà dans vos favoris.";
    header("Location: offre.php?id=$id_offre");
    exit;
}

$stmt3 = $pdo->prepare("INSERT INTO Favoris (id_offre, id_utilisateurs) VALUES (:id_offre, :id_user)");
$stmt3->execute([':id_offre' => $id_offre, ':id_user' => $id_user]);

$_SESSION['succes'] = "Offre ajoutée aux favoris !";
header("Location: offre.php?id=$id_offre");
exit;
