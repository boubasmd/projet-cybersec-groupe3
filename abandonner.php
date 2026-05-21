<?php
session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

$id_offre = intval($_GET['id'] ?? 0);
$id_user  = $_SESSION['id_utilisateurs'];

if (!$id_offre) {
    header('Location: mes_candidatures.php');
    exit;
}

$stmt = $pdo->prepare("SELECT id_candidature FROM Candidature WHERE id_offre = :id_offre AND id_utilisateurs = :id_user");
$stmt->execute([':id_offre' => $id_offre, ':id_user' => $id_user]);

if (!$stmt->fetch()) {
    $_SESSION['erreur'] = "Candidature introuvable.";
    header('Location: mes_candidatures.php');
    exit;
}

$stmt2 = $pdo->prepare("DELETE FROM Candidature WHERE id_offre = :id_offre AND id_utilisateurs = :id_user");
$stmt2->execute([':id_offre' => $id_offre, ':id_user' => $id_user]);

$_SESSION['succes'] = "Candidature annulée.";
header('Location: mes_candidatures.php');
exit;
