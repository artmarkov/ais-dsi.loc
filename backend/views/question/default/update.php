<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\question\Question */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/question', 'Questions'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['default/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('art', 'Update');
?>
<div class="question-update">
    <?= $this->render('_form', compact('model')) ?>
</div>