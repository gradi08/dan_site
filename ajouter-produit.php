<?php
require 'includes/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = htmlspecialchars($_POST['nom']);
    $prix = floatval($_POST['prix']);
    $categorie = htmlspecialchars($_POST['categorie']);

    // Upload de l'image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $dossier = "img/";
        if (!is_dir($dossier)) mkdir($dossier); // Crée le dossier si inexistant

        $imageNom = basename($_FILES['image']['name']);
        $chemin = $dossier . $imageNom;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $chemin)) {
            $stmt = $pdo->prepare("INSERT INTO produits (nom, prix, image, categorie) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nom, $prix, $chemin, $categorie]);
            $message = "✅ Produit ajouté avec succès.";
        } else {
            $message = "❌ Erreur lors de l’upload de l’image.";
        }
    } else {
        $message = "❗ Veuillez sélectionner une image valide.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un produit</title>
    <link rel="stylesheet" href="css/index.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 2rem;
        }
        h2 {
            text-align: center;
            margin-bottom: 2rem;
            color: #333;
        }
        form {
            max-width: 500px;
            margin: auto;
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-top: 1rem;
            font-weight: bold;
            color: #444;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: border-color 0.3s;
        }
        input:focus, select:focus {
            border-color: #007BFF;
            outline: none;
        }
        button {
            margin-top: 1.5rem;
            width: 100%;
            padding: 12px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #0056b3;
        }
        .message {
            text-align: center;
            margin-bottom: 1.5rem;
            padding: 10px;
            border-radius: 8px;
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <h2>Ajouter un produit</h2>

    <?php if ($message): ?>
        <div class="message <?= str_contains($message, '❌') || str_contains($message, '❗') ? 'error' : '' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Nom du produit :</label>
        <input type="text" name="nom" required>

        <label>Prix (€) :</label>
        <input type="number" name="prix" step="0.01" required>

        <label>Image :</label>
        <input type="file" name="image" accept="image/*" required>

        <label>Catégorie :</label>
        <select name="categorie" required>
            <option value="" disabled selected>-- Choisir une catégorie --</option>
            <option value="vetements">Vêtements</option>
            <option value="electronique">Électronique</option>
            <option value="maison">Maison</option>
            <option value="autre">Autre</option>
        </select>

        <button type="submit">Ajouter le produit</button>
    </form>
</body>
</html>
