<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\question\Question */
/* @var $modelsQuestionAttribute common\models\question\QuestionAttribute */
/* @var $modelsQuestionOptions common\models\question\QuestionOptions */
/* @var $readonly */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/question', 'Questions'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="question-create">
    <?= $this->render('_form', [
        'model' => $model,
        'readonly' => $readonly
    ]) ?>
</div>