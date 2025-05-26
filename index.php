<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vide-Grenier</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <header>
    <div class="logo">VideGrenier</div>
    <input type="text" placeholder="Rechercher un produit..." />
    <button>Rechercher</button>
    <div class="auth-links">
      <a href="#">Connexion</a> | <a href="#">Inscription</a>
    </div>
  </header>

  <main>
    <aside class="categories">
      <h3>Catégories</h3>
      <ul>
        <li>Vêtements</li>
        <li>Électronique</li>
        <li>Maison</li>
        <li>Livres</li>
        <li>Jouets</li>
        <li>Accessoires</li>
      </ul>
    </aside>

    <section class="carousel">
      <div class="slides">
        <img src="https://via.placeholder.com/800x300?text=Promo+1" class="slide active" />
        <img src="https://via.placeholder.com/800x300?text=Promo+2" class="slide" />
        <img src="https://via.placeholder.com/800x300?text=Promo+3" class="slide" />
      </div>
    </section>

    <section class="products">
      <h2>Produits populaires</h2>
      <div class="product-grid">
        <!-- Produits simulés -->
        <div class="product-card">
          <img src="https://via.placeholder.com/200x150" />
          <h4>Veste en jean</h4>
          <p>15€</p>
        </div>
        <div class="product-card">
          <img src="https://via.placeholder.com/200x150" />
          <h4>Livre ancien</h4>
          <p>5€</p>
        </div>
        <div class="product-card">
          <img src="https://via.placeholder.com/200x150" />
          <h4>Chaise vintage</h4>
          <p>25€</p>
        </div>
        <div class="product-card">
          <img src="https://via.placeholder.com/200x150" />
          <h4>Smartphone</h4>
          <p>80€</p>
        </div>
        <!-- Ajoute plus de produits ici -->
      </div>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 VideGrenier - Tous droits réservés</p>
  </footer>

  <script src="js/script.js"></script>
</body>
</html>
