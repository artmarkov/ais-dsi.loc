<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\EducationSpeciality */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/education', 'Education Specialities'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="education-speciality-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>