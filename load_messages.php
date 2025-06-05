<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$utilisateur_id = $_SESSION['user']['id'];
$autre_id = intval($_GET['avec'] ?? 0);

$stmt = $pdo->prepare("
    SELECT m.*, u.nom AS expediteur_nom
    FROM messages m
    JOIN utilisateurs u ON m.expediteur_id = u.id
    WHERE 
        (m.expediteur_id = ? AND m.destinataire_id = ?)
        OR
        (m.expediteur_id = ? AND m.destinataire_id = ?)
    ORDER BY m.date_envoi ASC
");
$stmt->execute([$utilisateur_id, $autre_id, $autre_id, $utilisateur_id]);
$messages = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($messages);
