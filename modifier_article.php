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
    $image_name = $article['image']; // valeur par défaut

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
        // Mise à jour de $article pour afficher les nouvelles valeurs dans le formulaire
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
<h2>Modifier l’article</h2>

<?php if ($message): ?>
    <p style="color: green;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form action="" method="POST" enctype="multipart/form-data">
    <label>Titre :</label><br>
    <input type="text" name="titre" value="<?= htmlspecialchars($article['titre']) ?>" required><br><br>

    <label>Description :</label><br>
    <textarea name="description" required><?= htmlspecialchars($article['description']) ?></textarea><br><br>

    <label>Prix (€) :</label><br>
    <input type="number" step="0.01" name="prix" value="<?= $article['prix'] ?>" required><br><br>

    <label>Catégorie :</label><br>
    <select name="categorie" required>
        <?php
        $res = $pdo->query("SELECT * FROM categories ORDER BY nom");
        while ($cat = $res->fetch()) {
            $selected = $cat['id'] == $article['categorie_id'] ? 'selected' : '';
            echo "<option value='{$cat['id']}' $selected>" . htmlspecialchars($cat['nom']) . "</option>";
        }
        ?>
    </select><br><br>

    <label>Image actuelle :</label><br>
    <img src="img/<?= htmlspecialchars($article['image']) ?>" alt="image" width="120"><br><br>

    <label>Nouvelle image (facultatif) :</label><br>
    <input type="file" name="image" accept="image/*"><br><br>

    <label>Statut :</label><br>
    <select name="statut">
        <option value="non_publie" <?= $article['statut'] === 'non_publie' ? 'selected' : '' ?>>Non publié</option>
        <option value="publie" <?= $article['statut'] === 'publie' ? 'selected' : '' ?>>Publié</option>
    </select><br><br>

    <button type="submit">Enregistrer</button>
</form>

<?php include_once 'public/footer.php'; ?>
