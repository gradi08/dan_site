<?php
require_once 'includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupération des articles avec téléphone de l'utilisateur
$stmt = $pdo->prepare("
    SELECT a.*, u.Tel
    FROM articles a
    JOIN users u ON a.user_id = u.id
    WHERE a.statut = 'publie'
    ORDER BY a.date_creation DESC
    LIMIT 10
");
$stmt->execute();
$articles = $stmt->fetchAll();
?>

<?php include_once 'public/navbar.php'; ?>

<!-- HERO SECTION -->
<section class="intro-section">
    <div class="container text-center">
        <h1 class="main-title">Donnez une seconde vie à vos objets</h1>
        <p class="main-desc">Débarrassez-vous intelligemment de ce que vous n’utilisez plus. Offrez à d’autres une opportunité de les réutiliser, gratuitement.</p>
    </div>
</section>

<!-- ARTICLES SECTION -->
<div class="container">
    <h2 class="section-title">Objets disponibles</h2>

    <?php if (empty($articles)): ?>
        <p class="empty-message">Aucun article disponible pour le moment.</p>
    <?php else: ?>
        <div class="articles-grid">
            <?php foreach ($articles as $article): ?>
                <div class="article-card">
                    <img src="img/<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['titre']) ?>" class="article-image">
                    <div class="article-content">
                        <h3><?= htmlspecialchars($article['titre']) ?></h3>
                        <p><?= htmlspecialchars($article['description']) ?></p>

                        <?php if (isset($_SESSION['user']['id'])): ?>
                            <a href="https://wa.me/<?= preg_replace('/\D/', '', $article['Tel']) ?>?text=Bonjour,%20je%20suis%20intéressé(e)%20par%20votre%20article%20'<?= urlencode($article['titre']) ?>'" 
                               class="btn btn-whatsapp" 
                               target="_blank" 
                               rel="noopener noreferrer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 5px;">
                                    <path d="M13.601 2.326A7.875 7.875 0 0 0 7.995 0C3.582 0 0 3.581 0 7.994a7.94 7.94 0 0 0 1.113 4.09L0 16l4.136-1.087a7.945 7.945 0 0 0 3.86.99h.003c4.413 0 7.995-3.582 7.995-7.995a7.944 7.944 0 0 0-2.393-5.582zM7.999 14.5a6.48 6.48 0 0 1-3.299-.894l-.235-.14-2.452.645.656-2.391-.153-.245A6.478 6.478 0 0 1 1.5 7.994C1.5 4.41 4.41 1.5 7.995 1.5c1.738 0 3.37.676 4.601 1.899a6.463 6.463 0 0 1 1.899 4.596c0 3.584-2.91 6.505-6.496 6.505z"/>
                                    <path d="M11.053 9.617c-.149-.075-.878-.433-1.015-.483-.135-.05-.233-.075-.33.075-.099.149-.379.483-.465.582-.085.1-.17.111-.318.037-.149-.075-.63-.231-1.2-.737-.444-.395-.742-.882-.83-1.03-.087-.149-.01-.23.066-.304.068-.067.149-.175.224-.262.075-.087.1-.149.149-.248.05-.1.025-.186-.012-.262-.037-.075-.33-.796-.45-1.09-.119-.286-.24-.248-.33-.253l-.28-.005c-.1 0-.262.037-.399.186-.136.149-.519.507-.519 1.235s.53 1.432.604 1.53c.075.099 1.046 1.596 2.537 2.24.355.153.631.244.846.313.355.112.678.096.933.058.285-.043.878-.36 1.002-.707.124-.348.124-.647.087-.707-.037-.062-.136-.099-.285-.174z"/>
                                </svg>
                                Contacter via WhatsApp
                            </a>
                        <?php else: ?>
                            <p><a href="connexion.php" class="login-link">Connecte-toi</a> pour contacter le donneur.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- FOOTER -->
<?php include_once 'public/footer.php'; ?>

<!-- STYLES CSS -->
<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #f4f4f4;
    color: #333;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 1200px;
    margin: auto;
    padding: 2rem;
}

.text-center {
    text-align: center;
}

.intro-section {
    background: linear-gradient(135deg, #56ab2f, #a8e063);
    color: #fff;
    padding: 4rem 2rem;
    text-align: center;
    border-bottom-left-radius: 40px;
    border-bottom-right-radius: 40px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.main-title {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.main-desc {
    font-size: 1.2rem;
    max-width: 700px;
    margin: auto;
}

.section-title {
    font-size: 2rem;
    text-align: center;
    margin: 3rem 0 2rem;
    color: #2c3e50;
}

.articles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.article-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
}

.article-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
}

.article-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.article-content {
    padding: 1.5rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.article-content h3 {
    font-size: 1.3rem;
    color: #2ecc71;
    margin-bottom: 0.5rem;
}

.article-content p {
    flex-grow: 1;
    font-size: 0.95rem;
    margin-bottom: 1rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    background-color: #25D366;
    color: white;
    border: none;
    padding: 10px 16px;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #1ebea5;
}

.login-link {
    color: #2980b9;
    text-decoration: none;
    font-weight: bold;
}

.login-link:hover {
    text-decoration: underline;
}

.empty-message {
    text-align: center;
    color: #888;
    font-style: italic;
    margin-top: 2rem;
}
</style>
