<?php
/**
 * @var list<array<string, mixed>> $articles
 * @var array<string, int> $stats
 */
use App\Support\Status;

$this->set('title', 'Mes annonces');
?>
<section class="page-head wrap page-head--row">
    <div>
        <h1 class="page-title">Mes annonces</h1>
        <p class="tally">
            <span><strong><?= $stats[Status::PUBLISHED] ?></strong> en ligne</span>
            <span><strong><?= $stats[Status::DRAFT] ?></strong> brouillon<?= $stats[Status::DRAFT] > 1 ? 's' : '' ?></span>
            <span><strong><?= $stats[Status::GIVEN] ?></strong> donné<?= $stats[Status::GIVEN] > 1 ? 's' : '' ?></span>
        </p>
    </div>
    <a class="btn btn--primary" href="<?= url('mes-annonces/nouvelle') ?>">Donner un objet</a>
</section>

<section class="wrap section section--tight">
    <?php if (!$articles): ?>
        <div class="empty">
            <p>Vous n'avez encore rien proposé. Une photo, un titre, et votre premier objet est en ligne.</p>
            <a class="btn btn--primary" href="<?= url('mes-annonces/nouvelle') ?>">Donner mon premier objet</a>
        </div>
    <?php else: ?>
        <ul class="listing" role="list">
            <?php foreach ($articles as $a): ?>
                <li class="listing__row">
                    <div class="listing__thumb">
                        <?php if ($src = image_url($a['image'])): ?>
                            <img src="<?= e($src) ?>" alt="" width="96" height="72" loading="lazy">
                        <?php endif; ?>
                    </div>

                    <div class="listing__main">
                        <h2 class="listing__title">
                            <?php if ($a['status'] === Status::DRAFT): ?>
                                <?= e($a['title']) ?>
                            <?php else: ?>
                                <a href="<?= url('objets/' . $a['id']) ?>"><?= e($a['title']) ?></a>
                            <?php endif; ?>
                        </h2>
                        <p class="listing__meta">
                            <span class="status status--<?= e($a['status']) ?>"><?= e(Status::label($a['status'])) ?></span>
                            <span class="muted">Créée <?= e(time_ago($a['created_at'])) ?></span>
                        </p>
                    </div>

                    <div class="listing__actions">
                        <?php
                        $next = match ($a['status']) {
                            Status::PUBLISHED => [Status::GIVEN, 'Marquer comme donné'],
                            Status::DRAFT     => [Status::PUBLISHED, 'Publier'],
                            default           => [Status::PUBLISHED, 'Remettre en ligne'],
                        };
                        ?>
                        <form method="post" action="<?= url('mes-annonces/' . $a['id'] . '/statut') ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="status" value="<?= e($next[0]) ?>">
                            <button class="btn btn--sm <?= $next[0] === Status::GIVEN ? 'btn--sticker' : 'btn--primary' ?>" type="submit"><?= e($next[1]) ?></button>
                        </form>
                        <a class="btn btn--sm btn--ghost" href="<?= url('mes-annonces/' . $a['id'] . '/modifier') ?>">Modifier</a>
                        <button class="btn btn--sm btn--danger-ghost" type="button" commandfor="delete-<?= (int) $a['id'] ?>" command="show-modal">Supprimer</button>
                    </div>

                    <dialog class="dialog" id="delete-<?= (int) $a['id'] ?>" aria-labelledby="delete-title-<?= (int) $a['id'] ?>">
                        <h2 class="dialog__title" id="delete-title-<?= (int) $a['id'] ?>">Supprimer « <?= e($a['title']) ?> » ?</h2>
                        <p class="muted">L'annonce et sa photo seront effacées définitivement. Si vous avez donné l'objet, préférez « Marquer comme donné ».</p>
                        <form class="dialog__actions" method="post" action="<?= url('mes-annonces/' . $a['id'] . '/supprimer') ?>">
                            <?= csrf_field() ?>
                            <button class="btn btn--ghost" type="button" commandfor="delete-<?= (int) $a['id'] ?>" command="close" autofocus>Annuler</button>
                            <button class="btn btn--danger" type="submit">Supprimer l'annonce</button>
                        </form>
                    </dialog>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
