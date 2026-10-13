<?php
/** @var array<string, mixed>|null $article */
use App\Support\Category;
use App\Support\Status;

$editing = $article !== null;
$this->set('title', $editing ? 'Modifier l\'annonce' : 'Donner un objet');

$value = static fn (string $key, string $default = '') => old($key, $article[$key] ?? $default);
$status = $value('status', Status::PUBLISHED);
$action = $editing ? url('mes-annonces/' . $article['id']) : url('mes-annonces');
$photo = $editing ? image_url($article['image']) : null;

$communes = ['Bandalungwa', 'Barumbu', 'Bumbu', 'Gombe', 'Kalamu', 'Kasa-Vubu', 'Kimbanseke', 'Kinshasa', 'Kintambo',
    'Kisenso', 'Lemba', 'Limete', 'Lingwala', 'Makala', 'Maluku', 'Masina', 'Matete', 'Mont-Ngafula', 'Ndjili',
    'Ngaba', 'Ngaliema', 'Ngiri-Ngiri', 'Nsele', 'Selembao'];
?>
<section class="wrap narrow">
    <div class="page-head">
        <a class="back" href="<?= url('mes-annonces') ?>">Retour à mes annonces</a>
        <h1 class="page-title"><?= $editing ? 'Modifier l\'annonce' : 'Donner un objet' ?></h1>
    </div>

    <form class="form form--panel" method="post" action="<?= $action ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="field">
            <span class="label" id="image-label">Photo</span>
            <label class="dropzone<?= $photo ? ' has-image' : '' ?>" for="image">
                <img class="dropzone__preview" src="<?= e($photo ?? '') ?>" alt="" <?= $photo ? '' : 'hidden' ?>>
                <span class="dropzone__text">
                    <strong><?= $photo ? 'Changer la photo' : 'Choisir une photo' ?></strong>
                    JPG, PNG ou WebP, 5 Mo maximum
                </span>
            </label>
            <input class="visually-hidden" id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" aria-labelledby="image-label" <?= $editing ? '' : 'required' ?> <?= field_attrs('image') ?>>
            <?= field_error('image') ?>
        </div>

        <div class="field">
            <label for="title">Titre</label>
            <input id="title" type="text" name="title" value="<?= e($value('title')) ?>" required minlength="3" maxlength="120" placeholder="Ex. Table basse en bois" <?= field_attrs('title') ?>>
            <?= field_error('title') ?>
        </div>

        <div class="field">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5" required minlength="10" maxlength="2000" data-counter placeholder="État, dimensions, défauts éventuels, disponibilités pour la remise…" <?= field_attrs('description') ?>><?= e($value('description')) ?></textarea>
            <p class="field__hint" data-counter-for="description"></p>
            <?= field_error('description') ?>
        </div>

        <div class="field-row">
            <div class="field">
                <label for="category">Catégorie</label>
                <select id="category" name="category" required <?= field_attrs('category') ?>>
                    <?php foreach (Category::ALL as $slug => $label): ?>
                        <option value="<?= e($slug) ?>" <?= $value('category', 'autre') === $slug ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
                <?= field_error('category') ?>
            </div>

            <div class="field">
                <label for="location">Commune <span class="optional">(facultatif)</span></label>
                <input id="location" type="text" name="location" value="<?= e($value('location')) ?>" maxlength="80" list="communes" autocomplete="off" <?= field_attrs('location') ?>>
                <datalist id="communes">
                    <?php foreach ($communes as $commune): ?>
                        <option value="<?= e($commune) ?>"></option>
                    <?php endforeach; ?>
                </datalist>
                <?= field_error('location') ?>
            </div>
        </div>

        <fieldset class="field choice">
            <legend>Visibilité</legend>
            <label class="choice__option">
                <input type="radio" name="status" value="<?= Status::PUBLISHED ?>" <?= $status === Status::PUBLISHED ? 'checked' : '' ?>>
                <span><strong>En ligne</strong> Visible par tout le monde.</span>
            </label>
            <label class="choice__option">
                <input type="radio" name="status" value="<?= Status::DRAFT ?>" <?= $status === Status::DRAFT ? 'checked' : '' ?>>
                <span><strong>Brouillon</strong> Visible par vous seul.</span>
            </label>
            <?php if ($editing && $article['status'] === Status::GIVEN): ?>
                <label class="choice__option">
                    <input type="radio" name="status" value="<?= Status::GIVEN ?>" <?= $status === Status::GIVEN ? 'checked' : '' ?>>
                    <span><strong>Donné</strong> Reste visible, sans bouton de contact.</span>
                </label>
            <?php endif; ?>
        </fieldset>

        <div class="form__actions">
            <a class="btn btn--ghost" href="<?= url('mes-annonces') ?>">Annuler</a>
            <button class="btn btn--primary" type="submit"><?= $editing ? 'Enregistrer les modifications' : 'Publier l\'annonce' ?></button>
        </div>
    </form>
</section>
