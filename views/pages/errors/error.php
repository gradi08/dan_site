<?php
/**
 * @var int $status
 * @var string $message
 */
[$heading, $default] = match ($status) {
    404     => ['Page introuvable', "Cette page n'existe pas ou plus. L'objet a peut-être été retiré par son donneur."],
    403     => ['Accès refusé', "Vous n'avez pas accès à cette page."],
    405     => ['Action non autorisée', "Cette action n'est pas disponible à cette adresse."],
    413     => ['Envoi trop volumineux', 'Réessayez avec des fichiers plus légers.'],
    419     => ['Session expirée', 'Rechargez la page et réessayez.'],
    default => ['Un problème est survenu', "Le serveur n'a pas pu traiter la demande. Réessayez dans un instant."],
};
$this->set('title', $heading);
?>
<section class="wrap error-page">
    <p class="sticker sticker--lg sticker--static" aria-hidden="true"><?= $status ?></p>
    <h1 class="page-title"><?= e($heading) ?></h1>
    <p class="muted"><?= e($message !== '' ? $message : $default) ?></p>
    <a class="btn btn--primary" href="<?= url('objets') ?>">Voir les objets à donner</a>
</section>
