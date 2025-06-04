<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>VideGrenier+ | Donnez une nouvelle vie à vos objets</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/index.css">
   <style>
        .article {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 12px;
            margin: 10px 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .article img {
            width: 120px;
            height: auto;
            object-fit: cover;
            border-radius: 5px;
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
        <a href="mes_articles.php">Vendre</a>
      </div>
    </nav>
  </header>
  