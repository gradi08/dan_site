<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Messagerie - Vide-Grenier</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }

    body {
      margin: 0;
      display: flex;
      height: 100vh;
      background: #f2f4f8;
    }

    .messagerie {
      display: flex;
      width: 100%;
      max-width: 1200px;
      margin: auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      background: white;
    }

    .liste-utilisateurs {
      width: 30%;
      border-right: 1px solid #e0e0e0;
      overflow-y: auto;
      background: #fafafa;
    }

    .utilisateur {
      padding: 15px;
      cursor: pointer;
      border-bottom: 1px solid #eee;
      transition: background 0.3s;
    }

    .utilisateur:hover,
    .utilisateur.active {
      background-color: #e0f0ff;
    }

    .discussion {
      width: 70%;
      display: flex;
      flex-direction: column;
    }

    .messages {
      flex: 1;
      padding: 20px;
      overflow-y: auto;
    }

    .message {
      margin-bottom: 15px;
      max-width: 70%;
      padding: 10px 15px;
      border-radius: 15px;
      font-size: 14px;
      line-height: 1.5;
      position: relative;
    }

    .message.envoye {
      background: #d1e7ff;
      align-self: flex-end;
    }

    .message.recu {
      background: #eeeeee;
      align-self: flex-start;
    }

    .form-message {
      display: flex;
      padding: 15px;
      border-top: 1px solid #ddd;
      background: #fff;
    }

    .form-message input {
      flex: 1;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 20px;
      outline: none;
    }

    .form-message button {
      margin-left: 10px;
      padding: 10px 20px;
      background: #007bff;
      border: none;
      color: white;
      border-radius: 20px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .form-message button:hover {
      background: #0056b3;
    }

  </style>
</head>
<body>

  <div class="messagerie">

    <div class="liste-utilisateurs" id="utilisateurs">
      <div class="utilisateur active">Alice</div>
      <div class="utilisateur">Jean</div>
      <div class="utilisateur">Sophie</div>
      <div class="utilisateur">Luc</div>
    </div>

    <div class="discussion">
      <div class="messages" id="zoneMessages">
        <div class="message recu">Bonjour, ce produit est-il encore disponible ?</div>
        <div class="message envoye">Oui, il l’est toujours 🙂</div>
      </div>

      <form class="form-message" onsubmit="envoyerMessage(event)">
        <input type="text" id="messageInput" placeholder="Écrire un message...">
        <button type="submit">Envoyer</button>
      </form>
    </div>

  </div>

  <script>
    const utilisateurs = document.querySelectorAll('.utilisateur');
    utilisateurs.forEach(u => {
      u.addEventListener('click', () => {
        utilisateurs.forEach(e => e.classList.remove('active'));
        u.classList.add('active');
        // Ici tu pourras ajouter la logique AJAX pour charger la discussion
      });
    });

    function envoyerMessage(e) {
      e.preventDefault();
      const input = document.getElementById('messageInput');
      const text = input.value.trim();
      if (text) {
        const zone = document.getElementById('zoneMessages');
        const nouveauMessage = document.createElement('div');
        nouveauMessage.className = 'message envoye';
        nouveauMessage.textContent = text;
        zone.appendChild(nouveauMessage);
        zone.scrollTop = zone.scrollHeight;
        input.value = '';
        // Ici, tu pourras ajouter une requête AJAX pour enregistrer le message côté PHP
      }
    }
  </script>

</body>
</html>
