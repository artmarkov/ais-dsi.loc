<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\LessonTest */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => sprintf('#%06d', $model->id)]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Lesson Tests'), 'url' => ['guidestudy/lesson-test/index']];
$this->params['breadcrumbs'][] = Yii::t('art', 'Update');
?>
<div class="lesson-test-update">
    <?= $this->render('_form', compact('model')) ?>
</div>