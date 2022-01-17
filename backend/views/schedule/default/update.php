<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\subjectsect\SubjectSectSchedule */

$this->title = Yii::t('art', 'Update');
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('art/guide', 'Subject Sect Schedule'), 'url' => ['schedule/default/index']];
$this->params['breadcrumbs'][] = ['label' =>sprintf('#%06d', $model->id)];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-sect-schedule-update">
    <?= $this->render('_form', compact('model')) ?>
</div>