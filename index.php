<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vide-Grenier Chic</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }
    body {
      background-color: #f9f9f9;
      color: #333;
    }
    header {
      background: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 2rem;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    .logo {
      font-weight: 700;
      font-size: 1.5rem;
      color: #2b6777;
    }
    nav a {
      margin: 0 1rem;
      text-decoration: none;
      color: #333;
      font-weight: 500;
    }
    nav a:hover {
      color: #2b6777;
    }
    .hero {
      background: url('https://images.unsplash.com/photo-1583337130417-3346a1be7dee') no-repeat center/cover;
      height: 60vh;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: white;
    }
    .hero h1 {
      font-size: 3rem;
      background: rgba(0,0,0,0.5);
      padding: 1rem 2rem;
      border-radius: 8px;
    }
    .categories {
      padding: 2rem;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 1rem;
      background: #fff;
    }
    .category {
      background: #f0f0f0;
      padding: 1rem;
      text-align: center;
      border-radius: 10px;
      transition: transform 0.3s, box-shadow 0.3s;
      cursor: pointer;
    }
    .category:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .products {
      padding: 2rem;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
    }
    .product-card {
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      transition: transform 0.3s;
    }
    .product-card:hover {
      transform: scale(1.03);
    }
    .product-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }
    .product-info {
      padding: 1rem;
    }
    .product-info h3 {
      font-size: 1rem;
      margin-bottom: 0.5rem;
    }
    .product-info p {
      color: #2b6777;
      font-weight: bold;
    }
    .cta {
      background: #2b6777;
      color: white;
      padding: 2rem;
      text-align: center;
    }
    .cta h2 {
      margin-bottom: 1rem;
    }
    .cta button {
      background: white;
      color: #2b6777;
      padding: 0.7rem 1.5rem;
      border: none;
      border-radius: 20px;
      cursor: pointer;
      transition: background 0.3s;
    }
    .cta button:hover {
      background: #f1f1f1;
    }
    footer {
      background: #eee;
      text-align: center;
      padding: 1rem;
      font-size: 0.9rem;
      color: #666;
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">VideGrenier+</div>
    <nav>
      <a href="#">Accueil</a>
      <a href="#">Catégories</a>
      <a href="#">Connexion</a>
      <a href="#">Vendre</a>
    </nav>
  </header>

  <section class="hero">
    <h1>Donnez une nouvelle vie à vos objets</h1>
  </section>

  <section class="categories">
    <div class="category">Vêtements</div>
    <div class="category">Électronique</div>
    <div class="category">Livres</div>
    <div class="category">Meubles</div>
    <div class="category">Jouets</div>
    <div class="category">Accessoires</div>
  </section>

  <section class="products">
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
    <!-- Ajoute d'autres produits ici -->
  </section>

  <section class="cta">
    <h2>Vous avez des objets à vendre ?</h2>
    <button>Déposer une annonce</button>
  </section>

  <footer>
    © 2025 VideGrenier+. Tous droits réservés.
  </footer>
</body>
</html>
