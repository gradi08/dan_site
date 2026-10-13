<?php
/**
 * @var list<array<string, mixed>> $articles
 * @var int $total
 * @var int $page
 * @var int $pages
 * @var string $q
 * @var string $category
 */
use App\Support\Category;

$view = \App\Core\App::get()->view();
$this->set('title', 'Objets à donner');
$filtered = $q !== '' || $category !== '';
?>
<section class="page-head wrap">
    <h1 class="page-title">Objets à donner</h1>
    <p class="muted">
        <?= $total ?> objet<?= $total > 1 ? 's' : '' ?>
        <?= $filtered ? 'correspond' . ($total > 1 ? 'ent' : '') . ' à votre recherche' : 'disponible' . ($total > 1 ? 's' : '') ?>.
    </p>

    <form class="search search--inline" action="<?= url('objets') ?>" method="get" role="search">
        <label class="visually-hidden" for="q">Rechercher</label>
        <input class="search__input" id="q" type="search" name="q" value="<?= e($q) ?>" placeholder="Rechercher par mot-clé ou commune" autocomplete="off">
        <?php if ($category !== ''): ?>
            <input type="hidden" name="categorie" value="<?= e($category) ?>">
        <?php endif; ?>
        <button class="btn btn--primary" type="submit">Rechercher</button>
    </form>

    <nav class="filters" aria-label="Filtrer par catégorie">
        <a class="chip" href="<?= url('objets', ['q' => $q]) ?>" <?= $category === '' ? 'aria-current="true"' : '' ?>>Toutes</a>
        <?php foreach (Category::ALL as $slug => $label): ?>
            <a class="chip" href="<?= url('objets', ['q' => $q, 'categorie' => $slug]) ?>" <?= $category === $slug ? 'aria-current="true"' : '' ?>><?= e($label) ?></a>
        <?php endforeach; ?>
    </nav>
</section>

<section class="wrap section section--tight">
    <?php if ($articles): ?>
        <div class="grid">
            <?php foreach ($articles as $article): ?>
                <?= $view->partial('partials/article-card', ['article' => $article]) ?>
            <?php endforeach; ?>
        </div>

        <?php if ($pages > 1): ?>
            <nav class="pagination" aria-label="Pagination">
                <?php if ($page > 1): ?>
                    <a class="btn btn--ghost" href="<?= url('objets', ['q' => $q, 'categorie' => $category, 'page' => $page - 1]) ?>" rel="prev">Page précédente</a>
                <?php endif; ?>
                <span class="muted">Page <?= $page ?> sur <?= $pages ?></span>
                <?php if ($page < $pages): ?>
                    <a class="btn btn--ghost" href="<?= url('objets', ['q' => $q, 'categorie' => $category, 'page' => $page + 1]) ?>" rel="next">Page suivante</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    <?php else: ?>
        <div class="empty">
            <?php if ($filtered): ?>
                <p>Rien ne correspond à cette recherche pour le moment. Essayez un mot plus court ou une autre catégorie.</p>
                <a class="btn btn--ghost" href="<?= url('objets') ?>">Voir tous les objets</a>
            <?php else: ?>
                <p>Aucun objet n'est proposé pour l'instant. Lancez le mouvement en donnant le premier.</p>
                <a class="btn btn--primary" href="<?= url('mes-annonces/nouvelle') ?>">Donner un objet</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
