<?php
/** @var array<string, mixed> $article */
use App\Support\Category;
use App\Support\Status;

$given = $article['status'] === Status::GIVEN;
?>
<article class="card<?= $given ? ' card--given' : '' ?>">
    <a class="card__link" href="<?= url('objets/' . $article['id']) ?>">
        <div class="card__media">
            <?php if ($src = image_url($article['image'])): ?>
                <img src="<?= e($src) ?>" alt="" loading="lazy" decoding="async" width="400" height="300">
            <?php endif; ?>
            <span class="sticker" aria-hidden="true"><?= $given ? 'Donné' : 'Gratuit' ?></span>
        </div>
        <div class="card__body">
            <h3 class="card__title"><?= e($article['title']) ?></h3>
            <p class="card__meta">
                <?php if (!empty($article['location'])): ?>
                    <span><?= e($article['location']) ?></span>
                <?php endif; ?>
                <span><?= e(time_ago($article['created_at'])) ?></span>
            </p>
            <p class="card__category"><?= e(Category::label($article['category'])) ?></p>
        </div>
    </a>
</article>
