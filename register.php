<?php
# Page d'inscription

require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
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
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashed]);
            header('Location: connexion.php');
            exit;
        } else {
            echo "Email déjà utilisé.";
        }
    } else {
        echo "Tous les champs sont obligatoires.";
    }
}
