<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\question\Question */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/question', 'Questions'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="question-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>