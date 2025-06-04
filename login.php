<?php
# Page de connexion
require 'includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'nom' => $user['nom'],
            'email' => $user['email']
        ];
        header('Location: index.php');
        exit;
    } else {
        echo "Identifiants incorrects.";
    }
}

