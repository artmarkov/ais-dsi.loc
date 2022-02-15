<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\LessonItems */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Lesson Items'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['default/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lesson-items-update">
    <?= $this->render('_form', compact('model')) ?>
</div>