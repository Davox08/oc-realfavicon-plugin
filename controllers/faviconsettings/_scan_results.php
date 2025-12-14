<?php if (isset($scanResults)): ?>

    <?php if (empty($scanResults['findings'])): ?>

        <div class="callout callout-success no-subheader">
            <div class="header">
                <h3><?= __('Scan Complete: No Conflicts Found') ?></h3>
                <p><?= __('Your active theme files appear to be clean of any conflicting favicon tags.') ?></p>
            </div>
        </div>

    <?php else: ?>

        <div class="callout callout-warning no-subheader">
            <div class="header">
                <h3><?= __('Warning: Potential Conflicts Found!') ?></h3>
                <p><?= __('The following tags were found in your theme files. It is recommended to remove them manually to avoid duplicate tags.') ?></p>
            </div>
        </div>

        <div class="control-list" data-control="listwidget">
            <table class="table data">
                <thead>
                    <tr>
                        <th class="list-cell-name"><span><?= __('File') ?></span></th>
                        <th class="list-cell-name"><span><?= __('Line') ?></span></th>
                        <th class="list-cell-name"><span><?= __('Conflicting Tag Found') ?></span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($scanResults['findings'] as $finding): ?>
                        <tr>
                            <td class="list-cell-name">
                                <?= e($finding['file']) ?>
                            </td>
                            <td class="list-cell-name">
                                <?= e($finding['line']) ?>
                            </td>
                            <td class="list-cell-name">
                                <code><?= e($finding['tag']) ?></code>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>

    <?php endif ?>

<?php endif ?>