<?php
session_start();
require_once 'auth.php';
require_once 'db.php';
$id_offre = intval($_GET['id'] ?? 0);
$id_user  = $_SESSION['id_utilisateurs'];
if (!$id_offre) { header('Location: index.php'); exit; }
$stmt = $pdo->prepare("SELECT id_offre, id_utilisateurs FROM Offre_emploi WHERE id_offre = :id");
$stmt->execute([':id' => $id_offre]);
$offre = $stmt->fetch();
if (!$offre) { $_SESSION['erreur'] = "Cette offre n'existe pas."; header('Location: index.php'); exit; }
if ($offre['id_utilisateurs'] == $id_user) { $_SESSION['erreur'] = "Vous ne pouvez pas postuler à votre propre offre."; header("Location: offre.php?id=$id_offre"); exit; }
$stmt2 = $pdo->prepare("SELECT id_candidature FROM Candidature WHERE id_offre = :id_offre AND id_utilisateurs = :id_user");
$stmt2->execute([':id_offre' => $id_offre, ':id_user' => $id_user]);
if ($stmt2->fetch()) { $_SESSION['erreur'] = "Vous avez déjà postulé."; header("Location: offre.php?id=$id_offre"); exit; }
$stmt3 = $pdo->prepare("INSERT INTO Candidature (id_offre, id_utilisateurs, date) VALUES (:id_offre, :id_user, CURDATE())");
$stmt3->execute([':id_offre' => $id_offre, ':id_user' => $id_user]);
$_SESSION['succes'] = "Candidature envoyée !";
header("Location: offre.php?id=$id_offre");
exit;
?>