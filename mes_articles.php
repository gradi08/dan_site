<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user']['id'];

// Récupérer tous les articles de l'utilisateur
$stmt = $pdo->prepare("SELECT a.*, c.nom AS categorie_nom FROM articles a 
                       JOIN categories c ON a.categorie_id = c.id 
                       WHERE a.user_id = ? 
                       ORDER BY a.date_creation DESC");
$stmt->execute([$user_id]);
$articles = $stmt->fetchAll();
?>

<?php include_once 'public/navbar.php';?>


<h2>Mes articles</h2>

<?php if (count($articles) === 0): ?>
    <p>Vous n’avez publié aucun article pour le moment.</p>
<?php else: ?>
    <?php foreach ($articles as $article): ?>
        <div class="article">
            <img src="img/<?= htmlspecialchars($article['image']) ?>" alt="image"> 
            <div>
                <h3><?= htmlspecialchars($article['titre']) ?></h3>
                <p>Prix : <?= number_format($article['prix'], 2, ',', ' ') ?> €</p>
                <p>Catégorie : <?= htmlspecialchars($article['categorie_nom']) ?></p>
                <p>Statut : 
                    <strong style="color: <?= $article['statut'] === 'publie' ? 'green' : 'orange' ?>;">
                        <?= ucfirst($article['statut']) ?>
                    </strong>
                </p>
                <a href="modifier_article.php?id=<?= $article['id'] ?>">✏️ Modifier</a> |
                <a href="supprimer_article.php?id=<?= $article['id'] ?>" onclick="return confirm('Confirmer la suppression ?')">🗑️ Supprimer</a>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>


<?php include_once 'public/footer.php'; ?>            
