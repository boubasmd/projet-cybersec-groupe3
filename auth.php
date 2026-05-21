<?php
/**
 * includes/auth.php
 * Protection des pages : redirige vers login.php si l'utilisateur n'est pas connecté.
 * Usage : require_once __DIR__ . '/includes/auth.php';  en haut de chaque page protégée.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_utilisateurs'])) {
    header("Location: login.php");
    exit();
}
