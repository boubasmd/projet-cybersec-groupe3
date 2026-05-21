<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$connecte = isset($_SESSION['id_utilisateurs']);
?>
<nav>
    <h2><a href="index.php">JobFinder</a></h2>
    <div class="menu">
        <a href="index.php">Accueil</a>
        <?php if ($connecte): ?>
            <a href="mes_offres.php">Mes offres</a>
            <a href="mes_candidatures.php">Mes candidatures</a>
            <a href="mes_favoris.php">Favoris</a>
            <a href="messagerie.php">Messagerie</a>
            <a href="profil.php">Mon profil</a>
            <a href="logout.php">Déconnexion</a>
        <?php else: ?>
            <a href="interface_connexion.php">Connexion</a>
            <a href="interface_inscription.php">S'inscrire</a>
        <?php endif; ?>
    </div>
</nav>
