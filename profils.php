<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user']['id'];

// Récupération des infos actuelles de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$message_info = "";
$message_pwd = "";

// ======================
// Mise à jour des infos
// ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_info'])) {
    $nom   = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $tel   = trim($_POST['tel']);

    $prefixe = "+243";
    $tel = preg_replace('/\s+/', '', $tel);
    if (strpos($tel, $prefixe) !== 0) {
        if (substr($tel, 0, 1) === "0") {
            $tel = substr($tel, 1);
        }
        $tel = $prefixe . $tel;
    }

    if (!empty($nom) && !empty($email) && !empty($tel)) {
        $stmt = $pdo->prepare("UPDATE users SET nom = ?, email = ?, Tel = ? WHERE id = ?");
        $stmt->execute([$nom, $email, $tel, $user_id]);

        $_SESSION['user']['nom'] = $nom;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['Tel'] = $tel;

        $message_info = "✅ Informations mises à jour.";
    } else {
        $message_info = "❌ Tous les champs sont obligatoires.";
    }
}

// =============================
// Mise à jour du mot de passe
// =============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $ancien_pwd = trim($_POST['ancien_password']);
    $nouveau_pwd = trim($_POST['nouveau_password']);
    $confirm_pwd = trim($_POST['confirm_password']);

    if (!empty($ancien_pwd) && !empty($nouveau_pwd) && !empty($confirm_pwd)) {
        if (strlen($nouveau_pwd) < 6) {
            $message_pwd = "❌ Le nouveau mot de passe doit contenir au moins 6 caractères.";
        } elseif ($nouveau_pwd !== $confirm_pwd) {
            $message_pwd = "❌ Les mots de passe ne correspondent pas.";
        } else {
            // Vérifier l'ancien mot de passe
            $stmt = $pdo->prepare("SELECT mot_de_passe FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $hashed = $stmt->fetchColumn();

            if (password_verify($ancien_pwd, $hashed)) {
                $newHashed = password_hash($nouveau_pwd, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET mot_de_passe  = ? WHERE id = ?");
                $stmt->execute([$newHashed, $user_id]);
                $message_pwd = "✅ Mot de passe mis à jour.";
            } else {
                $message_pwd = "❌ Ancien mot de passe incorrect.";
            }
        }
    } else {
        $message_pwd = "❌ Tous les champs sont obligatoires.";
    }
}
?>

<?php include_once 'public/navbar.php'; ?>

<div class="container">
    <h2>Modifier mon profil</h2>

    <?php if ($message_info): ?>
        <p class="message <?= strpos($message_info, '✅') !== false ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message_info) ?>
        </p>
    <?php endif; ?>

    <form method="POST" class="article-form">
        <input type="hidden" name="update_info" value="1">
        <label for="nom">Nom :</label>
        <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>

        <label for="email">Email :</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label for="tel">Téléphone :</label>
        <input type="tel" name="tel" id="tel" value="<?= htmlspecialchars($user['Tel']) ?>" required>

        <button type="submit" class="btn">💾 Enregistrer</button>
    </form>

    <hr>

    <h3>Changer le mot de passe</h3>
    <?php if ($message_pwd): ?>
        <p class="message <?= strpos($message_pwd, '✅') !== false ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message_pwd) ?>
        </p>
    <?php endif; ?>

    <form method="POST" class="article-form">
        <input type="hidden" name="update_password" value="1">

        <label for="ancien_password">Ancien mot de passe :</label>
        <input type="password" name="ancien_password" id="ancien_password" required>

        <label for="nouveau_password">Nouveau mot de passe :</label>
        <input type="password" name="nouveau_password" id="nouveau_password" required>

        <label for="confirm_password">Confirmer le nouveau mot de passe :</label>
        <input type="password" name="confirm_password" id="confirm_password" required>

        <button type="submit" class="btn">🔒 Modifier le mot de passe</button>
    </form>
</div>

<style>
    .message.error {
    background-color: #ffe6e6;
    color: #d32f2f;
}

hr {
    margin: 40px 0;
    border: 0;
    border-top: 1px solid #ddd;
}

body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f4f4;
    color: #333;
}

.container {
    max-width: 600px;
    margin: 40px auto;
    padding: 30px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #007bff;
}

.message {
    text-align: center;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.message.success {
    background-color: #e0f8e0;
    color: #2e7d32;
}

.message.error {
    background-color: #fdecea;
    color: #c62828;
}

.article-form {
    display: flex;
    flex-direction: column;
}

label {
    margin-bottom: 6px;
    font-weight: bold;
}

input[type="text"],
input[type="email"],
input[type="tel"] {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    margin-bottom: 20px;
    transition: border 0.3s;
}

input:focus {
    border-color: #007bff;
    outline: none;
}

.btn {
    padding: 12px;
    background-color: #007bff;
    border: none;
    border-radius: 6px;
    color: white;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s;
}

.btn:hover {
    background-color: #0056b3;
}
</style>

<?php include_once 'public/footer.php'; ?>
