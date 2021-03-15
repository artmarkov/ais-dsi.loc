<?php

/* @var $this yii\web\View */
/* @var $model artsoft\eav\models\EavAttribute; */
?>

<div class="attribute-id" data-attribute-id="<?= $model->id ?>">
    <?= $model->getIcon() ?>
    <?= $model->id ?></div>
<div><span><?= $model->eavType->name ?></span><b><?= $model->label ?></b></div>
