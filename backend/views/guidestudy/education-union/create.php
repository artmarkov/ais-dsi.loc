<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\guidestudy\EducationUnion */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Education Unions'), 'url' => ['guidestudy/education-union/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="education-union-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>