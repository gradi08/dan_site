<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user']['id'];
$article_id = intval($_POST['article_id']);

$pdo->prepare("DELETE FROM paniers WHERE user_id = ? AND article_id = ? AND statut = 'en_cours'")
    ->execute([$user_id, $article_id]);

header("Location: panier.php");
exit;
?>
