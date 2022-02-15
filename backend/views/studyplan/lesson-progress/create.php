<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\LessonProgress */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Lesson Progresses'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="lesson-progress-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>