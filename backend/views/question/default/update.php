<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\question\Question */
/* @var $modelsQuestionAttribute common\models\question\QuestionAttribute */
/* @var $modelsQuestionOptions common\models\question\QuestionOptions */
/* @var $readonly */

$this->title = Yii::t('art', 'Update "{item}"', ['item' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/question', 'Questions'), 'url' => ['question/default/index']];
$this->params['breadcrumbs'][] = sprintf('#%06d', $model->id)
?>
<div class="question-update">
    <?= $this->render('_form', [
        'model' => $model,
        'modelsQuestionAttribute' => $modelsQuestionAttribute,
        'modelsQuestionOptions' => $modelsQuestionOptions,
        'readonly' => $readonly
    ]) ?>
</div>