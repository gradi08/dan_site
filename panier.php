<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user']['id'];

$sql = "SELECT p.*, a.titre, a.image, a.prix, a.user_id AS vendeur_id, u.nom AS vendeur_nom
        FROM paniers p
        JOIN articles a ON a.id = p.article_id
        JOIN users u ON u.id = a.user_id
        WHERE p.user_id = ? AND p.statut = 'en_cours'";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$panier = $stmt->fetch();
?>
<?php include 'public/navbar.php'; ?>
<h2>Mon panier</h2>

<?php if ($panier): ?>
    <div>
        <img src="<?= $panier['image'] ?>" width="150">
        <h3><?= htmlspecialchars($panier['titre']) ?></h3>
        <p>Prix : <?= $panier['prix'] ?> €</p>
        <p>Vendeur : <?= htmlspecialchars($panier['vendeur_nom']) ?></p>
        <a href="discussion.php?article_id=<?= $panier['article_id'] ?>&destinataire=<?= $panier['vendeur_id'] ?>">
            💬 Envoyer un message au vendeur
        </a>
        <form action="supprimer_panier.php" method="post">
            <input type="hidden" name="article_id" value="<?= $panier['article_id'] ?>">
            <button type="submit">Retirer du panier</button>
        </form>
    </div>
<?php else: ?>
    <p>Votre panier est vide.</p>
<?php endif; ?>
<?php include 'public/footer.php'; ?>