<?php
require_once 'includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$stmt = $pdo->query("SELECT a.* FROM articles a WHERE a.statut = 'publie'");
$articles = $stmt->fetchAll();
?>
<?php include_once 'public/navbar.php'; ?>

<div class="container">
    <h2>Articles Disponibles</h2>

    <?php if (empty($articles)): ?>
        <p>Aucun article disponible.</p>
    <?php else: ?>
        <div class="articles-grid">
            <?php foreach ($articles as $article): ?>
                <div class="article-card">
                    <img src="img/<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['titre']) ?>" class="article-image">
                    <h3><?= htmlspecialchars($article['titre']) ?></h3>
                    <p><?= htmlspecialchars($article['description']) ?></p>
                    <?php if (isset($_SESSION['user']['id'])): ?>
                        <form method="post" action="ajouter_panier.php">
                            <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                            <button type="submit" class="btn">contacter</button>
                        </form>
                    <?php else: ?>
                        <p><a href="connexion.php">Connecte-toi</a> pour...</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<style>
    body {
    font-family: 'Arial', sans-serif;
    background-color: #f7f7f7;
    color: #333;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

.articles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.article-card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}

.article-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.article-image {
    width: 100%;
    height: auto;
    border-radius: 4px;
}

.btn {
    padding: 10px 15px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn:hover {
    background-color: #0056b3;
}
</style>
<?php include_once 'public/footer.php'; ?>