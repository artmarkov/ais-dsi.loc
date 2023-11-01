<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\schoolplan\SchoolplanPerform */

$this->title = Yii::t('art', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('art/guide', 'Schoolplan Performs'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="schoolplan-perform-create">    
    <?=  $this->render('_form', compact('model')) ?>
</div>