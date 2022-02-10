<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\studyplan\StudyplanThematic */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/studyplan', 'Studyplan Thematics'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="studyplan-thematic-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>