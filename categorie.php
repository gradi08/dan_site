<?php
require 'includes/db.php';

$nomCategorie = isset($_GET['nom']) ? htmlspecialchars($_GET['nom']) : '';

$stmt = $pdo->prepare("SELECT * FROM articles WHERE categorie = ?");
$stmt->execute([$nomCategorie]);
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Catégorie - <?= ucfirst($nomCategorie) ?></title>
  <link rel="stylesheet" href="css/index.css">
</head>
<body>
  <header>
    <div class="logo"><a href="index.html">VideGrenier+</a></div>
  </header>

  <section class="hero">
    <h1>Catégorie : <?= ucfirst($nomCategorie) ?></h1>
  </section>

  <section class="products">
    <?php if ($produits): ?>
      <?php foreach ($produits as $produit): ?>
        <div class="product-card">
          <img src="<?= $produit['image'] ?>" alt="<?= $produit['nom'] ?>">
          <div class="product-info">
            <h3><?= $produit['nom'] ?></h3>
            <p><?= number_format($produit['prix'], 2) ?> €</p>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p style='padding:2rem;'>Aucun produit trouvé pour cette catégorie.</p>
    <?php endif; ?>
  </section>

  <footer>
    © 2025 VideGrenier+. Tous droits réservés.
  </footer>
</body>
</html>
