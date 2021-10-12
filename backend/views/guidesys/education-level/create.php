<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\EducationLevel */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Education Levels'), 'url' => ['education/education-level/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="education-level-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>