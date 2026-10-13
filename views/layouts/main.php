<?php
/** @var string $content */
$appName = \App\Core\App::get()->config('name');
$pageTitle = isset($title) ? $title . ' | ' . $appName : $appName . ' | Donnez une seconde vie à vos objets';
$pageDescription = $description ?? 'Donnez gratuitement les objets dont vous ne vous servez plus à des voisins de Kinshasa. Contact direct par WhatsApp.';
$user = current_user();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($pageDescription) ?>">
    <meta property="og:type" content="website">
    <?php if (!empty($ogImage)): ?>
        <meta property="og:image" content="<?= e($ogImage) ?>">
    <?php endif; ?>
    <meta name="theme-color" content="#1B1F3B">
    <link rel="icon" href="<?= asset('assets/img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500..800&family=Public+Sans:ital,wght@0,400..700;1,400&display=swap">
    <link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>">
    <script src="<?= asset('assets/js/app.js') ?>" defer></script>
</head>
<body>
<a class="skip-link" href="#contenu">Aller au contenu</a>

<header class="site-header">
    <div class="wrap site-header__inner">
        <a class="brand" href="<?= url('/') ?>" aria-label="<?= e($appName) ?>, accueil">
            VideGrenier<span class="brand__dot" aria-hidden="true">+</span>
        </a>

        <nav class="nav" aria-label="Navigation principale">
            <a class="nav__link" href="<?= url('objets') ?>" <?= is_active('objets') ? 'aria-current="page"' : '' ?>>Objets<span class="hide-sm"> à donner</span></a>

            <?php if ($user): ?>
                <a class="nav__link nav__link--account" href="<?= url('mes-annonces') ?>" <?= is_active('mes-annonces') ? 'aria-current="page"' : '' ?>>Mes annonces</a>
                <details class="account">
                    <summary class="account__toggle" aria-label="Mon compte">
                        <span class="avatar" aria-hidden="true"><?= e(initials($user['name'])) ?></span>
                    </summary>
                    <div class="account__menu">
                        <p class="account__who">
                            <strong><?= e($user['name']) ?></strong>
                            <span><?= e($user['email']) ?></span>
                        </p>
                        <a href="<?= url('mes-annonces') ?>">Mes annonces</a>
                        <a href="<?= url('mes-annonces/nouvelle') ?>">Donner un objet</a>
                        <a href="<?= url('profil') ?>">Mon profil</a>
                        <form method="post" action="<?= url('deconnexion') ?>">
                            <?= csrf_field() ?>
                            <button type="submit">Se déconnecter</button>
                        </form>
                    </div>
                </details>
            <?php else: ?>
                <a class="nav__link" href="<?= url('connexion') ?>" <?= is_active('connexion') ? 'aria-current="page"' : '' ?>>Se connecter</a>
            <?php endif; ?>

            <a class="btn btn--primary btn--sm nav__cta" href="<?= url('mes-annonces/nouvelle') ?>">Donner un objet</a>
        </nav>
    </div>
</header>

<main id="contenu">
    <?= \App\Core\App::get()->view()->partial('partials/flash') ?>
    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="wrap site-footer__inner">
        <div>
            <p class="brand brand--footer">VideGrenier<span class="brand__dot" aria-hidden="true">+</span></p>
            <p class="muted">Des objets qui changent de main plutôt que de finir à la poubelle. Gratuit, entre voisins de Kinshasa.</p>
        </div>
        <nav class="site-footer__links" aria-label="Liens du pied de page">
            <a href="<?= url('objets') ?>">Objets à donner</a>
            <a href="<?= url('mes-annonces/nouvelle') ?>">Donner un objet</a>
            <?php if (!$user): ?><a href="<?= url('inscription') ?>">Créer un compte</a><?php endif; ?>
        </nav>
        <p class="site-footer__credit muted">
            © <?= date('Y') ?> <?= e($appName) ?>. Conçu et développé par
            <a href="<?= e(\App\Core\App::get()->config('author.url')) ?>" rel="author"><?= e(\App\Core\App::get()->config('author.name')) ?></a>.
        </p>
    </div>
</footer>
</body>
</html>
