<?php
/**
 * logout.php
 * Déconnexion : détruit la session et redirige vers la page de connexion.
 */

session_start();
session_unset();     // vide toutes les variables de session
session_destroy();   // détruit la session côté serveur

// Supprimer le cookie de session côté navigateur
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

header("Location: interface_connexion.php");
exit();
