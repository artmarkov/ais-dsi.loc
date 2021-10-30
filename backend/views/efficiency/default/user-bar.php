<?php

/* @var $this yii\web\View */
/* @var $id */
/* @var $timestamp_in */
/* @var $timestamp_out */

?>

<div class="teachers-efficiency-bar">
    <?= \common\widgets\EfficiencyUserBarWidget::widget(['id' => $id, 'timestamp_in' => $timestamp_in, 'timestamp_out' => $timestamp_out]) ?>
</div>
