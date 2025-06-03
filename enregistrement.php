<?php
$m = null;
// $m ? $_GET['m'] : null;
if (isset($_GET['m'])) {
    $m = True;
}
if (isset($_GET['t'])) {
  $t = True;
}
// var_dump($_GET['m']);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inscription</title>
    <link rel="stylesheet" href="css/enregistrement.css">
</head>
<body>
  <div class="form-container">
    <h2>Créer un compte</h2>
    <form id="registerForm" method="POST" action="register.php">
      <div class="form-group">
        <input type="text" name="username" placeholder="Nom d'utilisateur" required>
        <div class="error-message" id="error-username">Nom requis</div>
      </div>
      <div class="form-group">
        <input type="email" name="email" placeholder="Adresse e-mail" required>
        <div class="error-message" id="error-email">Email invalide</div>
        <?php if($t):?>
         <div style="color:red"><?=$_GET['t'] ?></div> 
        <?php endif ?>
      </div>
      <div class="form-group">
        <input type="password" name="password" placeholder="Mot de passe" required>
        <div class="error-message" id="error-password">Min. 6 caractères</div>
      </div>
      <div class="form-group">
        <input type="password" name="confirm_password" placeholder="Confirmer le Mot de passe" required>
        <div class="error-message" id="error-password">Min. 6 caractères</div>
        <?php if($m):?>
         <div style="color:red"><?=$_GET['m'] ?></div> 
        <?php endif ?>
      </div>
      <button type="submit">S'inscrire</button>
      <div class="link">
        Déjà inscrit ? <a href="connexion.php">Connectez-vous</a> 
    </form>
  </div>
<script src="js/script.js"></script>
</body>
</html>
