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
$stmt = $pdo->prepare("DELETE FROM Offre_emploi WHERE id_offre = :id_offre AND id_utilisateurs = :id_utilisateurs");
$stmt->execute([':id_offre' => $id_offre, ':id_utilisateurs' => $id_utilisateurs]);
header('Location: mes_offres.php');
exit;
?>