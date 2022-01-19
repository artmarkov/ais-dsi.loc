<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\subjectsect\SubjectSchedule */

$this->title = Yii::t('art', 'Update');
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('art/guide', 'Subject Schedule'), 'url' => ['schedule/default/index']];
$this->params['breadcrumbs'][] = ['label' =>sprintf('#%06d', $model->id)];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-schedule-update">
    <?= $this->render('_form', [
        'model' => $model,
        'teachersLoadModel' => $teachersLoadModel,
    ]); ?>
</div>