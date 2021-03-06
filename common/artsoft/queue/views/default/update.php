<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model artsoft\queue\models\QueueSchedule */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->title]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/queue', 'Queue Schedules'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['default/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="queue-schedule-update">
    <?= $this->render('_form', compact('model')) ?>
</div>