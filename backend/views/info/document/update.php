<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\info\Document */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Documents'), 'url' => ['info/document/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="document-update">
    <?= $this->render('_form', compact('model', 'readonly')) ?>
</div>