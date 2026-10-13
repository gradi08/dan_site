<?php
/**
 * @var array<string, mixed> $article
 * @var bool $isOwner
 */
use App\Support\Category;
use App\Support\Phone;
use App\Support\Status;

$this->set('title', $article['title']);
$this->set('description', mb_strimwidth($article['description'], 0, 155, '…'));
$this->set('ogImage', image_url($article['image']));
$given = $article['status'] === Status::GIVEN;
$firstName = explode(' ', trim($article['owner_name']))[0];
?>
<div class="wrap">
    <nav class="breadcrumb" aria-label="Fil d'Ariane">
        <a href="<?= url('objets') ?>">Objets à donner</a>
        <span aria-hidden="true">/</span>
        <a href="<?= url('objets', ['categorie' => $article['category']]) ?>"><?= e(Category::label($article['category'])) ?></a>
    </nav>

    <article class="detail">
        <div class="detail__media">
            <div class="gallery">
                <div class="gallery__track" tabindex="0" aria-label="Photos de l'objet<?= count($images) > 1 ? ', faites défiler' : '' ?>">
                    <?php foreach ($images as $i => $image): ?>
                        <img id="photo-<?= $i + 1 ?>" class="gallery__slide"
                             src="<?= e(image_url($image['filename'])) ?>"
                             alt="<?= e($article['title']) ?><?= count($images) > 1 ? ', photo ' . ($i + 1) . ' sur ' . count($images) : '' ?>"
                             width="800" height="600" <?= $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
                    <?php endforeach; ?>
                </div>
                <span class="sticker sticker--lg" aria-hidden="true"><?= $given ? 'Donné' : 'Gratuit' ?></span>
            </div>

            <?php if (count($images) > 1): ?>
                <nav class="gallery__thumbs" aria-label="Choisir une photo">
                    <?php foreach ($images as $i => $image): ?>
                        <a href="#photo-<?= $i + 1 ?>" <?= $i === 0 ? 'aria-current="true"' : '' ?>>
                            <img src="<?= e(image_url($image['filename'])) ?>" alt="Photo <?= $i + 1 ?>" width="96" height="72" loading="lazy">
                        </a>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>
        </div>

        <div class="detail__body">
            <h1 class="detail__title"><?= e($article['title']) ?></h1>

            <dl class="facts">
                <?php if (!empty($article['location'])): ?>
                    <div><dt>Où</dt><dd><?= e($article['location']) ?>, Kinshasa</dd></div>
                <?php endif; ?>
                <div><dt>Publié</dt><dd><?= e(time_ago($article['created_at'])) ?></dd></div>
                <div><dt>Catégorie</dt><dd><?= e(Category::label($article['category'])) ?></dd></div>
            </dl>

            <div class="detail__description">
                <?= nl2br(e($article['description'])) ?>
            </div>

            <div class="giver">
                <span class="avatar" aria-hidden="true"><?= e(initials($article['owner_name'])) ?></span>
                <p>Proposé par <strong><?= e($firstName) ?></strong></p>
            </div>

            <?php if ($given): ?>
                <p class="notice">Cet objet a déjà trouvé preneur. Jetez un œil aux <a href="<?= url('objets', ['categorie' => $article['category']]) ?>">autres objets de la catégorie</a>.</p>
            <?php elseif ($isOwner): ?>
                <p class="notice">C'est votre annonce.</p>
                <a class="btn btn--ghost btn--block" href="<?= url('mes-annonces/' . $article['id'] . '/modifier') ?>">Modifier l'annonce</a>
            <?php elseif (current_user()): ?>
                <a class="btn btn--whatsapp btn--block"
                   href="<?= e(Phone::whatsappLink($article['owner_phone'], "Bonjour $firstName, je suis intéressé(e) par votre annonce « {$article['title']} » sur VideGrenier+. Est-elle toujours disponible ?")) ?>"
                   target="_blank" rel="noopener noreferrer">
                    <svg aria-hidden="true" width="20" height="20" viewBox="0 0 16 16" fill="currentColor"><path d="M13.6 2.33A7.87 7.87 0 0 0 8 0C3.58 0 0 3.58 0 8c0 1.44.38 2.84 1.1 4.08L0 16l4.14-1.09A7.94 7.94 0 0 0 8 15.9h.003C12.41 15.9 16 12.32 16 7.9a7.94 7.94 0 0 0-2.4-5.57zM8 14.5a6.48 6.48 0 0 1-3.3-.9l-.24-.14-2.45.65.65-2.4-.15-.24A6.48 6.48 0 0 1 1.5 8 6.5 6.5 0 0 1 8 1.5a6.46 6.46 0 0 1 6.5 6.4c0 3.59-2.91 6.6-6.5 6.6zm3.05-4.88c-.15-.08-.88-.43-1.01-.48-.14-.05-.24-.08-.33.07-.1.15-.38.49-.47.58-.08.1-.17.11-.32.04-.15-.08-.63-.23-1.2-.74-.44-.4-.74-.88-.83-1.03-.09-.15 0-.23.07-.3.07-.07.15-.18.22-.26.08-.09.1-.15.15-.25.05-.1.03-.19-.01-.26-.04-.08-.33-.8-.45-1.09-.12-.29-.24-.25-.33-.25h-.28c-.1 0-.26.04-.4.19-.14.15-.52.5-.52 1.24s.53 1.43.6 1.53c.08.1 1.05 1.6 2.54 2.24.35.15.63.24.85.31.35.11.68.1.93.06.29-.04.88-.36 1-.71.12-.35.12-.65.09-.71-.04-.06-.14-.1-.29-.17z"/></svg>
                    Écrire à <?= e($firstName) ?> sur WhatsApp
                </a>
                <p class="hint">Convenez d'un lieu public pour la remise de l'objet.</p>
            <?php else: ?>
                <a class="btn btn--primary btn--block" href="<?= url('connexion') ?>">Se connecter pour contacter <?= e($firstName) ?></a>
                <p class="hint">Les numéros ne sont visibles que par les membres, pour éviter le démarchage. <a href="<?= url('inscription') ?>">Créer un compte</a> prend 30 secondes.</p>
            <?php endif; ?>
        </div>
    </article>
</div>
