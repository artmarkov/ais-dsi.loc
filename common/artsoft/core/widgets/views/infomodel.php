<?php if (!$model->isNewRecord): ?>
    <div class="info-model text-default text-muted">
        <?php if (isset($model->id)): ?>
            <span><strong>#</strong><?= $model->id ?></span>
        <?php endif; ?>
        <?php if (isset($model->createdDatetime)): ?>
            <span><strong><?= $model->attributeLabels()['created_at'] ?? \Yii::t('art', 'Created') ?></strong> : <?= $model->createdDatetime ?>
                <?= $model->createdBy->username ?? '' ?></span>
        <?php endif; ?>
        <?php if (isset($model->updatedDatetime)): ?>
            <span><strong><?= $model->attributeLabels()['updated_at'] ?? \Yii::t('art', 'Updated') ?></strong> : <?= $model->updatedDatetime ?>
                <?= $model->updatedBy->username ?? '' ?></span>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php
$css = <<<CSS
.info-model {
	    font-size: 0.8em;
}
CSS;

$this->registerCss($css);
?>