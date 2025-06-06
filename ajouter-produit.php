<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$message = "";

// Traitement du formulaire
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

<div class="container">
    <h2>Ajouter un Nouvel Article</h2>

    <?php if ($message): ?>
        <p class="message success"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" class="article-form">
        <label>Titre :</label>
        <input type="text" name="titre" required>

        <label>Description :</label>
        <textarea name="description" required></textarea>

        <label>Prix (€) :</label>
        <input type="number" step="0.01" name="prix" required>

        <label>Catégorie :</label>
        <select name="categorie" required>
            <option value="">-- Sélectionner --</option>
            <?php
            $res = $pdo->query("SELECT * FROM categories ORDER BY nom");
            while ($cat = $res->fetch()) {
                echo "<option value='" . $cat['id'] . "'>" . htmlspecialchars($cat['nom']) . "</option>";
            }
            ?>
        </select>

        <label>Image :</label>
        <input type="file" name="image" accept="image/*" required>

        <button type="submit" class="btn">Ajouter</button>
    </form>
</div>
<style>
    body {
    font-family: 'Arial', sans-serif;
    background-color: #f7f7f7;
    color: #333;
}

.container {
    max-width: 600px;
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
</style>

<?php include_once 'public/footer.php'; ?>