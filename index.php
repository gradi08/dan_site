<?php 
session_start();
require 'includes/db.php'; // Connexion à la base de données
?>

<?php include_once 'public/navbar.php'; ?>

<section class="hero">
    <h1>Donnez une nouvelle vie à vos objets inutilisés</h1>
</section>

<section class="section products">
    <div class="product-card">
        <img src="https://images.unsplash.com/photo-1512436991641-6745cdb1723f" alt="Appareil Photo Vintage">
        <div class="product-info">
            <h3>Appareil Photo Vintage</h3>
        </div>
    </div>
    <div class="product-card">
        <img src="https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f" alt="Chaise Rétro">
        <div class="product-info">
            <h3>Chaise Rétro</h3>
        </div>
    </div>
    <div class="product-card">
        <img src="https://images.unsplash.com/photo-1585559605153-6f94aa0c2e58" alt="Montre ancienne">
        <div class="product-info">
            <h3>Montre Ancienne</h3>
        </div>
    </div>
</section>

<section class="cta">
    <h2>Vous avez des objets à vendre ?</h2>
    <button>Déposer une annonce</button>
</section>

<?php include_once 'public/footer.php'; ?>