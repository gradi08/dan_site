<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$utilisateur_id = $_SESSION['user']['id'];
$autre_id = intval($_GET['avec'] ?? 0);

// Vérifie si l'autre utilisateur existe
$stmt = $pdo->prepare("SELECT nom FROM users WHERE id = ?");
$stmt->execute([$autre_id]);
$autre_utilisateur = $stmt->fetch();

if (!$autre_utilisateur) {
    echo "Utilisateur introuvable.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Discussion avec <?= htmlspecialchars($autre_utilisateur['nom']) ?></title>
    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            padding: 0; margin: 0;
        }

        .chatbox {
            width: 60%;
            max-width: 800px;
            margin: 50px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
            padding: 20px;
        }

        .messages {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 15px;
            background: #fafafa;
        }

        .message {
            margin: 10px 0;
            padding: 8px 12px;
            border-radius: 10px;
            max-width: 70%;
            clear: both;
        }

        .from-me {
            background: #dcf8c6;
            float: right;
            text-align: right;
        }

        .from-them {
            background: #eee;
            float: left;
        }

        form {
            display: flex;
        }

        input[type="text"] {
            flex: 1;
            padding: 10px;
            border-radius: 5px 0 0 5px;
            border: 1px solid #ccc;
        }

        button {
            padding: 10px 20px;
            border: none;
            background: #28a745;
            color: white;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="chatbox">
    <h2>Discussion avec <?= htmlspecialchars($autre_utilisateur['nom']) ?></h2>

    <div class="messages" id="messages"></div>

    <form id="chat-form">
        <input type="text" id="contenu" placeholder="Écris ton message..." autocomplete="off" required>
        <button type="submit">Envoyer</button>
    </form>
</div>

<script>
    const messagesDiv = document.getElementById('messages');
    const form = document.getElementById('chat-form');
    const contenuInput = document.getElementById('contenu');
    const autre_id = <?= $autre_id ?>;

    function chargerMessages() {
        fetch('load_messages.php?avec=' + autre_id)
            .then(res => res.json())
            .then(messages => {
                messagesDiv.innerHTML = '';
                messages.forEach(msg => {
                    const div = document.createElement('div');
                    div.classList.add('message');
                    div.classList.add(msg.expediteur_id == <?= $utilisateur_id ?> ? 'from-me' : 'from-them');
                    div.innerText = msg.contenu;
                    messagesDiv.appendChild(div);
                });
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const contenu = contenuInput.value.trim();
        if (!contenu) return;

        fetch('send_message.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                destinataire_id: autre_id,
                contenu: contenu
            })
        }).then(res => res.json())
          .then(data => {
              if (data.status === 'success') {
                  contenuInput.value = '';
                  chargerMessages();
              }
          });
    });

    // Actualisation automatique toutes les 3 secondes
    setInterval(chargerMessages, 3000);
    chargerMessages();
</script>

</body>
</html>
