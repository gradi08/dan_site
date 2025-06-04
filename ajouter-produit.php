<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
#var_dump($_SESSION['user']['id']);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $prix = floatval($_POST['prix']);
    $categorie_id = intval($_POST['categorie']);
    $user_id = $_SESSION['user']['id'];

    // Vérifier que tous les champs sont remplis
    if (!empty($titre) && !empty($description) && $prix > 0 && $categorie_id && isset($_FILES['image'])) {
        // Traitement de l’image
        $image = $_FILES['image'];
        $image_name = time() . "_" . basename($image['name']);
        $image_path = 'img/' . $image_name;

        if (move_uploaded_file($image['tmp_name'], $image_path)) {
            // Insertion dans la base de données
            $stmt = $pdo->prepare("INSERT INTO articles (titre, description, prix, categorie_id, image, user_id, statut, date_creation)
                                   VALUES (?, ?, ?, ?, ?, ?, 'non_publie', NOW())");
            $stmt->execute([$titre, $description, $prix, $categorie_id, $image_name, $user_id]);

            $message = "Article ajouté avec succès.";
        } else {
            $message = "Erreur lors de l’upload de l’image.";
        }
    } else {
        $message = "Tous les champs sont obligatoires.";
    }
}
?>

<?php include_once 'public/navbar.php'; ?>

<h2>Ajouter un nouvel article</h2>

<?php if ($message): ?>
    <p style="color: green;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form action="" method="POST" enctype="multipart/form-data">
    <label>Titre :</label><br>
    <input type="text" name="titre" required><br><br>

    <label>Description :</label><br>
    <textarea name="description" required></textarea><br><br>

    <label>Prix (€) :</label><br>
    <input type="number" step="0.01" name="prix" required><br><br>

    <label>Catégorie :</label><br>
    <select name="categorie" required>
        <option value="">-- Sélectionner --</option>
        <?php
        $res = $pdo->query("SELECT * FROM categories ORDER BY nom");
        while ($cat = $res->fetch()) {
            echo "<option value='" . $cat['id'] . "'>" . htmlspecialchars($cat['nom']) . "</option>";
        }
        ?>
    </select><br><br>

    <label>Image :</label><br>
    <input type="file" name="image" accept="image/*" required><br><br>

    <button type="submit">Ajouter</button>
</form>

<?php include_once 'public/footer.php'; ?>
