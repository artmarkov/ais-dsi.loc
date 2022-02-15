<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\LessonTest */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Lesson Tests'), 'url' => ['guidestudy/lesson-test/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="lesson-test-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>