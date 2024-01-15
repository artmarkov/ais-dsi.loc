<div class="panel panel-default">
    <div class="panel-heading">Сейчас в системе: <span class="small">основано на данных за последние 3 часа</span></div>
    <div class="panel-body">
        <?php if (count($active)): ?>
            <div class="clearfix">
                <?= '<b>Преподавателей и сотрудников (' . count($active) . '):</b> '?>
                <?= implode(', ', $active)?>
            </div>
        <?php else: ?>
            <h5><em><?= Yii::t('art/user', 'No users found.') ?></em></h5>
        <?php endif; ?>

    </div>
</div>
