<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\schedule\ConsultSchedule */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Consult Schedules'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="consult-schedule-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>