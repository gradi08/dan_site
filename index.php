<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>VideGrenier+ | Donnez une nouvelle vie à vos objets</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: #f4f7f9;
      color: #333;
      line-height: 1.6;
      scroll-behavior: smooth;
    }

    header {
      background: white;
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: background 0.3s ease;
    }

    .logo {
      font-size: 1.8rem;
      font-weight: bold;
      color: #2b6777;
      transition: transform 0.3s;
    }

    .logo:hover {
      transform: scale(1.05);
    }

    nav {
      display: flex;
      align-items: center;
    }

    .nav-links {
      display: flex;
      gap: 1.5rem;
    }

    nav a {
      text-decoration: none;
      color: #333;
      font-weight: 500;
      position: relative;
      transition: color 0.3s;
    }

    nav a::after {
      content: '';
      position: absolute;
      left: 0;
      bottom: -4px;
      width: 0%;
      height: 2px;
      background: #2b6777;
      transition: width 0.3s;
    }

    nav a:hover {
      color: #2b6777;
    }

    nav a:hover::after {
      width: 100%;
    }

    .hamburger {
      display: none;
      font-size: 1.8rem;
      cursor: pointer;
    }

    @media (max-width: 768px) {
      .nav-links {
        display: none;
        flex-direction: column;
        background: white;
        position: absolute;
        top: 70px;
        right: 0;
        width: 100%;
        padding: 1rem 2rem;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
      }

      .nav-links.active {
        display: flex;
      }

      .hamburger {
        display: block;
        margin-left: 1rem;
      }
    }

    .hero {
      background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)),
        url('https://images.unsplash.com/photo-1583337130417-3346a1be7dee') no-repeat center/cover;
      height: 80vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      text-align: center;
      animation: fadeIn 1s ease-in-out;
    }

    .hero h1 {
      font-size: 3rem;
      max-width: 90%;
      padding: 1.5rem 2rem;
      background: rgba(0, 0, 0, 0.4);
      border-radius: 12px;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .section {
      padding: 3rem 2rem;
    }
    .section a {
      text-decoration:none;
    }
    .categories, .products {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
      gap: 1.5rem;
    }

    .category, .product-card {
      background: white;
      border-radius: 12px;
      padding: 1rem;
      text-align: center;
      transition: all 0.3s ease;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .category:hover, .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .product-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 10px;
    }

    .product-info h3 {
      margin: 0.8rem 0 0.3rem;
      font-size: 1.1rem;
    }

    .product-info p {
      color: #2b6777;
      font-weight: bold;
    }

    .cta {
      background: #2b6777;
      color: white;
      text-align: center;
      padding: 3rem 2rem;
      border-radius: 20px;
      margin: 3rem 2rem;
    }

    .cta button {
      background: white;
      color: #2b6777;
      border: none;
      padding: 0.8rem 1.6rem;
      font-size: 1rem;
      border-radius: 30px;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 1rem;
    }

    .cta button:hover {
      background: #f1f1f1;
    }

    footer {
      background: #f0f0f0;
      padding: 1.5rem;
      text-align: center;
      color: #777;
    }

    @media (max-width: 480px) {
      .hero h1 {
        font-size: 2rem;
      }
      .cta {
        margin: 2rem 1rem;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">VideGrenier+</div>
    <nav>
      <div class="hamburger" id="hamburger">&#9776;</div>
      <div class="nav-links" id="nav-links">
        <a href="#">Accueil</a>
        <a href="#">Catégories</a>
        <a href="#">Connexion</a>
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
