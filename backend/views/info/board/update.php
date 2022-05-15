<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\info\Board */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/info', 'Board'), 'url' => ['info/board/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="board-update">
    <?= $this->render('_form', compact('model')) ?>
</div>