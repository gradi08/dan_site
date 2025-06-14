<?php 
session_start();
require 'includes/db.php';

// Récupération des 10 derniers articles
$stmt = $pdo->prepare("
    SELECT a.*, u.Tel 
    FROM articles a
    JOIN users u ON a.user_id = u.id
    WHERE a.statut = 'publie'
    ORDER BY a.date_creation DESC
    LIMIT 5
");
$stmt->execute();
$articles = $stmt->fetchAll();

?>

<?php include_once 'public/navbar.php'; ?>

<!-- HERO SECTION -->
<section class="hero">
  <div class="hero-overlay">
    <div class="hero-content">
      <h1>Offrez une seconde vie à vos objets</h1>
      <p>Transformez ce que vous n’utilisez plus en un geste utile et solidaire.</p>
      <a href="ajouter-annonce.php" class="btn btn-lg">Déposer une annonce</a>
    </div>
  </div>
</section>

<!-- À PROPOS -->
<section class="about">
  <div class="container">
    <h2>Pourquoi ce site ?</h2>
    <p>Notre mission est simple : connecter les gens qui souhaitent donner ce qu’ils n’utilisent plus avec ceux qui en ont besoin. Offrir plutôt que jeter, c’est bon pour la planète et bon pour les autres.</p>
    <div class="features">
      <div><span>♻️</span><p>Écologique</p></div>
      <div><span>🤝</span><p>Solidaire</p></div>
      <div><span>🧡</span><p>100% Gratuit</p></div>
    </div>
  </div>
</section>

<!-- COMMENT ÇA MARCHE -->
<section class="how-it-works">
  <div class="container">
    <h2>Comment ça marche ?</h2>
    <div class="steps">
      <div><strong>1</strong><p>Créez un compte</p></div>
      <div><strong>2</strong><p>Publiez une annonce</p></div>
      <div><strong>3</strong><p>Recevez des messages</p></div>
      <div><strong>4</strong><p>Donnez votre objet</p></div>
    </div>
  </div>
</section>

<!-- ARTICLES RÉCENTS -->
<section class="articles">
  <div class="container">
    <h2>Objets récemment offerts</h2>
    <div class="scroll-wrapper">
      <?php foreach ($articles as $article): ?>
        <div class="card">
          <img src="img/<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['titre']) ?>">
          <h3><?= htmlspecialchars($article['titre']) ?></h3>
          <p><?= htmlspecialchars($article['description']) ?></p>
          <?php if (isset($_SESSION['user']['id'])): ?>
            <form action="ajouter_panier.php" method="post">
              <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
              <button class="btn">Contacter</button>
            </form>
          <?php else: ?>
            <p><a href="connexion.php">Connectez-vous pour contacter</a></p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta">
  <div class="container text-center">
    <h2>Un objet chez vous pourrait changer la vie de quelqu’un.</h2>
    <a href="ajouter-annonce.php" class="btn btn-lg">Je donne maintenant</a>
  </div>
</section>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

:root {
  --primary: #4CAF50;
  --light: #f8f9fa;
  --dark: #333;
  --accent: #81C784;
}

body {
  margin: 0;
  font-family: 'Inter', sans-serif;
  background: var(--light);
  color: var(--dark);
}

.container {
  max-width: 1200px;
  margin: auto;
  padding: 2rem;
}

h1, h2 {
  font-weight: 700;
}

a {
  text-decoration: none;
  color: var(--primary);
}

.btn {
  background-color: var(--primary);
  color: white;
  padding: 0.7rem 1.4rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: 0.3s ease;
}

.btn:hover {
  background-color: #388E3C;
}

.btn-lg {
  padding: 1rem 2rem;
  font-size: 1.1rem;
}

/* HERO */
.hero {
  background: url('https://images.unsplash.com/photo-1593508512255-86ab42a8e620?auto=format&fit=crop&w=1470&q=80') center/cover no-repeat;
  height: 90vh;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hero-overlay {
  background: rgba(0,0,0,0.6);
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hero-content {
  color: white;
  text-align: center;
  padding: 2rem;
}

.hero-content h1 {
  font-size: 3rem;
  margin-bottom: 1rem;
}

/* ABOUT */
.about {
  background: white;
  padding: 4rem 2rem;
  text-align: center;
}

.features {
  display: flex;
  justify-content: center;
  gap: 2rem;
  margin-top: 2rem;
}

.features div {
  background: #f1f8e9;
  padding: 1rem;
  border-radius: 8px;
  width: 150px;
}

/* HOW IT WORKS */
.how-it-works {
  background: #e8f5e9;
  padding: 4rem 2rem;
  text-align: center;
}

.steps {
  display: flex;
  justify-content: space-around;
  margin-top: 2rem;
  flex-wrap: wrap;
}

.steps div {
  flex: 1 1 150px;
  margin: 1rem;
}

.steps strong {
  display: block;
  font-size: 2rem;
  color: var(--primary);
}

/* ARTICLES */
.articles {
  padding: 4rem 2rem;
  background: #fff;
}

.scroll-wrapper {
  display: flex;
  overflow-x: auto;
  gap: 1rem;
  padding: 1rem 0;
}

.card {
  flex: 0 0 280px;
  background: #ffffff;
  border-radius: 10px;
  box-shadow: 0 3px 12px rgba(0,0,0,0.08);
  overflow: hidden;
  padding: 1rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: transform 0.3s ease;
}

.card:hover {
  transform: translateY(-4px);
}

.card img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-radius: 6px;
}

/* CTA */
.cta {
  background: var(--accent);
  color: white;
  text-align: center;
  padding: 4rem 2rem;
}

.cta h2 {
  margin-bottom: 1.5rem;
}

</style>
<?php include_once 'public/footer.php'; ?>
