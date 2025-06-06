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
$panier = $stmt->fetchAll(); // Récupérer tous les articles dans le panier
?>
<?php include 'public/navbar.php'; ?>

<div class="container">
    <h2>Mon Panier</h2>

    <?php if ($panier): ?>
        <div class="panier-list">
            <?php foreach ($panier as $item): ?>
                <div class="panier-item">
                    <img src="img/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['titre']) ?>" class="panier-image">
                    <h3><?= htmlspecialchars($item['titre']) ?></h3>
                    <p><strong>Prix :</strong> <?= number_format($item['prix'], 2, ',', ' ') ?> €</p>
                    <p><strong>Vendeur :</strong> <?= htmlspecialchars($item['vendeur_nom']) ?></p>
                    <a href="discussion.php?article_id=<?= $item['article_id'] ?>&destinataire=<?= $item['vendeur_id'] ?>" class="btn">
                        💬 Envoyer un message au vendeur
                    </a>
                    <form action="supprimer_panier.php" method="post" class="remove-form">
                        <input type="hidden" name="article_id" value="<?= $item['article_id'] ?>">
                        <button type="submit" class="btn btn-remove">Retirer du panier</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Votre panier est vide.</p>
    <?php endif; ?>
</div>
<style>
body {
    font-family: 'Arial', sans-serif;
    background-color: #f0f0f0;
    color: #333;
}

.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #007bff;
}

.panier-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.panier-item {
    background: #ffffff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    display: flex;
    align-items: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
}

.panier-item:hover {
    transform: scale(1.02);
}

.panier-image {
    width: 150px;
    height: auto;
    border-radius: 4px;
    margin-right: 15px;
}

.item-details {
    flex-grow: 1;
}

.btn {
    padding: 10px 15px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    transition: background-color 0.3s;
}

.btn:hover {
    background-color: #0056b3;
}

.btn-remove {
    background-color: #dc3545;
    margin-left: 10px;
}

.btn-remove:hover {
    background-color: #c82333;
}

.item-actions {
    margin-top: 10px;
}
</style>

<?php include 'public/footer.php'; ?>