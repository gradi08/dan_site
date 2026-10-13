<?php $this->set('title', 'Se connecter'); ?>
<section class="auth wrap">
    <div class="auth__panel">
        <h1 class="page-title">Content de vous revoir</h1>
        <p class="muted">Connectez-vous pour contacter les donneurs et gérer vos annonces.</p>

        <form class="form" method="post" action="<?= url('connexion') ?>">
            <?= csrf_field() ?>

            <div class="field">
                <label for="email">E-mail</label>
                <input id="email" type="email" name="email" value="<?= e(old('email')) ?>" required autocomplete="email" autofocus <?= field_attrs('email') ?>>
                <?= field_error('email') ?>
            </div>

            <div class="field">
                <label for="password">Mot de passe</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" <?= field_attrs('password') ?>>
                <?= field_error('password') ?>
            </div>

            <button class="btn btn--primary btn--block" type="submit">Se connecter</button>
        </form>

        <p class="auth__switch">Pas encore de compte ? <a href="<?= url('inscription') ?>">Créer un compte</a></p>
    </div>
</section>
