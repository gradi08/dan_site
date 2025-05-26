<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="css/connexion.css">
  <title>Connexion</title>
</head>
<body>
  <div class="form-container">
    <h2>Connexion</h2>
    <form id="loginForm" method="POST" action="login.php">
      <div class="form-group">
        <input type="email" name="email" placeholder="Adresse e-mail" required />
        <div class="error-message" id="error-email">Email requis</div>
      </div>
      <div class="form-group">
        <input type="password" name="password" placeholder="Mot de passe" required   />
        <div class="error-message" id="error-password">Mot de passe requis</div>
      </div>
      <button type="submit">Se connecter</button>
      <div class="link">
        vous n'avez pas de compte? <a href="enregistrement.php">Créer un compte</a>
      </div>
    </form>
  </div>

  <script>
    const loginForm = document.getElementById("loginForm");

    loginForm.addEventListener("submit", function (e) {
      const email = loginForm.email.value.trim();
      const password = loginForm.password.value.trim();
      let valid = true;

      if (email === "") {
        document.getElementById("error-email").style.display = "block";
        valid = false;
      } else {
        document.getElementById("error-email").style.display = "none";
      }

      if (password === "") {
        document.getElementById("error-password").style.display = "block";
        valid = false;
      } else {
        document.getElementById("error-password").style.display = "none";
      }

      if (!valid) e.preventDefault();
    });
  </script>
</body>
</html>
