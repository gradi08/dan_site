<?php $this->set('title', 'Créer un compte'); ?>
<section class="auth wrap">
    <div class="auth__panel">
        <h1 class="page-title">Créer un compte</h1>
        <p class="muted">Gratuit, sans publicité. Votre numéro sert uniquement au bouton WhatsApp de vos annonces.</p>

        <form class="form" method="post" action="<?= url('inscription') ?>">
            <?= csrf_field() ?>

            <div class="field">
                <label for="name">Nom ou prénom</label>
                <input id="name" type="text" name="name" value="<?= e(old('name')) ?>" required minlength="2" maxlength="100" autocomplete="name" <?= field_attrs('name') ?>>
                <?= field_error('name') ?>
            </div>

            <div class="field">
                <label for="email">E-mail</label>
                <input id="email" type="email" name="email" value="<?= e(old('email')) ?>" required maxlength="190" autocomplete="email" <?= field_attrs('email') ?>>
                <?= field_error('email') ?>
            </div>

            <div class="field">
                <label for="phone">Numéro WhatsApp</label>
                <input id="phone" type="tel" name="phone" value="<?= e(old('phone')) ?>" required autocomplete="tel" inputmode="tel" placeholder="0812345678" <?= field_attrs('phone') ?>>
                <p class="field__hint">Sans indicatif, +243 est ajouté automatiquement.</p>
                <?= field_error('phone') ?>
            </div>

            <div class="field">
                <label for="password">Mot de passe</label>
                <input id="password" type="password" name="password" required minlength="8" maxlength="72" autocomplete="new-password" <?= field_attrs('password') ?>>
                <p class="field__hint">8 caractères minimum.</p>
                <?= field_error('password') ?>
            </div>

            <div class="field">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" maxlength="72" autocomplete="new-password">
            </div>

            <button class="btn btn--primary btn--block" type="submit">Créer mon compte</button>
        </form>

        <p class="auth__switch">Déjà inscrit ? <a href="<?= url('connexion') ?>">Se connecter</a></p>
    </div>
</section>
