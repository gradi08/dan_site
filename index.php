<?php session_start();
  // var_dump($_SESSION['user']['email']);
  // exit();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>VideGrenier+ | Donnez une nouvelle vie à vos objets</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/index.css">
</head>
<body>
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
              <a href="dashboard.php">Tableau de bord</a>
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

  <footer>
    © 2025 VideGrenier+. Tous droits réservés.
  </footer>

  <script>
    const hamburger = document.getElementById('hamburger');
    const navLinks = document.getElementById('nav-links');

    hamburger.onclick = () => {
      navLinks.classList.toggle('active');
    };

    document.querySelectorAll('#nav-links a').forEach(link => {
      link.onclick = () => {
        navLinks.classList.remove('active');
      };
    });
  </script>
</body>
</html>
