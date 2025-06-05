<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user']['id'];
$article_id = intval($_POST['article_id']);

// Vérifier si un panier existe déjà pour cet utilisateur
$check = $pdo->prepare("SELECT * FROM paniers WHERE user_id = ? AND statut = 'en_cours'");
$check->execute([$user_id]);
$existant = $check->fetch();

// S'il existe, on le supprime (1 seul article autorisé)
if ($existant) {
    $pdo->prepare("DELETE FROM paniers WHERE user_id = ? AND statut = 'en_cours'")->execute([$user_id]);
}

// Ajouter le nouvel article
$insert = $pdo->prepare("INSERT INTO paniers (user_id, article_id) VALUES (?, ?)");
$insert->execute([$user_id, $article_id]);

header("Location: panier.php");
exit;
?>
