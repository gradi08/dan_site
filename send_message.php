<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $expediteur_id = $_SESSION['user']['id'];
    $destinataire_id = intval($_POST['destinataire_id']);
    $contenu = trim($_POST['contenu']);
    $article_id = isset($_POST['article_id']) ? intval($_POST['article_id']) : null;

    if ($contenu === '') {
        echo json_encode(['status' => 'error', 'message' => 'Message vide']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO messages (expediteur_id, destinataire_id, contenu, article_id) 
                           VALUES (?, ?, ?, ?)");
    $stmt->execute([$expediteur_id, $destinataire_id, $contenu, $article_id]);

    echo json_encode(['status' => 'success']);
    exit;
}
?>
