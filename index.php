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
      <p id="pour">Transformez ce que vous n’utilisez plus en un geste utile et solidaire.</p>
      <?php if (isset($_SESSION['user']['id'])): ?>
          <a href="ajouter-produit.php" class="btn btn-lg">Je donne maintenant</a>
          <?php else: ?>
            <p><a href="connexion.php" class="btn btn-lg">Déposer une annonce</a></p>
          <?php endif; ?>
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
            <a href="https://wa.me/<?= preg_replace('/\D/', '', $article['Tel']) ?>?text=Bonjour,%20je%20suis%20intéressé(e)%20par%20votre%20article%20'<?= urlencode($article['titre']) ?>'" 
                               class="btn btn-whatsapp" 
                               target="_blank" 
                               rel="noopener noreferrer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 5px;">
                                    <path d="M13.601 2.326A7.875 7.875 0 0 0 7.995 0C3.582 0 0 3.581 0 7.994a7.94 7.94 0 0 0 1.113 4.09L0 16l4.136-1.087a7.945 7.945 0 0 0 3.86.99h.003c4.413 0 7.995-3.582 7.995-7.995a7.944 7.944 0 0 0-2.393-5.582zM7.999 14.5a6.48 6.48 0 0 1-3.299-.894l-.235-.14-2.452.645.656-2.391-.153-.245A6.478 6.478 0 0 1 1.5 7.994C1.5 4.41 4.41 1.5 7.995 1.5c1.738 0 3.37.676 4.601 1.899a6.463 6.463 0 0 1 1.899 4.596c0 3.584-2.91 6.505-6.496 6.505z"/>
                                    <path d="M11.053 9.617c-.149-.075-.878-.433-1.015-.483-.135-.05-.233-.075-.33.075-.099.149-.379.483-.465.582-.085.1-.17.111-.318.037-.149-.075-.63-.231-1.2-.737-.444-.395-.742-.882-.83-1.03-.087-.149-.01-.23.066-.304.068-.067.149-.175.224-.262.075-.087.1-.149.149-.248.05-.1.025-.186-.012-.262-.037-.075-.33-.796-.45-1.09-.119-.286-.24-.248-.33-.253l-.28-.005c-.1 0-.262.037-.399.186-.136.149-.519.507-.519 1.235s.53 1.432.604 1.53c.075.099 1.046 1.596 2.537 2.24.355.153.631.244.846.313.355.112.678.096.933.058.285-.043.878-.36 1.002-.707.124-.348.124-.647.087-.707-.037-.062-.136-.099-.285-.174z"/>
                                </svg>
                                Contacter
                            </a>
          <?php else: ?>
            <p><a href="connexion.php">Connectez-vous pour contacter le donneur</a></p>
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
#pour{
  font-size: 1.2rem;
  margin-bottom: 20px;
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
  margin-top: 100px;
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
/* Responsive Design */
@media (max-width: 992px) {
  .hero-content h1 {
    font-size: 2.2rem;
  }

  .btn {
    margin-top: 50px;
    font-size: 1rem;
  }

  .features {
    flex-direction: column;
    align-items: center;
  }

  .features div {
    width: 100%;
    max-width: 300px;
  }

  .steps {
    flex-direction: column;
    align-items: center;
  }

  .steps div {
    margin-bottom: 1.5rem;
  }

  .scroll-wrapper {
    gap: 0.5rem;
  }

  .card {
    flex: 0 0 240px;
    padding: 0.8rem;
  }

  .card img {
    height: 150px;
  }

  .container {
    padding: 1.2rem;
  }

  .cta h2 {
    font-size: 1.4rem;
  }
}

@media (max-width: 576px) {
  .hero {
    height: auto;
    padding: 4rem 1rem;
    text-align: center;
  }

  .hero-content h1 {
    font-size: 1.8rem;
  }

  .btn-lg {
    width: 100%;
    padding: 0.8rem;
    font-size: 1rem;
  }

  .scroll-wrapper {
    flex-wrap: nowrap;
    overflow-x: scroll;
    -webkit-overflow-scrolling: touch;
  }

  .card {
    flex: 0 0 90%;
    margin-right: 1rem;
  }

  .about, .how-it-works, .cta {
    padding: 3rem 1rem;
  }

  .steps strong {
    font-size: 1.8rem;
  }

  #pour {
    font-size: 1rem;
  }
}

</style>
<?php include_once 'public/footer.php'; ?>
