<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model artsoft\queue\models\QueueSchedule */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/queue', 'Queue Schedules'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="queue-schedule-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>