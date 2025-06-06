<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user']['id'];

if (!isset($_GET['id'])) {
    die("Article non spécifié.");
}

$article_id = intval($_GET['id']);

// Vérifier que l'article appartient à l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ? AND user_id = ?");
$stmt->execute([$article_id, $user_id]);
$article = $stmt->fetch();

if (!$article) {
    die("Article introuvable ou accès interdit.");
}

$message = "";

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $prix = floatval($_POST['prix']);
    $categorie_id = intval($_POST['categorie']);
    $statut = $_POST['statut'] === 'publie' ? 'publie' : 'non_publie';
    $image_name = $article['image']; // Valeur par défaut

    // Si nouvelle image envoyée
    if (!empty($_FILES['image']['name'])) {
        $new_image = $_FILES['image'];
        $image_name = time() . "_" . basename($new_image['name']);
        $image_path = 'img/' . $image_name;

        if (!move_uploaded_file($new_image['tmp_name'], $image_path)) {
            $message = "Erreur lors de l’upload de la nouvelle image.";
        }
    }

    if (empty($message)) {
        $stmt = $pdo->prepare("UPDATE articles SET titre = ?, description = ?, prix = ?, categorie_id = ?, image = ?, statut = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$titre, $description, $prix, $categorie_id, $image_name, $statut, $article_id, $user_id]);

        $message = "Article modifié avec succès.";
        // Mise à jour de $article
        $article = array_merge($article, [
            'titre' => $titre,
            'description' => $description,
            'prix' => $prix,
            'categorie_id' => $categorie_id,
            'statut' => $statut,
            'image' => $image_name
        ]);
    }
}
?>
<?php include_once 'public/navbar.php'; ?>

<div class="container">
    <h2>Modifier l’article</h2>

    <?php if ($message): ?>
        <p class="message success"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" class="article-form">
        <label>Titre :</label>
        <input type="text" name="titre" value="<?= htmlspecialchars($article['titre']) ?>" required>

        <label>Description :</label>
        <textarea name="description" required><?= htmlspecialchars($article['description']) ?></textarea>

        <label>Prix (€) :</label>
        <input type="number" step="0.01" name="prix" value="<?= $article['prix'] ?>" required>

        <label>Catégorie :</label>
        <select name="categorie" required>
            <?php
            $res = $pdo->query("SELECT * FROM categories ORDER BY nom");
            while ($cat = $res->fetch()) {
                $selected = $cat['id'] == $article['categorie_id'] ? 'selected' : '';
                echo "<option value='{$cat['id']}' $selected>" . htmlspecialchars($cat['nom']) . "</option>";
            }
            ?>
        </select>

        <label>Image actuelle :</label>
        <img src="img/<?= htmlspecialchars($article['image']) ?>" alt="image" width="120">

        <label>Nouvelle image (facultatif) :</label>
        <input type="file" name="image" accept="image/*">

        <label>Statut :</label>
        <select name="statut">
            <option value="non_publie" <?= $article['statut'] === 'non_publie' ? 'selected' : '' ?>>Non publié</option>
            <option value="publie" <?= $article['statut'] === 'publie' ? 'selected' : '' ?>>Publié</option>
        </select>

        <button type="submit" class="btn">Enregistrer</button>
    </form>
</div>
<style>
    body {
    font-family: 'Arial', sans-serif;
    background-color: #f7f7f7;
    color: #333;
}

.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    color: #333;
}

.message.success {
    color: green;
    text-align: center;
    margin-bottom: 20px;
}

.article-form {
    display: flex;
    flex-direction: column;
}

label {
    margin: 10px 0 5px;
}

input[type="text"],
input[type="number"],
textarea,
select {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-bottom: 15px;
}

input[type="file"] {
    margin-bottom: 15px;
}

.btn {
    padding: 10px 15px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn:hover {
    background-color: #0056b3;
}

img {
    margin: 10px 0;
}
</style>
<?php include_once 'public/footer.php'; ?>