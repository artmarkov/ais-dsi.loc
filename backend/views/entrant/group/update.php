<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\entrant\EntrantGroup */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Entrant Groups'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['default/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('art', 'Update');
?>
<div class="entrant-group-update">
    <?= $this->render('_form', compact('model')) ?>
</div>