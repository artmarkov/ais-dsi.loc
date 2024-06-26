<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\LessonMark */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => sprintf('#%06d', $model->id)]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Lesson Marks'), 'url' => ['guidestudy/lesson-mark/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id);
?>
<div class="lesson-mark-update">
    <?= $this->render('_form', compact('model')) ?>
</div>