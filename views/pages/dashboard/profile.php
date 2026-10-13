<?php
/** @var array<string, mixed> $user */
$this->set('title', 'Mon profil');
?>
<section class="wrap narrow">
    <div class="page-head">
        <h1 class="page-title">Mon profil</h1>
        <p class="muted">Membre depuis le <?= e(date('d/m/Y', strtotime($user['created_at']))) ?>.</p>
    </div>

    <form class="form form--panel" method="post" action="<?= url('profil') ?>">
        <?= csrf_field() ?>
        <h2 class="form__title">Coordonnées</h2>

        <div class="field">
            <label for="name">Nom ou prénom</label>
            <input id="name" type="text" name="name" value="<?= e(old('name', $user['name'])) ?>" required minlength="2" maxlength="100" autocomplete="name" <?= field_attrs('name') ?>>
            <?= field_error('name') ?>
        </div>

        <div class="field">
            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" value="<?= e(old('email', $user['email'])) ?>" required maxlength="190" autocomplete="email" <?= field_attrs('email') ?>>
            <?= field_error('email') ?>
        </div>

        <div class="field">
            <label for="phone">Numéro WhatsApp</label>
            <input id="phone" type="tel" name="phone" value="<?= e(old('phone', $user['phone'])) ?>" required autocomplete="tel" inputmode="tel" <?= field_attrs('phone') ?>>
            <p class="field__hint">Les personnes intéressées par vos objets vous écriront à ce numéro.</p>
            <?= field_error('phone') ?>
        </div>

        <div class="form__actions">
            <button class="btn btn--primary" type="submit">Enregistrer</button>
        </div>
    </form>

    <form class="form form--panel" method="post" action="<?= url('profil/mot-de-passe') ?>">
        <?= csrf_field() ?>
        <h2 class="form__title">Mot de passe</h2>

        <div class="field">
            <label for="current_password">Mot de passe actuel</label>
            <input id="current_password" type="password" name="current_password" required autocomplete="current-password" <?= field_attrs('current_password') ?>>
            <?= field_error('current_password') ?>
        </div>

        <div class="field-row">
            <div class="field">
                <label for="password">Nouveau mot de passe</label>
                <input id="password" type="password" name="password" required minlength="8" maxlength="72" autocomplete="new-password" <?= field_attrs('password') ?>>
                <?= field_error('password') ?>
            </div>
            <div class="field">
                <label for="password_confirmation">Confirmer</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" maxlength="72" autocomplete="new-password">
            </div>
        </div>

        <div class="form__actions">
            <button class="btn btn--primary" type="submit">Changer le mot de passe</button>
        </div>
    </form>
</section>
