<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    // Redirection vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: ../connexion.php");
    exit();
}
?>
