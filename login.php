<?php
session_start();
require_once __DIR__ . '/db.php';

if (isset($_SESSION['id_utilisateurs'])) {
    header("Location: index.php");
    exit();
}

$email    = trim($_POST['email']    ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    $_SESSION['erreur_connexion'] = "Veuillez remplir tous les champs.";
    header("Location: interface_connexion.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT id_utilisateurs, nom, prenom, mot_de_passe, role
                           FROM Utilisateurs
                           WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $utilisateur = $stmt->fetch();

    if ($utilisateur && password_verify($password, $utilisateur['mot_de_passe'])) {
        session_regenerate_id(true);

        $_SESSION['id_utilisateurs'] = $utilisateur['id_utilisateurs'];
        $_SESSION['nom']             = $utilisateur['nom'];
        $_SESSION['prenom']          = $utilisateur['prenom'];
        $_SESSION['role']            = $utilisateur['role'];

        header("Location: index.php");
        exit();
    } else {
        $_SESSION['erreur_connexion'] = "Email ou mot de passe incorrect.";
        header("Location: interface_connexion.php");
        exit();
    }

} catch (PDOException $e) {
    error_log("Erreur connexion : " . $e->getMessage());
    $_SESSION['erreur_connexion'] = "Une erreur est survenue. Veuillez réessayer.";
    header("Location: interface_connexion.php");
    exit();
}
