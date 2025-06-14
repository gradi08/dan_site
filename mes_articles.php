<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user']['id'];

// Récupérer tous les articles de l'utilisateur
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
    <h2>Mes Articles</h2>

    <?php if (empty($articles)): ?>
        <div class="no-articles">
            <p>Vous n’avez publié aucun article pour le moment.</p>
            <a href="ajouter-produit.php" class="btn btn-primary">Ajouter un Article</a>
        </div>
    <?php else: ?>
        <div class="articles-grid">
            <?php foreach ($articles as $article): ?>
                <div class="article-card">
                    <img src="img/<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['titre']) ?>" class="article-image"> 
                    <div class="article-info">
                        <h3><?= htmlspecialchars($article['titre']) ?></h3>
                        <h5><?= htmlspecialchars($article['description']) ?></h5>
                        <p><strong>Statut :</strong> 
                            <span class="status <?= $article['statut'] ?>">
                                <?= ucfirst($article['statut']) ?>
                            </span>
                        </p>
                        <div class="article-actions">
                            <a href="modifier_article.php?id=<?= $article['id'] ?>" class="btn btn-edit">Modifier</a>
                            <a href="supprimer_article.php?id=<?= $article['id'] ?>" class="btn btn-delete" onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
                        </div>
                    </div>
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
    margin: 0;
    padding: 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

h2 {
    color: #333;
    margin-bottom: 20px;
    text-align: center;
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
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}

.article-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.article-image {
    width: 100%;
    height: 200px; /* Hauteur fixe pour uniformiser l'affichage */
    object-fit: cover; /* Remplit le conteneur sans déformer l'image */
}

.article-info {
    padding: 15px;
}

.article-actions {
    margin-top: 15px; /* Espace entre les informations et les boutons */
    display: flex;
    justify-content: space-between; /* Espace entre les boutons */
}

.btn {
    display: inline-block;
    padding: 10px 15px;
    border-radius: 4px;
    text-decoration: none;
    color: white;
    transition: background-color 0.3s;
}

.btn-primary {
    background-color: #007bff;
}

.btn-edit {
    background-color: #28a745;
}

.btn-delete {
    background-color: #dc3545;
}

.btn:hover {
    opacity: 0.9;
}

.status.publie {
    color: green;
}

.status.privé {
    color: orange;
}

.no-articles {
    text-align: center;
    margin-top: 20px;
}
</style>

<?php include_once 'public/footer.php'; ?>