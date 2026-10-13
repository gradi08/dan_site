<?php
/**
 * @var list<array<string, mixed>> $latest
 * @var array{published: int, given: int, givers: int} $stats
 */
use App\Support\Category;

$view = \App\Core\App::get()->view();
$board = array_slice(array_filter($latest, static fn ($a) => !empty($a['image'])), 0, 3);
?>
<section class="hero">
    <div class="wrap hero__grid">
        <div class="hero__text">
            <h1 class="hero__title">Ce qui dort chez vous peut servir à quelqu'un d'autre.</h1>
            <p class="hero__lead">
                Donnez gratuitement les objets dont vous ne vous servez plus à des voisins de Kinshasa.
                Ils vous écrivent sur WhatsApp, vous convenez ensemble du rendez-vous.
            </p>

            <form class="search" action="<?= url('objets') ?>" method="get" role="search">
                <label class="visually-hidden" for="hero-q">Rechercher un objet</label>
                <input class="search__input" id="hero-q" type="search" name="q" placeholder="Un vélo, une table, des livres…" autocomplete="off">
                <button class="btn btn--primary" type="submit">Rechercher</button>
            </form>

            <p class="hero__chips" aria-label="Catégories populaires">
                <?php foreach (['meubles', 'electronique', 'vetements', 'enfants'] as $slug): ?>
                    <a class="chip" href="<?= url('objets', ['categorie' => $slug]) ?>"><?= e(Category::label($slug)) ?></a>
                <?php endforeach; ?>
            </p>
        </div>

        <?php if ($board): ?>
            <div class="board" aria-label="Derniers objets proposés">
                <?php foreach ($board as $i => $item): ?>
                    <a class="board__item board__item--<?= $i + 1 ?>" href="<?= url('objets/' . $item['id']) ?>">
                        <img src="<?= e(image_url($item['image'])) ?>" alt="<?= e($item['title']) ?>" width="360" height="280" <?= $i === 0 ? 'fetchpriority="high"' : '' ?>>
                        <span class="board__caption"><?= e($item['title']) ?></span>
                        <span class="sticker" aria-hidden="true">Gratuit</span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="figures wrap" aria-label="La communauté en chiffres">
    <p><strong><?= $stats['published'] ?></strong> objets à donner en ce moment</p>
    <p><strong><?= $stats['given'] ?></strong> objets déjà partis chez quelqu'un</p>
    <p><strong><?= $stats['givers'] ?></strong> donneurs dans la communauté</p>
</section>

<section class="section wrap" aria-labelledby="latest-title">
    <div class="section__head">
        <h2 id="latest-title" class="section__title">Proposés récemment</h2>
        <a class="link-more" href="<?= url('objets') ?>">Voir tous les objets</a>
    </div>

    <?php if ($latest): ?>
        <div class="grid">
            <?php foreach ($latest as $article): ?>
                <?= $view->partial('partials/article-card', ['article' => $article]) ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty">
            <p>Aucun objet pour l'instant. Soyez le premier à donner quelque chose.</p>
            <a class="btn btn--primary" href="<?= url('mes-annonces/nouvelle') ?>">Donner un objet</a>
        </div>
    <?php endif; ?>
</section>

<section class="section steps-section" aria-labelledby="how-title">
    <div class="wrap">
        <h2 id="how-title" class="section__title">Comment donner un objet</h2>
        <ol class="steps">
            <li>
                <h3>Prenez-le en photo</h3>
                <p>Une photo nette à la lumière du jour, un titre clair, et précisez votre commune.</p>
            </li>
            <li>
                <h3>Publiez l'annonce</h3>
                <p>Elle apparaît tout de suite sur le site. Votre numéro n'est visible que par les membres connectés.</p>
            </li>
            <li>
                <h3>Répondez sur WhatsApp</h3>
                <p>Les personnes intéressées vous écrivent directement. Vous choisissez à qui donner.</p>
            </li>
            <li>
                <h3>Marquez-le comme donné</h3>
                <p>Un clic dans « Mes annonces » et plus personne ne vous écrit pour cet objet.</p>
            </li>
        </ol>
    </div>
</section>

<section class="closing">
    <div class="wrap closing__inner">
        <h2 class="closing__title">Un placard à vider avant la fin du mois ?</h2>
        <a class="btn btn--sticker" href="<?= url('mes-annonces/nouvelle') ?>">Donner un objet</a>
    </div>
</section>
