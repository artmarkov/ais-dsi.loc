<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\info\Board */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/info', 'Board'), 'url' => ['info/board/index']];
$this->params['breadcrumbs'][] = ['label' => sprintf('#%06d', $model->id), 'url' => ['info/board/update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('art', 'Update');
?>
<div class="board-update">
    <?= $this->render('_form', compact('model')) ?>
</div>