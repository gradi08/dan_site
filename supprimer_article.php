<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user']['id'];

if (!isset($_GET['id'])) {
    die("Article non spécifié.");
}

$article_id = intval($_GET['id']);

// Vérifie que l'article existe et appartient à l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ? AND user_id = ?");
$stmt->execute([$article_id, $user_id]);
$article = $stmt->fetch();

if (!$article) {
    die("Article introuvable ou accès interdit.");
}

// Supprime l'image si elle existe
if (!empty($article['image']) && file_exists("img/" . $article['image'])) {
    unlink("img/" . $article['image']);
}

// Supprimer l'article de la base de données
$stmt = $pdo->prepare("DELETE FROM articles WHERE id = ? AND user_id = ?");
$stmt->execute([$article_id, $user_id]);

// Redirection
header("Location: mes_articles.php?message=Article supprimé avec succès");
exit;
?>
