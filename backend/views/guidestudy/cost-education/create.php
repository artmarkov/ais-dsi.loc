<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\EducationLevel */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Cost Education'), 'url' => ['education/cost-education/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="cost-education-create">
    <?=  $this->render('_form', compact('model')) ?>
</div>