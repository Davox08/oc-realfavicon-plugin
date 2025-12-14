<?php
/**
 * View for the Favicon Settings page.
 */
?>
<?php if (! $this->fatalError) : ?>

    <div id="settings-form-container">
        <?= $this->makePartial('form') ?>
    </div>

<?php else : ?>
    <p class="flash-message static error"><?= e(__($this->fatalError)) ?></p>
    <p><a href="<?= $parentLink ?>" class="btn btn-default"><?= e(__('Return to System Settings')) ?></a></p>
<?php endif ?>