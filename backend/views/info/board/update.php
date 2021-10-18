<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\info\Board */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/info', 'Board'), 'url' => ['info/board/index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['default/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="board-update">
    <?= $this->render('_form', compact('model')) ?>
</div>