<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\subjectsect\SubjectSchedule */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('art/guide', 'Subject Schedule'), 'url' => ['schedule/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="subject-schedule-create">
    <?=  $this->render('_form', [
        'model' => $model,
        'teachersLoadModel' => $teachersLoadModel,
    ]); ?>
</div>