<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("
    SELECT a.*
    FROM articles a 
    WHERE a.user_id = ? 
    ORDER BY a.date_creation DESC
");
$stmt->execute([$user_id]);
$articles = $stmt->fetchAll();
?>

<?php include_once 'public/navbar.php'; ?>

<div class="container">
    <h2 class="page-title">Mes Articles</h2>

    <?php if (empty($articles)): ?>
        <div class="no-articles">
            <p>Vous n’avez publié aucun article pour le moment.</p>
            <a href="ajouter-produit.php" class="btn btn-primary">➕ Ajouter un Article</a>
        </div>
    <?php else: ?>
        <div class="articles-grid">
            <?php foreach ($articles as $article): ?>
                <div class="article-card">
                    <img src="img/<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['titre']) ?>" class="article-image"> 
                    <div class="article-info">
                        <h3><?= htmlspecialchars($article['titre']) ?></h3>
                        <p class="desc"><?= htmlspecialchars($article['description']) ?></p>
                        <p class="status-label">
                            <strong>Statut :</strong> 
                            <span class="status <?= strtolower($article['statut']) ?>">
                                <?= ucfirst($article['statut']) ?>
                            </span>
                        </p>
                        <div class="article-actions">
                            <a href="modifier_article.php?id=<?= $article['id'] ?>" class="btn btn-edit">✏️ Modifier</a>
                            <a href="supprimer_article.php?id=<?= $article['id'] ?>" class="btn btn-delete" onclick="return confirm('Confirmer la suppression ?')">🗑️ Supprimer</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #f2f4f8;
    color: #333;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.page-title {
    text-align: center;
    font-size: 2rem;
    margin-bottom: 2rem;
    color: #2c3e50;
}

.articles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
}

.article-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}

.article-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.article-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.article-info {
    padding: 1rem 1.2rem;
}

.article-info h3 {
    font-size: 1.3rem;
    margin-bottom: 0.6rem;
    color: #27ae60;
}

.article-info .desc {
    font-size: 0.95rem;
    margin-bottom: 1rem;
    color: #555;
}

.status-label {
    font-size: 0.95rem;
    margin-bottom: 1rem;
}

.status {
    padding: 4px 10px;
    border-radius: 4px;
    font-weight: bold;
    text-transform: capitalize;
}

.status.publie {
    background-color: #e8f5e9;
    color: #2e7d32;
}

.status.prive, .status.privé {
    background-color: #fff3cd;
    color: #856404;
}

.article-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 1rem;
}

.btn {
    padding: 0.6rem 1.2rem;
    border-radius: 6px;
    font-weight: bold;
    text-decoration: none;
    color: white;
    transition: 0.3s;
    font-size: 0.9rem;
}

.btn-primary {
    background-color: #3498db;
}

.btn-edit {
    background-color: #27ae60;
}

.btn-delete {
    background-color: #e74c3c;
}

.btn:hover {
    opacity: 0.9;
}

.no-articles {
    text-align: center;
    padding: 2rem;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.05);
}
</style>

<?php include_once 'public/footer.php'; ?>
