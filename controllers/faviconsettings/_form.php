<?php
$settingsModel = $this->formGetModel();
?>

<?= Form::open(['class' => 'layout design-settings']) ?>
    <div class="layout-row">
        <?= $this->formRender() ?>
    </div>

    <div class="form-buttons">
        <div data-control="loader-container">
            <?= Ui::ajaxButton(e(__('Save')), 'onSave')
                ->primary()
                ->ajaxData(['redirect' => false])
                ->hotkey('ctrl+s', 'cmd+s')
                ->loadingMessage(e(__('Saving...'))) ?>

            <?php if (! empty($settingsModel->api_key) && $settingsModel->master_picture) : ?>
                <?= Ui::ajaxButton(e(__('Generate favicon')), 'onGenerate')
                ->warning()
                ->loadingMessage(e(__('Generating...'))) ?>
            <?php endif; ?>

            <?php if (! empty($settingsModel->overlapping_markups)) : ?>
                <?= Ui::ajaxButton(e(__('Scan theme for overlapping tags')), 'onScanForOverlaps')
                ->danger()
                ->loadingMessage(e(__('Scanning...'))) ?>
            <?php endif; ?>

            <span class="btn-text">
                <span class="button-separator"><?= e(__('or')) ?></span>
                <?= Ui::button(e(__('Cancel')), 'system/settings')
                    ->textLink() ?>
            </span>
        </div>
    </div>
<?= Form::close() ?>