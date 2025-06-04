<?php 
session_start();
require 'includes/db.php'; // Assurez-vous que ce fichier contient la connexion à la base de données
?>
<?php include_once 'public/navbar.php';?>

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