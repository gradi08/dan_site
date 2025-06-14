<?php
# Page d'inscription

require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prefixe = "+243";// Préfixe pour le numéro de téléphone congolais
    $Tel = trim($_POST['phone']);
// Nettoyage pour enlever les espaces et caractères non numériques sauf +
    $Tel = preg_replace('/\s+/', '', $Tel);
// Vérifie s’il commence déjà par +243
if (strpos($Tel, $prefixe) !== 0) {
    if (substr($Tel, 0, 1) === "0") {
        $Tel = substr($Tel, 1);
    }
    $Tel = $prefixe . $Tel;
}
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $phone = $Tel; // Bien formaté, prêt à être stocké
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($username) && !empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($password != $confirm_password) {
            $m = "Veuillez confirmer le mot de passe";
            header('Location: enregistrement.php?m=Veuillez confirmer le mot de passe');
        }
        elseif (!$stmt->fetch()) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (nom, email,Tel, mot_de_passe,date_inscription) VALUES (?,?, ?, ?,NOW())");
            $stmt->execute([$username, $email,$phone, $hashed]);
            header('Location: connexion.php');
            exit;
        } else {
            header('Location: enregistrement.php?t=Email déjà utilisé');
        }
    } else {
        echo "Tous les champs sont obligatoires.";
    }
}
