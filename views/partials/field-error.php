<?php /** @var string $name */ ?>
<p class="field__error" id="<?= e($name) ?>-error" <?= error($name) ? '' : 'hidden' ?>>
    <?= e(error($name) ?? '') ?>
</p>
