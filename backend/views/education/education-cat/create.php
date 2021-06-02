<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\education\EducationCat */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Education Cats'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="education-cat-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>