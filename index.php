<?php 
session_start();
require 'includes/db.php'; // Assurez-vous que ce fichier contient la connexion à la base de données
?>
<?php include_once 'public/navbar.php';    ?>

  <header>
    <div class="logo">VideGrenier+</div>
    <nav>
      <div class="hamburger" id="hamburger">&#9776;</div>
      <div class="nav-links" id="nav-links">
        <a href="#">Accueil</a>
        <a href="categorie.php">Catégories</a>
        <?php if (isset($_SESSION['user'])): ?>
          <div class="profile-menu">
            <a href="#" class="profile-link">Profil ▼</a>
            <div class="dropdown">
              <p><strong><?= htmlspecialchars($_SESSION['user']['nom'] ?? 'Utilisateur') ?></strong></p>
              <p><strong><?= htmlspecialchars($_SESSION['user']['email'] ?? 'Email non défini') ?></strong></p>
              <a href="ajouter-produit.php?">Tableau de bord</a>
              <a href="logout.php">Déconnexion</a>
            </div>
          </div>
        <?php else: ?>
          <a href="connexion.php">Connexion</a>
        <?php endif; ?>
        <a href="#">Vendre</a>
      </div>
    </nav>
  </header>

  <section class="hero">
    <h1>Donnez une nouvelle vie à vos objets inutilisés</h1>
  </section>

  <section class="section categories">
    <a href="categorie.php?nom=vetements" class="category">👕 Vêtements</a>
    <a href="categorie.php?nom=electronique" class="category">📱 Électronique</a>
    <a href="categorie.php?nom=livres" class="category">📚 Livres</a>
    <a href="categorie.php?nom=meubles" class="category">🪑 Meubles</a>
    <a href="categorie.php?nom=jouets" class="category">🧸 Jouets</a>
    <a href="categorie.php?nom=accessoires" class="category">👜 Accessoires</a>
  </section>

  <section class="section products">
    <div class="product-card">
      <img src="https://images.unsplash.com/photo-1512436991641-6745cdb1723f" alt="Produit">
      <div class="product-info">
        <h3>Appareil Photo Vintage</h3>
        <p>45€</p>
      </div>
    </div>
    <div class="product-card">
      <img src="https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f" alt="Produit">
      <div class="product-info">
        <h3>Chaise Rétro</h3>
        <p>25€</p>
      </div>
    </div>
    <div class="product-card">
      <img src="https://images.unsplash.com/photo-1585559605153-6f94aa0c2e58" alt="Produit">
      <div class="product-info">
        <h3>Montre ancienne</h3>
        <p>60€</p>
      </div>
    </div>
  </section>

  <section class="cta">
    <h2>Vous avez des objets à vendre ?</h2>
    <button>Déposer une annonce</button>
  </section>


<?php include_once 'public/footer.php'; ?>