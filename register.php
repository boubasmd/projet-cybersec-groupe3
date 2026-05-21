<?php
session_start();
require_once __DIR__ . '/db.php';

if (isset($_SESSION['id_utilisateurs'])) {
    header("Location: index.php");
    exit();
}

$nom              = trim($_POST['nom']             ?? '');
$prenom           = trim($_POST['prenom']          ?? '');
$email            = trim($_POST['email']           ?? '');
$password         = $_POST['password']             ?? '';
$password_confirm = $_POST['password_confirm']     ?? '';
$role             = $_POST['role']                 ?? 'candidat';

$erreurs = [];

if (empty($nom))    $erreurs[] = "Le nom est obligatoire.";
if (empty($prenom)) $erreurs[] = "Le prénom est obligatoire.";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erreurs[] = "L'adresse email est invalide.";
if (strlen($password) < 8) $erreurs[] = "Le mot de passe doit contenir au moins 8 caractères.";
if ($password !== $password_confirm) $erreurs[] = "Les mots de passe ne correspondent pas.";
if (!in_array($role, ['candidat', 'recruteur'])) $erreurs[] = "Rôle invalide.";

if (!empty($erreurs)) {
    $_SESSION['erreurs_inscription'] = $erreurs;
    header("Location: interface_inscription.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT id_utilisateurs FROM Utilisateurs WHERE email = :email");
    $stmt->execute([':email' => $email]);

    if ($stmt->fetch()) {
        $_SESSION['erreurs_inscription'] = ["Cette adresse email est déjà utilisée."];
        header("Location: interface_inscription.php");
        exit();
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO Utilisateurs (nom, prenom, email, mot_de_passe, role)
            VALUES (:nom, :prenom, :email, :mot_de_passe, :role)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom'          => $nom,
        ':prenom'       => $prenom,
        ':email'        => $email,
        ':mot_de_passe' => $hash,
        ':role'         => $role,
    ]);

    $_SESSION['id_utilisateurs'] = $pdo->lastInsertId();
    $_SESSION['nom']             = $nom;
    $_SESSION['prenom']          = $prenom;
    $_SESSION['role']            = $role;

    header("Location: index.php");
    exit();

} catch (PDOException $e) {
    error_log("Erreur inscription : " . $e->getMessage());
    $_SESSION['erreurs_inscription'] = ["Une erreur est survenue. Veuillez réessayer."];
    header("Location: interface_inscription.php");
    exit();
}
