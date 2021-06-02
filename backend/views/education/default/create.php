<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\EducationProgramm */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/education', 'Education Programms'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="education-programm-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>