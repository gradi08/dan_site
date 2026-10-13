<?php
/**
 * @var array<string, mixed>|null $article
 * @var list<array{id: int, filename: string, position: int}> $images
 */
use App\Repository\ArticleRepository;
use App\Support\Category;
use App\Support\Status;

$editing = $article !== null;
$this->set('title', $editing ? 'Modifier l\'annonce' : 'Donner un objet');

$value = static fn (string $key, string $default = '') => old($key, $article[$key] ?? $default);
$status = $value('status', Status::PUBLISHED);
$action = $editing ? url('mes-annonces/' . $article['id']) : url('mes-annonces');
$max = ArticleRepository::MAX_IMAGES;

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

        <fieldset class="field photos" data-max="<?= $max ?>" data-existing="<?= count($images) ?>">
            <legend>Photos <span class="optional">(1 obligatoire, <?= $max ?> maximum)</span></legend>
            <p class="field__hint">Les annonces avec 3 photos partent plus vite : vue d'ensemble, détail, défaut éventuel.</p>

            <?php if ($images): ?>
                <ul class="photo-grid" role="list">
                    <?php foreach ($images as $image): ?>
                        <li class="photo-tile">
                            <img src="<?= e(image_url($image['filename'])) ?>" alt="" width="200" height="150" loading="lazy">
                            <label class="photo-tile__cover">
                                <input type="radio" name="cover" value="<?= $image['id'] ?>" <?= $image['position'] === 0 ? 'checked' : '' ?>>
                                Photo principale
                            </label>
                            <label class="photo-tile__remove">
                                <input type="checkbox" name="remove[]" value="<?= $image['id'] ?>" data-remove-photo>
                                Retirer
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <ul class="photo-grid photo-grid--new" role="list" data-previews hidden></ul>

            <label class="dropzone" for="images">
                <span class="dropzone__text">
                    <strong data-dropzone-label><?= $images ? 'Ajouter des photos' : 'Choisir des photos' ?></strong>
                    <span data-dropzone-count>JPG, PNG ou WebP, 5 Mo maximum par photo</span>
                </span>
            </label>
            <input class="visually-hidden" id="images" type="file" name="images[]" multiple
                   accept="image/jpeg,image/png,image/webp" <?= $images ? '' : 'required' ?> <?= field_attrs('images') ?>>
            <?= field_error('images') ?>
        </fieldset>

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
