<?php
require_once 'includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$stmt = $pdo->query("SELECT a.*, c.nom AS categorie_nom FROM articles a JOIN categories c ON a.categorie_id = c.id WHERE a.statut = 'publie'");
$articles = $stmt->fetchAll();
?>
<?php include_once 'public/navbar.php'; ?>
<h2>Articles disponibles</h2>

<?php foreach ($articles as $article): ?>
    <div class="article">
        <img src="img/<?= htmlspecialchars($article['image']) ?>" width="150">
        <h3><?= htmlspecialchars($article['titre']) ?></h3>
        <p><?= htmlspecialchars($article['description']) ?></p>
        <p>Prix : <?= $article['prix'] ?> €</p>
        <p>Catégorie : <?= htmlspecialchars($article['categorie_nom']) ?></p>

        <?php if (isset($_SESSION['user']['id'])): ?>
            <form method="post" action="ajouter_panier.php">
                <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                <button type="submit">Ajouter au panier</button>
            </form>
        <?php else: ?>
            <p><a href="connexion.php">Connecte-toi</a> pour acheter</p>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<?php include_once 'public/footer.php'; ?>
