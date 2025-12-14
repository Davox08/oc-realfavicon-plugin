<?php
/**
 * Partial for displaying the favicon preview image on the settings page.
 */
?>
<?php if ($model->preview_image): ?>
    <div class="form-group">
        <label><?= e(__('Favicon Preview')) ?></label>
        <p class="form-text"><?= e(__('A preview of how your favicon looks on different platforms.')) ?></p>
        <div class="favicon-preview-container" style="background-color: #f5f5f5; border: 1px solid #ddd; padding: 15px; text-align: center; margin-top: 5px;">
            <img src="<?= $model->preview_image->getPath() ?>"
                alt="Favicon Preview"
                style="max-width: 100%; height: auto;" />
        </div>
    </div>
<?php endif; ?>